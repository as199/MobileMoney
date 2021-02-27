<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Repository\CaissierRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompteController extends AbstractController
{
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;

    /**
     * CompteController constructor.
     * @param CompteRepository $compteRepository
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     */
    public function __construct(CompteRepository $compteRepository,TokenStorageInterface $tokenStorage,EntityManagerInterface $manager)
    {
        $this->compteRepository = $compteRepository;
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/api/adminSys/comptes/{id}", name="updateCompte",methods={"PUT"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function UpdateCompte($id,Request $request): JsonResponse
    {
        $infos = json_decode($request->getContent(),true);
        $compte = $this->compteRepository->find($id);
        $user = $this->tokenStorage->getToken()->getUser();
        if ($user->getRoles() === 'ROLE_Caissier'){
            $compte->setSolde($compte->getSolde() +$infos['solde']);
        }

        $this->manager->persist($compte);
        $this->manager->flush();
        return new JsonResponse("Le compte a été approvisionné avec succé",200,[],true);


    }

    public function AddCompte(Request $request,CaissierRepository $caissierRepository): JsonResponse
    {
        $data = new Compte();
        $infos = json_decode($request->getContent(),true);
        $adminSysteme = $this->tokenStorage->getToken()->getUser();
        if(isset($infos['caissier'])){
            $caissier = $caissierRepository->findOneBy(['id'=>$infos["caissier"]]);
           $data->addCaissier($caissier);
        }
         $data->setNumCompte($infos['numCompte']);
         $data->setSolde($infos['solde']);
         $data->setStatus(false);
         $data->setAdminSysteme($adminSysteme);
            $this->manager->persist($data);
            $this->manager->flush();
        return new JsonResponse("Le compte a été creé avec succé",200,[],true);
    }
}
