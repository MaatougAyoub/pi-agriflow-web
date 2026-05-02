<?php

namespace App\Entity;

use App\Entity\Diagnosti;
use App\Entity\Reclamation;
use App\Enum\Role;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
#[ORM\UniqueConstraint(name: 'cin', columns: ['cin'])]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
class Utilisateur
    implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(name: 'motDePasse', length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(length: 40)]
    private ?string $role = null;

    #[ORM\Column(name: 'dateCreation', type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(length: 500)]
    private ?string $signature = null;

    #[ORM\Column(nullable: true)]
    private ?float $revenu = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $carte_pro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $parcelles = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $certification = null;

    #[ORM\Column(length: 20, options: ['default' => 'APPROVED'])]
    private ?string $verification_status = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $verification_reason = null;

    #[ORM\Column(nullable: true)]
    private ?float $verification_score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_ar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom_ar = null;

    /**
     * @var Collection<int, Reclamation>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Reclamation::class, orphanRemoval: true)]
    private Collection $reclamations;

    /**
     * @var Collection<int, Diagnosti>
     */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Diagnosti::class)]
    private Collection $diagnostis;

    public function __construct()
    {
        $this->reclamations = new ArrayCollection();
        $this->diagnostis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles(): array
    {
        $storedRole = strtoupper((string) $this->role);

        // DB contains domain values (ADMIN/EXPERT/AGRICULTEUR); normalize to Symfony role names.
        if (str_starts_with($storedRole, 'ROLE_')) {
            $securityRole = $storedRole;
        } elseif (Role::tryFrom('ROLE_'.$storedRole) !== null) {
            $securityRole = 'ROLE_'.$storedRole;
        } else {
            $securityRole = Role::AGRICULTEUR->value;
        }

        return array_values(array_unique([$securityRole]));
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPassword(): string
    {
        return (string) $this->motDePasse;
    }

    public function eraseCredentials(): void
    {
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    public function getRevenu(): ?float
    {
        return $this->revenu;
    }

    public function setRevenu(?float $revenu): static
    {
        $this->revenu = $revenu;

        return $this;
    }

    public function getCartePro(): ?string
    {
        return $this->carte_pro;
    }

    public function setCartePro(?string $carte_pro): static
    {
        $this->carte_pro = $carte_pro;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getParcelles(): ?string
    {
        return $this->parcelles;
    }

    public function setParcelles(?string $parcelles): static
    {
        $this->parcelles = $parcelles;

        return $this;
    }

    public function getCertification(): ?string
    {
        return $this->certification;
    }

    public function setCertification(?string $certification): static
    {
        $this->certification = $certification;

        return $this;
    }

    public function getVerificationStatus(): ?string
    {
        return $this->verification_status;
    }

    public function setVerificationStatus(?string $verification_status): static
    {
        $this->verification_status = $verification_status;

        return $this;
    }

    public function getVerificationReason(): ?string
    {
        return $this->verification_reason;
    }

    public function setVerificationReason(?string $verification_reason): static
    {
        $this->verification_reason = $verification_reason;

        return $this;
    }

    public function getVerificationScore(): ?float
    {
        return $this->verification_score;
    }

    public function setVerificationScore(?float $verification_score): static
    {
        $this->verification_score = $verification_score;

        return $this;
    }

    public function getNomAr(): ?string
    {
        return $this->nom_ar;
    }

    public function setNomAr(?string $nom_ar): static
    {
        $this->nom_ar = $nom_ar;

        return $this;
    }

    public function getPrenomAr(): ?string
    {
        return $this->prenom_ar;
    }

    public function setPrenomAr(?string $prenom_ar): static
    {
        $this->prenom_ar = $prenom_ar;

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Diagnosti>
     */
    public function getDiagnostis(): Collection
    {
        return $this->diagnostis;
    }

    public function addDiagnosti(Diagnosti $diagnosti): static
    {
        if (!$this->diagnostis->contains($diagnosti)) {
            $this->diagnostis->add($diagnosti);
            $diagnosti->setUtilisateur($this);
        }

        return $this;
    }

    public function removeDiagnosti(Diagnosti $diagnosti): static
    {
        if ($this->diagnostis->removeElement($diagnosti)) {
            if ($diagnosti->getUtilisateur() === $this) {
                $diagnosti->setUtilisateur(null);
            }
        }

        return $this;
    }
}
