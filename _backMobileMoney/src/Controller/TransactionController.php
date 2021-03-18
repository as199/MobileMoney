<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
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
use Symfony\Component\Serializer\Exception\ExceptionInterface;
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
     * @var ClientRepository
     */
    private ClientRepository $clientRepository;

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
     * @param ClientRepository $clientRepository
     */
    public function __construct(EntityManagerInterface $manager,CalculFraisService $calculFraisService,
                                TokenStorageInterface $tokenStorage,
                                GenererNum $generator,
                                TransactionRepository $transactionRepository,
                                CompteRepository $compteRepository, AgenceRepository $agenceRepository,
                                SerializerInterface $serializer,ClientRepository $clientRepository
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
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/api/transactions", name="addTransaction", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function AddTransaction(Request $request): JsonResponse
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
            if($resp = $this->clientRepository->findOneBy(['telephone'=>$data['clientenvoi']['telephone']])){
                $clientEnvoi = $resp;
            }else{
                $clientEnvoi = $this->serializer->denormalize($data['clientenvoi'], Client::class);
                $clientEnvoi->setStatus(false);
            }
            if($resp = $this->clientRepository->findOneBy(['telephone'=>$data['clientRetrait']['telephone']])){
                $clientRetrait = $resp;
            }else{
                $clientRetrait = $this->serializer->denormalize($data['clientRetrait'], Client::class);
                $clientRetrait->setStatus(false);
            }

         $totalFrais = $this->calculFraisService->CalcFrais($data['montant']);
        $tarif = $this->calculFraisService->CalcPart($totalFrais);

        $this->manager->persist($clientEnvoi);
        $this->manager->persist($clientRetrait);

        $transaction->setNumero($this->generator->genrecode(0, 'transaction'));
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
        $compte->setSolde($compte->getSolde() - $data['montant'] + $tarif['Depot']);
    }elseif ($data['type'] === 'retrait'){
            $transaction = $this->transactionRepository->findOneBy(['numero'=>$data['code']]);
            
            if ($transaction->getDateRetrait() === null ){
                if ($transaction->getDateAnnulation()=== null){
                        $compte = $this->compteRepository->findOneBy(['id'=>$transaction->getCompte()->getId()]);
                        $compte->setSolde($compte->getSolde() + $transaction->getMontant()+$transaction->getTotalCommission());
                        $transaction->setDateRetrait(new \DateTime('now'));
                        $transaction->setUserRetrait($user);
                        if(isset($data['cni'])){
                            $client = $this->clientRepository->findOneBy(['id'=>$transaction->getClientRecepteur()->getId()]);
                            $client->setCni($data['cni']);
                            $this->manager->persist($client);
                         }
                        
                        $this->manager->persist($transaction);
                        $this->manager->flush();
                        return $this->json(['message' => 'Ce transfert a  été retirer avec succè ', 'data'=>$transaction],200);

                }else{
                    return $this->json(['message' => "Ce transfert a été annulé"], 400);
                }

            }else{
                return $this->json(['message' => "Ce transfert a déja été retirer "], 400);
            }

    }



        $this->manager->persist($transaction);
        $this->manager->flush();
        return $this->json(['data'=>$this->getLastTRansaction()],200);

    }

    public function GetTransactionByNum(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $result = array();
        $transaction = $this->transactionRepository->findOneBy(['numero'=>$data['code']]);
        $clientEnvoi = $this->clientRepository->findOneBy(['id'=>$transaction->getClientEnvoi()->getId()]);
        $clientRecepteur = $this->clientRepository->findOneBy(['id'=>$transaction->getClientRecepteur()->getId()]);
        $result["clientEnvoi"] = $clientEnvoi;
        $result["clientRecepteur"] = $clientRecepteur;
        $result["montant"] = $transaction->getMontant();
        $result["dateEnvoi"] = $transaction->getDateEnvoi();
        return $this->json(['data'=>$result],200);
    }

    public function DeleteTransaction(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($transaction = $this->transactionRepository->findOneBy(['numero' => $data['numero']])) {
            $idAgenceEnvoi = $transaction->getUserEnvoi()->getAgence()->getId();
            $idAgenceAnnulation = $this->tokenStorage->getToken()->getUser()->getAgence()->getId();
            if ($idAgenceAnnulation === $idAgenceEnvoi) {
                if ($transaction->getDateRetrait() === null) {
                    if ($transaction->getDateAnnulation() === null) {
                        $transaction->setDateAnnulation(new \DateTime);
                        $compte = $transaction->getCompte();
                        $compte->setSolde($compte->getSolde() + $transaction->getMontant());
                        $this->manager->persist($transaction);
                        $this->manager->flush();
                        return new JsonResponse(" transaction annulle  avec succée", 200, [], true);

                    } else {
                        return $this->json(['message' => "Impossible d'annuler le depot car celle ci a deja ete annuler"], 400);
                    }

                } else {
                    return $this->json(['message' => "Impossible d'annuler le depot car l,argent a été déja retirer"], 400);
                }
            } else {
                return $this->json(['message' => "Impossible d'annuler le depot car la transaction n'as pas été effectuer dans cette agence"], 400);
            }
        } else {
            return $this->json(['message' => "Impossible d'annuler le depot car la transaction n'existe pas"], 400);
        }
    }

    public function Calculatrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        if($data['montant'] > 0){
         $montant = $this->calculFraisService->CalcFrais($data['montant']);
            return $this->json(['message' => 'le montant', 'data'=>$montant]);
        }else{
            return $this->json(['message' => 'le montant doit etre superieur à 0 '],500);

        }

    }

    public function DeCalculatrice(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        if($data['total'] > 0){
            $result = $this->calculFraisService->DeCalcFrais($data['total']);
            return $this->json(['message' => 'le montant', 'data'=>$result]);
        }else{
            return $this->json(['message' => 'le total doit etre superieur à 0 '],500);

        }

    }

    public function GetSolde(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $solde = 0;
        if ($id = $user->getAgence()->getId()){
           $agence = $this->agenceRepository->findOneBy(['id' => $id]);
           $compteId = $agence->getCompte()->getId();
           $compte = $this->compteRepository->findOneBy(['id' => $compteId]);
           $solde = $compte->getSolde();
        }
        return $this->json(['data'=>$solde]);
    }


    private function getLastTRansaction(): array
    {
        $data= array();
        $transaction = $this->transactionRepository->findBy([], ['id'=>'DESC'])[0];
        $clientEnvoi = $transaction->getClientEnvoi();
        $clientRetrait = $transaction->getClientRecepteur();
        $data['montant'] = $transaction->getMontant();
        $data['code1'] =$transaction->getNumero();
        $data['code'] = $this->code($transaction->getNumero());
        $data['dateEnvoi'] = $transaction->getDateEnvoi()->format('Y-m-d');
        $data['clientEnvoi']['telephone'] = $clientEnvoi->getTelephone();
        $data['clientEnvoi']['nom'] = $clientEnvoi->getPrenom().' '.$clientEnvoi->getNom();
        $data['clientRetrait']['telephone'] = $clientEnvoi->getTelephone();
        $data['clientRetrait']['nom'] = $clientRetrait->getPrenom().' '.$clientRetrait->getNom();
        return $data;
    }
// (334) 781-4853 

    private function code($str): string
    {
        if(strlen($str) == 9) {
        $res = substr($str, 0, 3) .'-';
        $res .= substr($str, 3, 3) .'-';
        $res .= substr($str, 6, 9) ;

        }
        return $res;
    }



}
