<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CultureRepository;

#[ORM\Entity(repositoryClass: CultureRepository::class)]
#[ORM\Table(name: 'cultures')]
class Culture
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

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $parcelle_id = null;

    public function getParcelle_id(): ?int
    {
        return $this->parcelle_id;
    }

    public function setParcelle_id(int $parcelle_id): self
    {
        $this->parcelle_id = $parcelle_id;
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type_culture = null;

    public function getType_culture(): ?string
    {
        return $this->type_culture;
    }

    public function setType_culture(?string $type_culture): self
    {
        $this->type_culture = $type_culture;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $superficie = null;

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(?float $superficie): self
    {
        $this->superficie = $superficie;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $etat = null;

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_recolte = null;

    public function getDate_recolte(): ?\DateTimeInterface
    {
        return $this->date_recolte;
    }

    public function setDate_recolte(?\DateTimeInterface $date_recolte): self
    {
        $this->date_recolte = $date_recolte;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $recolte_estime = null;

    public function getRecolte_estime(): ?float
    {
        return $this->recolte_estime;
    }

    public function setRecolte_estime(?float $recolte_estime): self
    {
        $this->recolte_estime = $recolte_estime;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDate_creation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'cultures')]
    #[ORM\JoinColumn(name: 'id_acheteur', referencedColumnName: 'id')]
    private ?Utilisateur $utilisateur = null;

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_vente = null;

    public function getDate_vente(): ?\DateTimeInterface
    {
        return $this->date_vente;
    }

    public function setDate_vente(?\DateTimeInterface $date_vente): self
    {
        $this->date_vente = $date_vente;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_publication = null;

    public function getDate_publication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDate_publication(?\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $prix_vente = null;

    public function getPrix_vente(): ?float
    {
        return $this->prix_vente;
    }

    public function setPrix_vente(?float $prix_vente): self
    {
        $this->prix_vente = $prix_vente;
        return $this;
    }

    public function getParcelleId(): ?int
    {
        return $this->parcelle_id;
    }

    public function setParcelleId(int $parcelle_id): static
    {
        $this->parcelle_id = $parcelle_id;

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

    public function getTypeCulture(): ?string
    {
        return $this->type_culture;
    }

    public function setTypeCulture(?string $type_culture): static
    {
        $this->type_culture = $type_culture;

        return $this;
    }

    public function getDateRecolte(): ?\DateTime
    {
        return $this->date_recolte;
    }

    public function setDateRecolte(?\DateTime $date_recolte): static
    {
        $this->date_recolte = $date_recolte;

        return $this;
    }

    public function getRecolteEstime(): ?string
    {
        return $this->recolte_estime;
    }

    public function setRecolteEstime(?string $recolte_estime): static
    {
        $this->recolte_estime = $recolte_estime;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateVente(): ?\DateTime
    {
        return $this->date_vente;
    }

    public function setDateVente(?\DateTime $date_vente): static
    {
        $this->date_vente = $date_vente;

        return $this;
    }

    public function getDatePublication(): ?\DateTime
    {
        return $this->date_publication;
    }

    public function setDatePublication(?\DateTime $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getPrixVente(): ?string
    {
        return $this->prix_vente;
    }

    public function setPrixVente(?string $prix_vente): static
    {
        $this->prix_vente = $prix_vente;

        return $this;
    }

}
