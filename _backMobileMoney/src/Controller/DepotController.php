<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DepotController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $tokenStorage;
    /**
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * DepotController constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param CompteRepository $compteRepository
     * @param AgenceRepository $agenceRepository
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $manager
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                CompteRepository $compteRepository,
                                AgenceRepository $agenceRepository,
                                SerializerInterface $serializer,EntityManagerInterface $manager
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->compteRepository = $compteRepository;
        $this->agenceRepository = $agenceRepository;
        $this->serializer = $serializer;
        $this->manager = $manager;

    }

    public function ADDdepot(Request $request): Response
    {
        $infos = json_decode($request->getContent(),true);
        $depot = $this->serializer->denormalize($infos, Depot::class);
        $user = $this->tokenStorage->getToken()->getUser();

        if(isset($infos['comptes'])){
            $compte = $this->compteRepository->findOneBy(['id' =>$infos['comptes']]);

        }else{
            $agence = $this->agenceRepository->findOneBy(['id' => $user->getAgence()->getId()]);
            $compte = $this->compteRepository->findOneBy(['id' => $agence->getCompte()->getId()]);
        }
        if($infos['montant']> 0){
            $compte->setSolde($compte->getSolde() + $infos['montant']);

        }else{
            return new JsonResponse("le montant doit etre superieiur à 0",400,[],true);
        }

        $depot->setUtilisateur($user);
        $depot->setCompte($compte);
        $this->manager->persist($depot);
        $this->manager->flush();
        return new JsonResponse("le depot a été effectuer avec success",200,[],true);

    }
}
