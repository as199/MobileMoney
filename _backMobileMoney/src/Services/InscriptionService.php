<?php


namespace App\Services;


use App\Entity\AdminAgence;
use App\Entity\AdminSysteme;
use App\Entity\Caissier;
use App\Entity\Utilisateur;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class InscriptionService
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;
    /**
     * @var ProfilRepository
     */
    private ProfilRepository $profilRepository;



    /**
     * InscriptionService constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param SerializerInterface $serializer
     * @param ProfilRepository $profilRepository
     */
    public function __construct( UserPasswordEncoderInterface $encoder,SerializerInterface $serializer, ProfilRepository $profilRepository)
    {
        $this->encoder =$encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
    }
    public function NewUser($profil, Request $request){
        $userReq = $request->request->all();


        $uploadedFile = $request->files->get('avatar');

        if($uploadedFile){
            $file = $uploadedFile->getRealPath();
            $userReq['avatar']= fopen($file,'r+');
        }

        if($profil == "AdminAgence"){
            $user = AdminAgence::class;
        }elseif ($profil == "AdminSysteme"){
            $user =AdminSysteme::class;
        }elseif ($profil == "Caissier"){
            $user =Caissier::class;
        }else{
            $user = Utilisateur::class;
        }
        $newUser = $this->serializer->denormalize($userReq, $user);
        $newUser->setProfil($this->profilRepository->findOneBy(['libelle'=>$profil]));
        $newUser->setStatus(false);
        $newUser->setPassword($this->encoder->encodePassword($newUser,$userReq['password']));

        return $newUser;
    }
}