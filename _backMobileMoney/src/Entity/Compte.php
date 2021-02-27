<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 *
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 *     itemOperations={
    *           "updateCompte":{
    *             "route_name"="updateCompte",
    *              "method":"PUT",
    *              "path":"/adminSys/comptes/{id}",
    *              "access_control"="(is_granted('ROLE_AdminSysteme')or is_granted('ROLE_Caissier')  )",
    *              "access_control_message"="Vous n'avez pas access à cette Ressource",
     *      },
     *     "deleteCompte":{
     *              "method":"DELETE",
     *              "path":"/adminSys/comptes/{id}",
     *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
     *              "access_control_message"="Vous n'avez pas access à cette Ressource",
     *      },
     *     "getOneCompte":{
     *              "method":"GET",
     *              "path":"/comptes/{id}",
     *              "normalization_context"={"groups":"compte:read"},
     *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
     *              "access_control_message"="Vous n'avez pas access à cette Ressource",
     *      },
 *     },
 *     collectionOperations={
     *       "addCompte":{
     *              "method":"POST",
 *                  "route_name"="addCompte",
     *              "path":"/adminSys/comptes",
     *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
     *              "access_control_message"="Vous n'avez pas access à cette Ressource",
     *      },
     *      "getComptes":{
     *              "method":"GET",
     *              "path":"/adminSys/comptes",
     *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
     *               "access_control_message"="Vous n'avez pas access à cette Ressource",
     *       }
 *     }
 * )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"compte:read"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"compte:read"})
     */
    private $solde;
    /**
     * @ORM\Column(type="boolean")
     * @Groups({"compte:read"})
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Caissier::class, mappedBy="comptes")
     * @Groups({"compte:read"})
     */
    private $caissiers;

    /**
     * @ORM\ManyToOne(targetEntity=AdminSysteme::class, inversedBy="compte")
     */
    private $adminSysteme;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="compte")
     */
    private $transactions;

    public function __construct()
    {
        $this->caissiers = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?string
    {
        return $this->numCompte;
    }

    public function setNumCompte(string $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

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

    /**
     * @return Collection|Caissier[]
     */
    public function getCaissiers(): Collection
    {
        return $this->caissiers;
    }

    public function addCaissier(Caissier $caissier): self
    {
        if (!$this->caissiers->contains($caissier)) {
            $this->caissiers[] = $caissier;
            $caissier->addCompte($this);
        }

        return $this;
    }

    public function removeCaissier(Caissier $caissier): self
    {
        if ($this->caissiers->removeElement($caissier)) {
            $caissier->removeCompte($this);
        }

        return $this;
    }

    public function getAdminSysteme(): ?AdminSysteme
    {
        return $this->adminSysteme;
    }

    public function setAdminSysteme(?AdminSysteme $adminSysteme): self
    {
        $this->adminSysteme = $adminSysteme;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setCompte($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCompte() === $this) {
                $transaction->setCompte(null);
            }
        }

        return $this;
    }
}
