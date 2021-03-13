<?php

namespace App\Controller;

use App\Entity\Agence;
use App\Entity\Compte;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\UserAgenceRepository;
use App\Services\GenererNum;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AgenceController extends AbstractController
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
     * @var UserAgenceRepository
     */
    private UserAgenceRepository $userAgenceRepository;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var GenererNum
     */
    private GenererNum $generator;

    /**
     * AgenceController constructor.
     * @param AgenceRepository $agenceRepository
     * @param CompteRepository $compteRepository
     * @param UserAgenceRepository $userAgenceRepository
     * @param EntityManagerInterface $manager
     * @param GenererNum $generator
     */
    public function __construct(AgenceRepository $agenceRepository,
                                CompteRepository $compteRepository,
                                UserAgenceRepository $userAgenceRepository,
                                EntityManagerInterface $manager,
                                GenererNum $generator
    )
    {
        $this->agenceRepository = $agenceRepository;
        $this->compteRepository = $compteRepository;
        $this->userAgenceRepository = $userAgenceRepository;
        $this->manager = $manager;
        $this->generator = $generator;
    }

    /**
     * @Route("/agence", name="agence")
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     * @return Response
     * @throws ExceptionInterface
     */
    public function AddAgence(Request $request, SerializerInterface $serializer, TokenStorageInterface $tokenStorage): Response
    {
      $infos = json_decode($request->getContent(),true);
        $agence = $serializer->denormalize($infos, Agence::class);
        $newcompte = new Compte();
        $newcompte->setNumCompte($this->generator->genrecode("CMPT",'compte'));
        $newcompte->setSolde(70000);
        $newcompte->setAdminSysteme($tokenStorage->getToken()->getUser());
        $newcompte->setStatus(false);
        $this->manager->persist($newcompte);
          

      
        if($infos['userAgence']){
            foreach($infos['userAgence'] as  $user){
              if( $agent = $this->userAgenceRepository->find($user)){

                  $agence->addUtilisateur($agent);
              }

            }

        }
        $agence->setStatus(false);
        $agence->setCompte($newcompte);
        $this->manager->persist($agence);
        $this->manager->flush();
        return $this->json(["data"=> $agence], 200);
    }

}
