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
    public function NewUser(Request $request){
        
       $userReq = $request->request->all();
        $nomComplet = $userReq['prenom'].' '.$userReq['nom'];
        $profil = $userReq['type'];
        $uploadedFile = $request->files->get('avatar');
        if(isset($userReq['agences'])){
            $id =(int)$userReq['agences'];
            $agence = $this->agenceRepository->findOneBy(['id'=>$id]);
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
            

        }elseif ($profil == "UserAgence"){
            $user =UserAgence::class;
        }else{
            $user = Utilisateur::class;
        }
        $newUser = $this->serializer->denormalize($userReq, $user);
        $newUser->setProfil($this->profilRepository->findOneBy(['libelle'=>$profil]));
        

        $newUser->setStatus(false);
        $newUser->setNomComplet($nomComplet);

        $newUser->setAgence($agence);

        
        $newUser->setPassword($this->encoder->encodePassword($newUser,$userReq['password']));

        return $newUser;
    }
}
