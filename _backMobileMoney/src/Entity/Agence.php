<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AgenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass=AgenceRepository::class)
 *  @ApiResource(
 *      normalizationContext={"groups"={"agence:read"}},
 *     itemOperations={
 *     "get"={
 *     "method":"GET","path":"/agences/{id}",
 *     "normalizationContext"={"groups"={"agencex:read"}},
 *          }
 * ,"delete","put"},
 *     collectionOperations={
 *     "addAgence"={
 *     "method":"POST",
 *     "route_name"="addingAgence",
 *     "path":"/agences"
 *      },
 *     "get"
 * }
 * )
 */
class Agence
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"adminAgence:read","agence:read","user:read","agencex:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"adminAgence:read","user:read","agencex:read"})
     */
    private $nomAgence;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"adminAgence:read","agence:read","user:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;



    /**
     * @ORM\OneToOne(targetEntity=Compte::class, cascade={"persist", "remove"})
     * @Groups({"user:read","agence:read"})
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="agence")
     */
    private $utilisateurs;



    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomAgence(): ?string
    {
        return $this->nomAgence;
    }

    public function setNomAgence(string $nomAgence): self
    {
        $this->nomAgence = $nomAgence;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }



    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setAgence($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getAgence() === $this) {
                $utilisateur->setAgence(null);
            }
        }

        return $this;
    }


}
