<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\ProfilRepository;
use App\Repository\UtilisateurRepository;
use App\Services\GestionImage;
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
    /**
     * @var UtilisateurRepository
     */
    private UtilisateurRepository $repository;
    /**
     * @var UtilisateurRepository
     */
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(ProfilRepository $profilRepository, EntityManagerInterface $manager
        , SerializerInterface $serializer, UtilisateurRepository $utilisateurRepository,
                                UserPasswordEncoderInterface $encode, Validator $validator)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->encode =$encode;
        $this->profilRepository =$profilRepository;
        $this->validator =$validator;
        $this->utilisateurRepository =$utilisateurRepository;
    }

    /**
     * @Route("/api/adminSys/utilisateurs", name="adding",methods={"POST"})
     * @param InscriptionService $service
     * @param Request $request
     * @return JsonResponse
     */
    public function Adduser(InscriptionService $service, Request $request): JsonResponse
    {
       

        $utilisateur = $service->NewUser($request);
        $this->validator->ValidatePost($utilisateur) ;
        $this->manager->persist($utilisateur);
        $this->manager->flush();
        return new JsonResponse(["data"=>$utilisateur], 200);

    }


    public function UpdateUser(GestionImage $service,Request $request, $id): JsonResponse
    {

        // $profil = $request->get('profil'); //pour dynamiser

        $userUpdate = $service->GestionImage($request,'avatar');
        $utilisateur = $this->utilisateurRepository->findOneBy(['id'=>$id]);
        //dd($utilisateur);
        foreach ($userUpdate as $key=> $valeur){
            $setter = 'set'. ucfirst(strtolower($key));
            //dd($setter);
            if(method_exists(Utilisateur::class, $setter)){
                if($setter=='setProfil'){

                }
                else{
                    $utilisateur->$setter($valeur);
                }

            }
            if($setter  =='setPrenom'){
                $nom = $userUpdate["prenom"].' '.$userUpdate["nom"];
                $utilisateur->setNomComplet($nom);
            }
            if ($setter=='setPassword'){

                if(isset($userUpdate['password'])){
                    $utilisateur->setPassword($this->encode->encodePassword($utilisateur,$userUpdate['password']));
                }
            }


        }

        $this->manager->persist($utilisateur);
        $this->manager->flush();
        return $this->json([$utilisateur],200);


    }

    public function ResetPassword(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(),true);
       $user = $this->utilisateurRepository->findOneBy(['telephone'=>$data['telephone']]);
       if(isset($user) && $user->getEmail() === $data['email']){
           $newPassword = $this->genererChaineAleatoire();
           $user->setPassword($this->encode->encodePassword($user,$newPassword));
           $this->manager->flush();
       }
        return new JsonResponse(["data"=>$newPassword],200);


    }

    private  function genererChaineAleatoire($longueur = 8)
    {
        $x='0123456789@&!abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($x, ceil($longueur/strlen($x)) )),1,$longueur);
    }
}
