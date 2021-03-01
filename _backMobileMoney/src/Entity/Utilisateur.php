<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_id", type="integer")
 * @ORM\DiscriminatorMap({1 ="AdminSysteme",2="Caissier",3="AdminAgence",4="UserAgence", 5="Utilisateur"})
 * @ApiResource (attributes={"route_prefix"="/adminSys","security"="is_granted('ROLE_AdminSysteme')"},
 *     normalizationContext={"groups"={"user:read"}},
 *    itemOperations={"GET","PUT","DELETE"},
 *    collectionOperations={
 *        "addUser":{
 *              "route_name"="adding",
 *              "method":"POST",
 *              "path":"/utilisateurs",
 *              "access_control"="(is_granted('ROLE_AdminSysteme') )",
 *              "access_control_message"="Vous n'avez pas access à cette Ressource",
 *          },"GET"
 *     }
 * )
 * * @UniqueEntity(fields="email", message="ce e-mail {{ value }} est féjà utilisé !")
 * @UniqueEntity(fields="telephone", message="ce tele {{ value }} already being used.")

 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank
     *  @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     * @Assert\NotBlank(message="please enter your E-mail")
     * @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     *
     */
    private ?string $email;


    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter your complete name")
     * @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     */
    private ?string $nomComplet;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="please enter your phone number")
     * @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     */
    private ?string $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter your Adresse")
     * @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     */
    private ?string $Adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="please enter your gender")
     *  @Groups({"compte:read","user:read","caissier:read","adminAgence:read","transaction:read"})
     */
    private ?string $genre;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $status;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="utilisateurs")
     * @Groups ({"user:read"})
     */
    private ?Profil $profil;




    /**
     * @ORM\Column(type="blob", nullable=true)
     *  @Groups({"compte:read","user:read","caissier:read"})
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(targetEntity=Transaction::class, inversedBy="utilisateurs")
     *
     */
    private $transaction;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="utilisateur")
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userEnvoi")
     */
    private $transactions;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->depots = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }




    public function getAvatar()
    {
        if ($this->avatar != null) {
            return base64_encode(stream_get_contents($this->avatar));
        } else {
            return $this->avatar;
        }
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction[] = $transaction;
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        $this->transaction->removeElement($transaction);

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setUtilisateur($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getUtilisateur() === $this) {
                $depot->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
