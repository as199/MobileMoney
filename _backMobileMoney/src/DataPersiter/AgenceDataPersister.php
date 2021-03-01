<?php


namespace App\DataPersiter;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Agence;
use App\Entity\Compte;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class AgenceDataPersister implements ContextAwareDataPersisterInterface
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


    public function __construct(EntityManagerInterface $manager,TokenStorageInterface $tokenStorage)
    {
        $this->manager=$manager;
        $this->tokenStorage = $tokenStorage;
        

    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Agence;
    }

    public function persist($data, array $context = []): object
    {


    }

    public function remove($data, array $context = []): JsonResponse
    {
        dd($data);
        $data->setStatus(true);
        $this->manager->persist($data);
        $this->manager->flush();
        return new JsonResponse("Archivage successfully!",200,[],true);
        
        // call your persistence layer to delete $data
    }
}