<?php


namespace App\Services;


use App\Entity\AdminAgence;
use App\Entity\AdminSysteme;
use App\Entity\Caissier;
use App\Entity\UserAgence;
use App\Entity\Utilisateur;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
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
     * @var CompteRepository
     */
    private CompteRepository $compteRepository;
    /**
     * @var AgenceRepository
     */
    private AgenceRepository $agenceRepository;


    /**
     * InscriptionService constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param AgenceRepository $agenceRepository
     * @param CompteRepository $compteRepository
     * @param SerializerInterface $serializer
     * @param ProfilRepository $profilRepository
     */
    public function __construct( UserPasswordEncoderInterface $encoder, AgenceRepository $agenceRepository, CompteRepository $compteRepository, SerializerInterface $serializer, ProfilRepository $profilRepository)
    {
        $this->encoder =$encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
        $this->compteRepository = $compteRepository;
        $this->agenceRepository = $agenceRepository;
    }
    public function NewUser($profil, Request $request){
        $ref = false;
        $userReq = $request->request->all();


        $uploadedFile = $request->files->get('avatar');
        if(isset($userReq['agences'])){
            $agence = $this->agenceRepository->findOneBy(['nomAgence'=>$userReq['agences']]);
        }

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
            if(!empty($userReq['compte'])){
                $ref = true;
            }

        }elseif ($profil == "UserAgence"){
            $user =UserAgence::class;
        }else{
            $user = Utilisateur::class;
        }
        $newUser = $this->serializer->denormalize($userReq, $user);
        $newUser->setProfil($this->profilRepository->findOneBy(['libelle'=>$profil]));
        if ($ref){
            $newUser->addCompte($this->compteRepository->find($userReq['compte']));
        }

        $newUser->setStatus(false);
        if(isset($userReq['agences'])){

            $newUser->setAgence($this->agenceRepository->findOneBy(["id"=>$userReq['agences']]));

        }
        $newUser->setPassword($this->encoder->encodePassword($newUser,$userReq['password']));

        return $newUser;
    }
}