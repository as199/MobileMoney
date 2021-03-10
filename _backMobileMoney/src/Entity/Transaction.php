<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 * * @ApiResource( itemOperations={"GET","PUT",
 *     "deleteTransaction":{
 *              "route_name"="deleteTransaction",
 *              "method":"DELETE",
 *              "path":"/transactions/delete",
 *              "denormalizationContext"={"groups"={"transaction:write"}},
 *              "access_control"="(is_granted('ROLE_UserAgence') or is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          },
 *     "findTransaction":{
 *              "route_name"="findTransaction",
 *              "method":"POST",
 *              "path":"/transactions/find",
 *              "normalization_context"={"groups"={"transactionGet:read"}},
 *              "access_control"="(is_granted('ROLE_UserAgence') or is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          },
 * },
 *    collectionOperations={
 *        "addtransaction":{
 *              "route_name"="addTransaction",
 *              "method":"POST",
 *              "path":"/transactions",
 *              "denormalizationContext"={"groups"={"transaction:write"}},
 *              "access_control"="(is_granted('ROLE_UserAgence') or is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          },
 *      "getTransaction":{
 *              "method":"GET",
 *              "path":"/transactions",
 *              "normalizationContext"={"groups"={"transaction:read"}},
 *              "access_control"="(is_granted('ROLE_AdminSysteme') or is_granted('ROLE_AdminAgence') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          },
 *         "Calculer":{
 *               "method":"POST",
 *               "path":"/calculer",
 *               "route_name"="addCalcul"
 *          }
 *     })
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"client:read","transaction:read","transactionGet:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"transaction:write","client:read","transaction:read","transactionGet:read"})
     *
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"transaction:write","client:read","transaction:read","transactionGet:read"})
     */
    private $dateEnvoi;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"transaction:write","client:read","transaction:read"})
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"transaction:write","client:read","transaction:read"})
     */
    private $dateAnnulation;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"transaction:write","client:read","transaction:read"})
     */
    private $totalCommission;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"transaction:write","client:read"})
     */
    private $commissionEtat;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"transaction:write","client:read","transaction:read"})
     */
    private $commissionTransfere;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"transaction:write","transaction:read"})
     */
    private $commissionDepot;

    /**
     * @ORM\Column(type="float")
     * @Groups ({"transaction:write","client:read","transaction:read"})
     */
    private $commissionRetrait;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    /**
     * @ORM\ManyToMany(targetEntity=Utilisateur::class, mappedBy="transaction")
     * @Groups ({"transaction:write","transaction:read"})
     */
    private $utilisateurs;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups ({"transaction:write","transaction:read"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="transactions")
     *  @Groups ({"transaction:write","transaction:read"})
     */
    private $compte;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups ({"transactionGet:read"})
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     * @Groups ({"transactionGet:read"})
     */
    private $clientEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     *  @Groups ({"transactionGet:read"})
     */
    private $clientRecepteur;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="transactions")
     */
    private $userEnvoi;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="transactions")
     */
    private $userRetrait;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->clients = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getDateAnnulation(): ?\DateTimeInterface
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(?\DateTimeInterface $dateAnnulation): self
    {
        $this->dateAnnulation = $dateAnnulation;

        return $this;
    }

    public function getTotalCommission(): ?float
    {
        return $this->totalCommission;
    }

    public function setTotalCommission(float $totalCommission): self
    {
        $this->totalCommission = $totalCommission;

        return $this;
    }

    public function getCommissionEtat(): ?float
    {
        return $this->commissionEtat;
    }

    public function setCommissionEtat(float $commissionEtat): self
    {
        $this->commissionEtat = $commissionEtat;

        return $this;
    }

    public function getCommissionTransfere(): ?float
    {
        return $this->commissionTransfere;
    }

    public function setCommissionTransfere(float $commissionTransfere): self
    {
        $this->commissionTransfere = $commissionTransfere;

        return $this;
    }

    public function getCommissionDepot(): ?float
    {
        return $this->commissionDepot;
    }

    public function setCommissionDepot(float $commissionDepot): self
    {
        $this->commissionDepot = $commissionDepot;

        return $this;
    }

    public function getCommissionRetrait(): ?float
    {
        return $this->commissionRetrait;
    }

    public function setCommissionRetrait(float $commissionRetrait): self
    {
        $this->commissionRetrait = $commissionRetrait;

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
            $utilisateur->addTransaction($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            $utilisateur->removeTransaction($this);
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getClientEnvoi(): ?Client
    {
        return $this->clientEnvoi;
    }

    public function setClientEnvoi(?Client $clientEnvoi): self
    {
        $this->clientEnvoi = $clientEnvoi;

        return $this;
    }

    public function getClientRecepteur(): ?Client
    {
        return $this->clientRecepteur;
    }

    public function setClientRecepteur(?Client $clientRecepteur): self
    {
        $this->clientRecepteur = $clientRecepteur;

        return $this;
    }

    public function getUserEnvoi(): ?Utilisateur
    {
        return $this->userEnvoi;
    }

    public function setUserEnvoi(?Utilisateur $userEnvoi): self
    {
        $this->userEnvoi = $userEnvoi;

        return $this;
    }

    public function getUserRetrait(): ?Utilisateur
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?Utilisateur $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }


}
