<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AnnonceRepository;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ORM\Table(name: 'annonces')]
class Annonce
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
    private ?string $titre = null;

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $prix = null;

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $unite_prix = null;

    public function getUnite_prix(): ?string
    {
        return $this->unite_prix;
    }

    public function setUnite_prix(?string $unite_prix): self
    {
        $this->unite_prix = $unite_prix;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $categorie = null;

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $marque = null;

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $modele = null;

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): self
    {
        $this->modele = $modele;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $annee_fabrication = null;

    public function getAnnee_fabrication(): ?int
    {
        return $this->annee_fabrication;
    }

    public function setAnnee_fabrication(?int $annee_fabrication): self
    {
        $this->annee_fabrication = $annee_fabrication;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $localisation = null;

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $latitude = null;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $longitude = null;

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $proprietaire_id = null;

    public function getProprietaire_id(): ?int
    {
        return $this->proprietaire_id;
    }

    public function setProprietaire_id(int $proprietaire_id): self
    {
        $this->proprietaire_id = $proprietaire_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_debut_disponibilite = null;

    public function getDate_debut_disponibilite(): ?\DateTimeInterface
    {
        return $this->date_debut_disponibilite;
    }

    public function setDate_debut_disponibilite(?\DateTimeInterface $date_debut_disponibilite): self
    {
        $this->date_debut_disponibilite = $date_debut_disponibilite;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_fin_disponibilite = null;

    public function getDate_fin_disponibilite(): ?\DateTimeInterface
    {
        return $this->date_fin_disponibilite;
    }

    public function setDate_fin_disponibilite(?\DateTimeInterface $date_fin_disponibilite): self
    {
        $this->date_fin_disponibilite = $date_fin_disponibilite;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_creation = null;

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDate_creation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_modification = null;

    public function getDate_modification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDate_modification(\DateTimeInterface $date_modification): self
    {
        $this->date_modification = $date_modification;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $avec_operateur = null;

    public function isAvec_operateur(): ?bool
    {
        return $this->avec_operateur;
    }

    public function setAvec_operateur(?bool $avec_operateur): self
    {
        $this->avec_operateur = $avec_operateur;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $assurance_incluse = null;

    public function isAssurance_incluse(): ?bool
    {
        return $this->assurance_incluse;
    }

    public function setAssurance_incluse(?bool $assurance_incluse): self
    {
        $this->assurance_incluse = $assurance_incluse;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $caution = null;

    public function getCaution(): ?float
    {
        return $this->caution;
    }

    public function setCaution(?float $caution): self
    {
        $this->caution = $caution;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $conditions_location = null;

    public function getConditions_location(): ?string
    {
        return $this->conditions_location;
    }

    public function setConditions_location(?string $conditions_location): self
    {
        $this->conditions_location = $conditions_location;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantite_disponible = null;

    public function getQuantite_disponible(): ?int
    {
        return $this->quantite_disponible;
    }

    public function setQuantite_disponible(?int $quantite_disponible): self
    {
        $this->quantite_disponible = $quantite_disponible;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $unite_quantite = null;

    public function getUnite_quantite(): ?string
    {
        return $this->unite_quantite;
    }

    public function setUnite_quantite(?string $unite_quantite): self
    {
        $this->unite_quantite = $unite_quantite;
        return $this;
    }

    public function getUnitePrix(): ?string
    {
        return $this->unite_prix;
    }

    public function setUnitePrix(?string $unite_prix): static
    {
        $this->unite_prix = $unite_prix;

        return $this;
    }

    public function getAnneeFabrication(): ?int
    {
        return $this->annee_fabrication;
    }

    public function setAnneeFabrication(?int $annee_fabrication): static
    {
        $this->annee_fabrication = $annee_fabrication;

        return $this;
    }

    public function getProprietaireId(): ?int
    {
        return $this->proprietaire_id;
    }

    public function setProprietaireId(int $proprietaire_id): static
    {
        $this->proprietaire_id = $proprietaire_id;

        return $this;
    }

    public function getDateDebutDisponibilite(): ?\DateTime
    {
        return $this->date_debut_disponibilite;
    }

    public function setDateDebutDisponibilite(?\DateTime $date_debut_disponibilite): static
    {
        $this->date_debut_disponibilite = $date_debut_disponibilite;

        return $this;
    }

    public function getDateFinDisponibilite(): ?\DateTime
    {
        return $this->date_fin_disponibilite;
    }

    public function setDateFinDisponibilite(?\DateTime $date_fin_disponibilite): static
    {
        $this->date_fin_disponibilite = $date_fin_disponibilite;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTime $date_modification): static
    {
        $this->date_modification = $date_modification;

        return $this;
    }

    public function isAvecOperateur(): ?bool
    {
        return $this->avec_operateur;
    }

    public function setAvecOperateur(?bool $avec_operateur): static
    {
        $this->avec_operateur = $avec_operateur;

        return $this;
    }

    public function isAssuranceIncluse(): ?bool
    {
        return $this->assurance_incluse;
    }

    public function setAssuranceIncluse(?bool $assurance_incluse): static
    {
        $this->assurance_incluse = $assurance_incluse;

        return $this;
    }

    public function getConditionsLocation(): ?string
    {
        return $this->conditions_location;
    }

    public function setConditionsLocation(?string $conditions_location): static
    {
        $this->conditions_location = $conditions_location;

        return $this;
    }

    public function getQuantiteDisponible(): ?int
    {
        return $this->quantite_disponible;
    }

    public function setQuantiteDisponible(?int $quantite_disponible): static
    {
        $this->quantite_disponible = $quantite_disponible;

        return $this;
    }

    public function getUniteQuantite(): ?string
    {
        return $this->unite_quantite;
    }

    public function setUniteQuantite(?string $unite_quantite): static
    {
        $this->unite_quantite = $unite_quantite;

        return $this;
    }

}
