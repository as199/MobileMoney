<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserAgenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAgenceRepository::class)
 * @ApiResource (itemOperations={"GET","PUT",
 *     "deleteUserAgence":{
 *              "method":"DELETE",
 *              "path":"/user/user_agences/{id}",
 *              "access_control"="(is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          }
 *     },
 *    collectionOperations={
 *      "getTransaction":{
 *              "method":"GET",
 *              "path":"/user/user_agences",
 *              "access_control"="(is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          }
 *     }
 *     )
 */
class UserAgence extends Utilisateur
{

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="userAgences")
     */
    private $agence;


    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }
}
