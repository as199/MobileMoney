<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Transaction;
use App\Services\CalculFraisService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * TransactionController constructor.
     *
     * @param EntityManagerInterface $manager
     * @param CalculFraisService $calculFraisService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $manager,CalculFraisService $calculFraisService,
                                TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
        $this->manager = $manager;
        $this->calculFraisService = $calculFraisService;
    }

    /**
     * @Route("/api/transactions", name="addTransaction", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function AddCompte(Request $request): Response
    {
        $data =  json_decode($request->getContent(),true);
        $user = $this->tokenStorage->getToken()->getUser();
        $client = new Client();
        $transaction = new Transaction();
        $totalFrais = $this->calculFraisService->CalcFrais($data['montant']);
        $tarif = $this->calculFraisService->CalcPart($totalFrais);
        $client->setNomComplet($data['client']['nomComplet']);
        $client->setTelephone($data['client']['telephone']);
        $client->setCni($data['client']['cni']);
        $client->setStatus(false);
        $client->setAdresse($data['client']['adresse']);
        $this->manager->persist($client);

        $transaction->setMontant($data['montant']);
        $transaction->setTotalCommission($totalFrais);
        $transaction->setCommissionDepot($tarif['Depot']);
        $transaction->setCommissionEtat($tarif['etat']);
        $transaction->setCommissionRetrait($tarif['Retrait']);
        $transaction->setCommissionTransfere($tarif['transfert']);
        $transaction->setDateEnvoi(new DateTime('now'));
        $transaction->addUtilisateur($user);
        $transaction->addClient($client);
        $transaction->setType($data['type']);
        $transaction->setStatus(false);
//        $transaction = $this->calculFraisService->faireTransaction($request);
        $this->manager->persist($transaction);
        $this->manager->flush();

    }
}
