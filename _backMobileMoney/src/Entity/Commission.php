<?php

namespace App\Entity;

use App\Repository\CommissionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommissionRepository::class)
 */
class Commission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $etat;

    /**
     * @ORM\Column(type="integer")
     */
    private $transfertArgent;

    /**
     * @ORM\Column(type="integer")
     */
    private $restant;

    /**
     * @ORM\Column(type="integer")
     */
    private $operateurDepot;

    /**
     * @ORM\Column(type="integer")
     */
    private $operateurRetrait;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTransfertArgent(): ?int
    {
        return $this->transfertArgent;
    }

    public function setTransfertArgent(int $transfertArgent): self
    {
        $this->transfertArgent = $transfertArgent;

        return $this;
    }

    public function getRestant(): ?int
    {
        return $this->restant;
    }

    public function setRestant(int $restant): self
    {
        $this->restant = $restant;

        return $this;
    }

    public function getOperateurDepot(): ?int
    {
        return $this->operateurDepot;
    }

    public function setOperateurDepot(int $operateurDepot): self
    {
        $this->operateurDepot = $operateurDepot;

        return $this;
    }

    public function getOperateurRetrait(): ?int
    {
        return $this->operateurRetrait;
    }

    public function setOperateurRetrait(int $operateurRetrait): self
    {
        $this->operateurRetrait = $operateurRetrait;

        return $this;
    }
}
