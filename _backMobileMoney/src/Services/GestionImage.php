<?php


namespace App\Services;
use App\Repository\ProfilRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;

class GestionImage
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
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;


    /**
     * InscriptionService constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param SerializerInterface $serializer
     * @param ProfilRepository $profilRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(UserPasswordEncoderInterface $encoder, SerializerInterface $serializer, ProfilRepository $profilRepository, ValidatorInterface $validator)
    {
        $this->encoder = $encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
        $this->validator = $validator;
    }

    /**
     * put image of user
     * @param Request $request
     * @param string|null $fileName
     * @return array
     */
    public function GestionImage(Request $request, string $fileName = null): array
    {
        $raw = $request->getContent();
        //dd($request->headers->get("content-type"));
        $delimiteur = "multipart/form-data; boundary=";
        $boundary = "--" . explode($delimiteur, $request->headers->get("content-type"))[1];
        //dd($boundary);
        $elements = str_replace([$boundary, 'Content-Disposition: form-data;', "name="], "", $raw);
        //dd($elements);
        $elementsTab = explode("\r\n\r\n", $elements);
        //dd($elementsTab);
        $data = [];
        for ($i = 0; isset($elementsTab[$i + 1]); $i += 2) {
            //dd($elementsTab[$i+1]);
            $key = str_replace(["\r\n", ' "', '"'], '', $elementsTab[$i]);
            //dd($key);
            if (strchr($key, $fileName)) {
                $stream = fopen('php://memory', 'r+');
                //dd($stream);
                fwrite($stream, $elementsTab[$i + 1]);
                rewind($stream);
                $data[$fileName] = $stream;
                //dd($data);
            } else {
                $val = $elementsTab[$i + 1];
                $val = str_replace(["\r\n", "--"],'',$elementsTab[$i+1]);
                //dd($val);
                $data[$key] = $val;
                // dd($data[$key]);
            }
        }
        //dd($data);
        if (isset($data["profil"])){
            $prof = $this->profilRepository->findOneBy(['libelle' => $data["profil"]]);
            $data["profil"] = $prof;
        }

        //dd($data);
        return $data;
    }


}