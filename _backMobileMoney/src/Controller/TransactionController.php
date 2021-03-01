<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\TransactionRepository;
use App\Services\CalculFraisService;
use App\Services\GenererNum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class TransactionController extends AbstractController
{
    /**
     * @var CalculFraisService
     */
    private CalculFraisService $calculFraisService;
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var GenererNum
     */
    private GenererNum $generator;
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var TransactionRepository
     */
    private TransactionRepository $transactionRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * TransactionController constructor.
     *
     * @param EntityManagerInterface $manager
     * @param CalculFraisService $calculFraisService
     * @param TokenStorageInterface $tokenStorage
     * @param GenererNum $generator
     * @param TransactionRepository $transactionRepository
     * @param CompteRepository $compteRepository
     * @param AgenceRepository $agenceRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(EntityManagerInterface $manager,CalculFraisService $calculFraisService,
                                TokenStorageInterface $tokenStorage,
                                GenererNum $generator,
                                TransactionRepository $transactionRepository,
                                CompteRepository $compteRepository, AgenceRepository $agenceRepository,
                                SerializerInterface $serializer
    )
    {

        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->generator = $generator;
        $this->agenceRepository = $agenceRepository;
        $this->calculFraisService = $calculFraisService;
        $this->compteRepository = $compteRepository;
        $this->transactionRepository = $transactionRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/transactions", name="addTransaction", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function AddTransaction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->tokenStorage->getToken()->getUser();



        $transaction = new Transaction();
        if ($data['type'] === 'depot') {
            $agence = $this->agenceRepository->findOneBy(['id' => $user->getAgence()->getId()]);
            $compte = $this->compteRepository->findOneBy(['id' => $agence->getCompte()->getId()]);
            if($data['montant'] > $compte->getSolde()){
                return new JsonResponse("Impossible d'effectuer le depot", 400, [], true);
            }
         $clientEnvoi = $this->serializer->denormalize($data['clientenvoi'], Client::class);
         $clientEnvoi->setStatus(false);
         $clientRetrait = $this->serializer->denormalize($data['clientRetrait'], Client::class);
         $clientRetrait->setStatus(false);

         $totalFrais = $this->calculFraisService->CalcFrais($data['montant']);
        $tarif = $this->calculFraisService->CalcPart($totalFrais);

        $this->manager->persist($clientEnvoi);
        $this->manager->persist($clientRetrait);

        $transaction->setNumero($this->generator->genrecode("TR", 'transaction'));
        $transaction->setMontant($data['montant']);
        $transaction->setTotalCommission($totalFrais);
        $transaction->setCommissionDepot($tarif['Depot']);
        $transaction->setCommissionEtat($tarif['etat']);
        $transaction->setCommissionRetrait($tarif['Retrait']);
        $transaction->setCommissionTransfere($tarif['transfert']);
        $transaction->setDateEnvoi(new DateTime('now'));
        $transaction->setUserEnvoi($user);
        $transaction->setClientRecepteur($clientRetrait);
        $transaction->setClientEnvoi($clientEnvoi);
        $transaction->setType($data['type']);
        $transaction->setCompte($compte);
        $transaction->setStatus(false);
        $compte->setSolde($compte->getSolde() - $data['montant'] );
    }elseif ($data['type'] === 'retrait'){
            $transaction = $this->transactionRepository->findOneBy(['numero'=>$data['numero']]);

            if ($transaction->getDateRetrait() === null){
                if ($transaction->getClientRecepteur()->getTelephone() === $data['client']['telephone']){
                    $compte = $this->compteRepository->findOneBy(['id'=>$transaction->getCompte()->getId()]);
                        $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                        $transaction->setDateRetrait(new \DateTime('now'));
                        $transaction->setUserRetrait($user);
                        $this->manager->persist($transaction);
                        $this->manager->flush();
                        return new JsonResponse("Ce transfert a  été retirer avec succè ", 200, [], true);

                }else{
                    return new JsonResponse("Ce transfert n'est pas destiner à ce numero de telephone ", 400, [], true);
                }
            }else{
                return new JsonResponse("Ce transfert a déja été retirer ", 400, [], true);
            }

    }
        $this->manager->persist($transaction);
        $this->manager->flush();
        return new JsonResponse($transaction, 200, [], true);

    }

    public function DeleteTransaction($id,Request $request): JsonResponse
    {
        $data =  json_decode($request->getContent(),true);
        $transaction = $this->transactionRepository->findOneBy(['id'=>$id]);
      if(isset($data['type']) && $data['type']==='annuler'){

          $transaction->setDateAnnulation(new \DateTime);

      }else{
          $transaction->setStatus(true);
      }
        $this->manager->persist($transaction);
        $this->manager->flush();
        return new JsonResponse("success", 200, [], true);
    }
}
