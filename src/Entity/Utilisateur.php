<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateurs')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prenom = null;

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $cin = null;

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): self
    {
        $this->cin = $cin;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $motDePasse = null;

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $signature = null;

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(string $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $revenu = null;

    public function getRevenu(): ?float
    {
        return $this->revenu;
    }

    public function setRevenu(?float $revenu): self
    {
        $this->revenu = $revenu;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $carte_pro = null;

    public function getCarte_pro(): ?string
    {
        return $this->carte_pro;
    }

    public function setCarte_pro(?string $carte_pro): self
    {
        $this->carte_pro = $carte_pro;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $adresse = null;

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $parcelles = null;

    public function getParcelles(): ?string
    {
        return $this->parcelles;
    }

    public function setParcelles(?string $parcelles): self
    {
        $this->parcelles = $parcelles;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $certification = null;

    public function getCertification(): ?string
    {
        return $this->certification;
    }

    public function setCertification(?string $certification): self
    {
        $this->certification = $certification;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $verification_status = null;

    public function getVerification_status(): ?string
    {
        return $this->verification_status;
    }

    public function setVerification_status(string $verification_status): self
    {
        $this->verification_status = $verification_status;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $verification_reason = null;

    public function getVerification_reason(): ?string
    {
        return $this->verification_reason;
    }

    public function setVerification_reason(?string $verification_reason): self
    {
        $this->verification_reason = $verification_reason;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $verification_score = null;

    public function getVerification_score(): ?float
    {
        return $this->verification_score;
    }

    public function setVerification_score(?float $verification_score): self
    {
        $this->verification_score = $verification_score;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom_ar = null;

    public function getNom_ar(): ?string
    {
        return $this->nom_ar;
    }

    public function setNom_ar(?string $nom_ar): self
    {
        $this->nom_ar = $nom_ar;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $prenom_ar = null;

    public function getPrenom_ar(): ?string
    {
        return $this->prenom_ar;
    }

    public function setPrenom_ar(?string $prenom_ar): self
    {
        $this->prenom_ar = $prenom_ar;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Culture::class, mappedBy: 'utilisateur')]
    private Collection $cultures;

    /**
     * @return Collection<int, Culture>
     */
    public function getCultures(): Collection
    {
        if (!$this->cultures instanceof Collection) {
            $this->cultures = new ArrayCollection();
        }
        return $this->cultures;
    }

    public function addCulture(Culture $culture): self
    {
        if (!$this->getCultures()->contains($culture)) {
            $this->getCultures()->add($culture);
        }
        return $this;
    }

    public function removeCulture(Culture $culture): self
    {
        $this->getCultures()->removeElement($culture);
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Diagnosti::class, mappedBy: 'utilisateur')]
    private Collection $diagnostis;

    public function __construct()
    {
        $this->cultures = new ArrayCollection();
        $this->diagnostis = new ArrayCollection();
    }

    /**
     * @return Collection<int, Diagnosti>
     */
    public function getDiagnostis(): Collection
    {
        if (!$this->diagnostis instanceof Collection) {
            $this->diagnostis = new ArrayCollection();
        }
        return $this->diagnostis;
    }

    public function addDiagnosti(Diagnosti $diagnosti): self
    {
        if (!$this->getDiagnostis()->contains($diagnosti)) {
            $this->getDiagnostis()->add($diagnosti);
        }
        return $this;
    }

    public function removeDiagnosti(Diagnosti $diagnosti): self
    {
        $this->getDiagnostis()->removeElement($diagnosti);
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

    public function getVerificationStatus(): ?string
    {
        return $this->verification_status;
    }

    public function setVerificationStatus(string $verification_status): static
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

    public function getVerificationScore(): ?string
    {
        return $this->verification_score;
    }

    public function setVerificationScore(?string $verification_score): static
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

}
