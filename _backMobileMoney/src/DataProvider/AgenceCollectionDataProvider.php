<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Agence;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class AgenceCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;

    /**
     * TransactionCollectionDataProvider constructor.
     * @param AgenceRepository $agenceRepository
     * @param CompteRepository $compteRepository
     */
    public function __construct(AgenceRepository  $agenceRepository, CompteRepository $compteRepository)
    {
        $this->agenceRepository = $agenceRepository;
        $this->compteRepository = $compteRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Agence::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JSONResponse
    {
        $agences = $this->agenceRepository->findBy([], ['id'=>'DESC']);
        $data = array();
        foreach ($agences as $key => $agence){
            
            if($agence->getStatus() == false){
            $data[$key]["nom"] = $agence->getNomAgence();
            $data[$key]["id"] = $agence->getId();
            if($compte = $this->compteRepository->findOneBy(['id'=>$agence->getCompte()->getId()])){
                $data[$key]["numeroCompte"]= $compte->getNumCompte();
                $data[$key]["solde"]= $compte->getSolde();
                $data[$key]["adresse"]= $agence->getAdresse();
            }
        }
        }
        return new JSONResponse($data);

       // gné kanolé
    }


}
