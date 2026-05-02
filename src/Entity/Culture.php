<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CultureRepository;

#[ORM\Entity(repositoryClass: CultureRepository::class)]
#[ORM\Table(name: 'cultures')]
#[ORM\HasLifecycleCallbacks]
class Culture
{
    public const ETAT_EN_COURS = 'EN_COURS';
    public const ETAT_EN_VENTE = 'EN_VENTE';
    public const ETAT_VENDUE = 'VENDUE';
    public const ETAT_RECOLTEE = 'RECOLTEE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Parcelle::class)]
    #[ORM\JoinColumn(name: 'parcelle_id', referencedColumnName: 'id', nullable: false)]
    private ?Parcelle $parcelle_id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'proprietaire_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $proprietaire_id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type_culture = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $superficie = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_recolte = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $recolte_estime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'acheteur_id', referencedColumnName: 'id', nullable: true)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_vente = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_publication = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $prix_vente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getParcelle_id(): ?int
    {
        return $this->getParcelleId();
    }

    public function setParcelle_id(int $parcelle_id): self
    {
        return $this->setParcelleId($parcelle_id);
    }

    public function getProprietaire_id(): ?int
    {
        return $this->getProprietaireId();
    }

    public function setProprietaire_id(int $proprietaire_id): self
    {
        return $this->setProprietaireId($proprietaire_id);
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getType_culture(): ?string
    {
        return $this->type_culture;
    }

    public function setType_culture(?string $type_culture): self
    {
        $this->type_culture = $type_culture;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(?float $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat ?? self::ETAT_EN_COURS;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDate_recolte(): ?\DateTimeInterface
    {
        return $this->date_recolte;
    }

    public function setDate_recolte(?\DateTimeInterface $date_recolte): self
    {
        $this->date_recolte = $date_recolte;

        return $this;
    }

    public function getRecolte_estime(): ?float
    {
        return $this->recolte_estime;
    }

    public function setRecolte_estime(?float $recolte_estime): self
    {
        $this->recolte_estime = $recolte_estime;

        return $this;
    }

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    protected function setDate_creation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getAcheteur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setAcheteur(?Utilisateur $acheteur): self
    {
        $this->utilisateur = $acheteur;

        return $this;
    }

    public function getDate_vente(): ?\DateTimeInterface
    {
        return $this->date_vente;
    }

    public function setDate_vente(?\DateTimeInterface $date_vente): self
    {
        $this->date_vente = $date_vente;

        return $this;
    }

    public function getDate_publication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDate_publication(?\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getPrix_vente(): ?float
    {
        return $this->prix_vente;
    }

    public function setPrix_vente(?float $prix_vente): self
    {
        $this->prix_vente = $prix_vente;

        return $this;
    }

    public function getParcelle(): ?Parcelle
    {
        return $this->parcelle_id;
    }

    public function setParcelle(?Parcelle $parcelle): static
    {
        $this->parcelle_id = $parcelle;

        return $this;
    }

    public function getParcelleId(): ?int
    {
        return $this->parcelle_id?->getId();
    }

    public function setParcelleId(int $parcelle_id): static
    {
        $parcelle = new Parcelle();
        $parcelle->setId($parcelle_id);
        $this->parcelle_id = $parcelle;

        return $this;
    }

    public function getProprietaire(): ?Utilisateur
    {
        return $this->proprietaire_id;
    }

    public function setProprietaire(?Utilisateur $proprietaire): self
    {
        $this->proprietaire_id = $proprietaire;

        return $this;
    }

    public function getProprietaireId(): ?int
    {
        return $this->proprietaire_id?->getId();
    }

    public function setProprietaireId(int $proprietaire_id): static
    {
        $proprietaire = new Utilisateur();
        $proprietaire->setId($proprietaire_id);
        $this->proprietaire_id = $proprietaire;

        return $this;
    }

    public function getTypeCulture(): ?string
    {
        return $this->type_culture;
    }

    public function setTypeCulture(?string $type_culture): static
    {
        $this->type_culture = $type_culture;

        return $this;
    }

    public function getDateRecolte(): ?\DateTimeInterface
    {
        return $this->date_recolte;
    }

    public function setDateRecolte(?\DateTimeInterface $date_recolte): static
    {
        $this->date_recolte = $date_recolte;

        return $this;
    }

    public function getRecolteEstime(): ?float
    {
        return $this->recolte_estime;
    }

    public function setRecolteEstime(?float $recolte_estime): static
    {
        $this->recolte_estime = $recolte_estime;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    protected function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateVente(): ?\DateTimeInterface
    {
        return $this->date_vente;
    }

    public function setDateVente(?\DateTimeInterface $date_vente): static
    {
        $this->date_vente = $date_vente;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(?\DateTimeInterface $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prix_vente;
    }

    public function setPrixVente(?float $prix_vente): static
    {
        $this->prix_vente = $prix_vente;

        return $this;
    }

    public function getAcheteurId(): ?int
    {
        return $this->utilisateur?->getId();
    }

    public function hasAcheteur(): bool
    {
        return null !== $this->getAcheteurId();
    }

    public function isEnCours(): bool
    {
        return self::ETAT_EN_COURS === $this->getEtat();
    }

    public function isEnVente(): bool
    {
        return self::ETAT_EN_VENTE === $this->getEtat();
    }

    public function isVendue(): bool
    {
        return self::ETAT_VENDUE === $this->getEtat();
    }

    public function isRecoltee(): bool
    {
        return self::ETAT_RECOLTEE === $this->getEtat();
    }

    public function isOwnedBy(?int $utilisateurId): bool
    {
        return null !== $utilisateurId && $utilisateurId === $this->getProprietaireId();
    }

    public function isBoughtBy(?int $utilisateurId): bool
    {
        return null !== $utilisateurId && $utilisateurId === $this->getAcheteurId();
    }

    public function canBePublishedBy(?int $utilisateurId): bool
    {
        return $this->isOwnedBy($utilisateurId)
            && !$this->hasAcheteur()
            && $this->isEnCours();
    }

    public function canCancelPublicationBy(?int $utilisateurId): bool
    {
        return $this->isOwnedBy($utilisateurId)
            && !$this->hasAcheteur()
            && $this->isEnVente();
    }

    public function canBeBoughtBy(?int $utilisateurId): bool
    {
        return null !== $utilisateurId
            && !$this->isOwnedBy($utilisateurId)
            && !$this->hasAcheteur()
            && $this->isEnVente();
    }

    public function canBeHarvestedBy(?int $utilisateurId): bool
    {
        if ($this->isRecoltee() || null === $utilisateurId) {
            return false;
        }

        if ($this->isVendue()) {
            return $this->isBoughtBy($utilisateurId);
        }

        return $this->isOwnedBy($utilisateurId);
    }

    public function isModifiableOrSuppressible(): bool
    {
        return !$this->hasAcheteur()
            && \in_array($this->getEtat(), [self::ETAT_EN_COURS, self::ETAT_EN_VENTE], true);
    }

    public function canBeViewedBy(?int $utilisateurId): bool
    {
        return $this->isOwnedBy($utilisateurId)
            || $this->isBoughtBy($utilisateurId)
            || $this->canBeBoughtBy($utilisateurId);
    }

    #[ORM\PrePersist]
    public function initializeDateCreation(): void
    {
        if (null === $this->date_creation) {
            $this->date_creation = new \DateTimeImmutable();
        }
    }
}
