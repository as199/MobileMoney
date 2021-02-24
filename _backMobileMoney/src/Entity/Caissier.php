<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CaissierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *      itemOperations={
     *     "listerCaissier":{
     *              "method":"GET",
     *              "path":"/adminSys/caissiers/{id}",
     *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
     *              "access_control_message"="Vous n'avez pas access à cette Ressource",
     *      },
 *          "bloquerCaissier":{
 *              "method":"DELETE",
 *              "path":"/adminSys/caissiers/{id}",
 *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *      },
 *     },
 *     collectionOperations={
 *      "getComptes":{
 *              "method":"GET",
 *              "path":"/adminSys/caissiers",
 *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
 *               "access_control_message"="Vous n'avez pas access à cette Ressource",
 *       }
 *     })
 * @ORM\Entity(repositoryClass=CaissierRepository::class)
 */
class Caissier extends Utilisateur
{
    /**
     * @ORM\ManyToMany(targetEntity=Compte::class, inversedBy="caissiers")
     */
    private $comptes;

    public function __construct()
    {
        parent::__construct();
        $this->comptes = new ArrayCollection();
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        $this->comptes->removeElement($compte);

        return $this;
    }
}
