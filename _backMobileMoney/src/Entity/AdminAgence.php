<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AdminAgenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"security"="is_granted('ROLE_AdminSysteme')"},
 *      normalizationContext={"groups"={"adminAgence:read"}}
 * )
 * @ORM\Entity(repositoryClass=AdminAgenceRepository::class)
 */
class AdminAgence extends Utilisateur
{


    /**
     * @ORM\OneToOne(targetEntity=Agence::class, cascade={"persist", "remove"})
     * @Groups({"adminAgence:read"})
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
