<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Repository\CaissierRepository;
use App\Repository\CompteRepository;
use App\Services\GenererNum;
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
     * @var GenererNum
     */
    private GenererNum $generator;

    /**
     * CompteController constructor.
     * @param CompteRepository $compteRepository
     * @param GenererNum $generator
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     */
    public function __construct(CompteRepository $compteRepository, GenererNum $generator,TokenStorageInterface $tokenStorage,EntityManagerInterface $manager)
    {
        $this->compteRepository = $compteRepository;
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
        $this->generator = $generator;
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
            if($infos['solde']> 0){
                $compte->setSolde($compte->getSolde() +$infos['solde']);
            }else{
                return new JsonResponse("le montant doit etre superieiur à 0",400,[],true);
            }

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
         $data->setNumCompte($this->generator->genrecode("CMPT",'compte'));
         $data->setSolde($infos['solde']);
         $data->setStatus(false);
         $data->setAdminSysteme($adminSysteme);
            $this->manager->persist($data);
            $this->manager->flush();
        return new JsonResponse("Le compte a été creé avec succé",200,[],true);
    }



}
