<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UtilisateurRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="L'adresse Email est obligatoire")
     * @Assert\Email(
     *     message = "L'email '{{ value }}' n'est pas une adresse mail valide.",
     *     checkMX = true
     * )
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre nom")
     * @Assert\Length(
     *     min="2", max="255",
     *     minMessage="Le nom doit faire au moins 2 charactères",
     *     maxMessage="Le nom de téléphone doit faire 255 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="Veuillez renseigner votre prénom")
     * @Assert\Length(
     *     min="2", max="255",
     *     minMessage="Le prénom doit faire au moins 2 charactères",
     *     maxMessage="Le prénom doit faire 255 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @Assert\NotBlank(message="Le pseudo est obligatoire")
     * @Assert\Length(
     *     min="2", max="255",
     *     minMessage="Le pseudo doit faire au moins 2 charactères",
     *     maxMessage="Le pseudo doit faire 255 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $pseudo;

    /**
     * @Assert\Length(
     *     min="8", max="15",
     *     minMessage="Le numéro de téléphone doit faire au moins 8 charactères",
     *     maxMessage="Le numéro de téléphone doit faire 15 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @Assert\Length(
     *     min="2", max="225",
     *     minMessage="Le nom de la ville doit faire au moins 2 charactères",
     *     maxMessage="Le nom de la ville doit faire 255 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @Assert\Length(
     *     min="2", max="225",
     *     minMessage="Le nom de la ville doit faire au moins 2 charactères",
     *     maxMessage="Le nom de la ville doit faire 255 charactères maximum"
     * )
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cp;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $centreFormation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     */
    private $sortiesOrganisees;



    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie", mappedBy="participants")
     */
    private $mesSorties;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\File(mimeTypes={ "image/*" })
     */
    private $file;

    public function __construct()
    {
        $this->sortiesOrganisees = new ArrayCollection();
        $this->mesSorties = new ArrayCollection();
        $this->setActif(true);
        $this->setAdministrateur(false);
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
        $roles[] = 'ROLE_USER';

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
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(?int $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCentreFormation(): ?Site
    {
        return $this->centreFormation;
    }

    public function setCentreFormation(?Site $centreFormation): self
    {
        $this->centreFormation = $centreFormation;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sortiesOrganisees->contains($sorty)) {
            $this->sortiesOrganisees[] = $sorty;
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sortiesOrganisees->contains($sorty)) {
            $this->sortiesOrganisees->removeElement($sorty);
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getMesSorties(): Collection
    {
        return $this->mesSorties;
    }

    public function addMesSorty(Sortie $mesSorty): self
    {
        if (!$this->mesSorties->contains($mesSorty)) {
            $this->mesSorties[] = $mesSorty;
            $mesSorty->addParticipant($this);
        }

        return $this;
    }

    public function removeMesSorty(Sortie $mesSorty): self
    {
        if ($this->mesSorties->contains($mesSorty)) {
            $this->mesSorties->removeElement($mesSorty);
            $mesSorty->removeParticipant($this);
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }


}
