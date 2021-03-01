<?php


namespace App\DataPersiter;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Depot;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


final class DepotDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager=$manager;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
    }

    public function persist($data, array $context = []): object
    {
        dd($data);
        // call your persistence layer to save $data
        return $data;
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