<?php


namespace App\DataProvider;


use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Agence;
use App\Entity\UserAgence;
use App\Entity\Utilisateur;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UtilisateurCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private  $utilisateurRepository;
    private  $agenceRepository;
    public function __construct(UtilisateurRepository $utilisateurRepository, AgenceRepository $agenceRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->agenceRepository = $agenceRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Utilisateur::class === $resourceClass;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): JsonResponse
    {
        if($operationName === "getusers"){
            $data = [];
            $users = $this->utilisateurRepository->findAll();
            $i = 0;
            foreach($users as $key => $user ){

                if($user->getAgence() == null){

                    $data[$i]['id'] = $user->getId();
                    $data[$i]['nom'] = $user->getNomComplet();
                    $i++;
                }
            }

            return new JsonResponse(['data'=>$data]);
        }else{
            $users = $this->utilisateurRepository->findAll();
            $data = array();
            $i = 0;
            foreach($users as $key => $user ){
                if($user->getStatus() == false){
                    $data[$i]['id'] = $user->getId();
                    $data[$i]['nom'] = ucfirst(strtolower($user->getNomComplet()));
                    $data[$i]['telephone'] = $user->getTelephone();
                    $data[$i]['email'] = ucfirst(strtolower($user->getEmail()));
                    $data[$i]['agence'] =ucfirst(strtolower( $user->getAgence()->getNomAgence()));
                    $type =" ";
                    if($user->getProfil()->getLibelle() === "AdminSysteme"){
                        $data[$i]['visible'] = false;
                    }else{
                        $data[$i]['visible'] = true;
                    }
                    if($user->getProfil()->getLibelle() === "AdminSysteme"){
                            $type= "Admin Systeme";
                    }else if($user->getProfil()->getLibelle() === "Caissier"){
                        $type= "Caissier";
                    }else if($user->getProfil()->getLibelle() === "AdminAgence"){
                        $type= "Admin Agence";
                    }else if($user->getProfil()->getLibelle() === "UserAgence"){
                        $type= "User Agence";
                    }
                    $data[$i]['type'] = $type;
                    $i++;
                }
                
            }
            return new JsonResponse(['data'=>$data],200);
        }


       // gné kanolé
    }


}