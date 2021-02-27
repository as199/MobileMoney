<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use App\Services\InscriptionService;
use App\Services\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AdminSystemController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encode;
    /**
     * @var ProfilRepository
     */
    private ProfilRepository $profilRepository;
    /**
     * @var Validator
     */
    private Validator $validator;

    public function __construct(ProfilRepository $profilRepository, EntityManagerInterface $manager
        , SerializerInterface $serializer,
                                UserPasswordEncoderInterface $encode, Validator $validator)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->encode =$encode;
        $this->profilRepository =$profilRepository;
        $this->validator =$validator;
    }

    /**
     * @Route("/api/adminSys/utilisateurs", name="adding",methods={"POST"})
     * @param InscriptionService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function Adduser(InscriptionService $service, Request $request): JsonResponse
    {
        $type = $request->get('type'); //pour dynamiser

        $utilisateur = $service->NewUser($type,$request);
        $this->validator->ValidatePost($utilisateur) ;
        $this->manager->persist($utilisateur);
        $this->manager->flush();
        return new JsonResponse("success", 200, [], true);

    }
}
