<?php


namespace App\DataPersiter;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Compte;
use App\Entity\Transaction;
use App\Services\CalculFraisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class TransactionDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var UserInterface
     */
    private UserInterface $user;
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var CalculFraisService
     */
    private CalculFraisService $fraisService;


    public function __construct(EntityManagerInterface $manager,TokenStorageInterface $tokenStorage, CalculFraisService $fraisService)
    {
        $this->manager=$manager;
        $this->tokenStorage = $tokenStorage;
        $this->fraisService = $fraisService;


    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Transaction;
    }

    public function persist($data, array $context = []): object
    {


       $totalFrais = $this->fraisService->CalcFrais($data->getMontant());
        //dd($totalFrais);
        $tarif = $this->fraisService->CalcPart($totalFrais);
        dd($tarif);
        $data->setTotalCommission($totalFrais);
        $data->setCommissionEtat($tarif['etat']);
        dd($data);


    }

    public function remove($data, array $context = []): JsonResponse
    {
        $data->setStatus(true);
        $this->manager->persist($data);
        $this->manager->flush();
        return new JsonResponse("Archivage successfully!",200,[],true);
        
        // call your persistence layer to delete $data
    }
}