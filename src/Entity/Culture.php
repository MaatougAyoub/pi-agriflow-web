<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CultureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CultureRepository::class)]
#[ORM\Table(name: 'cultures')]
class Culture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'parcelle_id')]
    private int $parcelleId = 0;

    #[ORM\Column(name: 'proprietaire_id')]
    private int $proprietaireId = 0;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(name: 'type_culture', length: 20, nullable: true)]
    private ?string $typeCulture = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $superficie = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $etat = null;

    #[ORM\Column(name: 'date_recolte', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRecolte = null;

    #[ORM\Column(name: 'recolte_estime', type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $recolteEstime = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'id_acheteur', nullable: true)]
    private ?int $idAcheteur = null;

    #[ORM\Column(name: 'date_vente', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVente = null;

    #[ORM\Column(name: 'date_publication', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(name: 'prix_vente', nullable: true)]
    private ?float $prixVente = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParcelleId(): int
    {
        return $this->parcelleId;
    }

    public function setParcelleId(int $parcelleId): static
    {
        $this->parcelleId = $parcelleId;

        return $this;
    }

    public function getProprietaireId(): int
    {
        return $this->proprietaireId;
    }

    public function setProprietaireId(int $proprietaireId): static
    {
        $this->proprietaireId = $proprietaireId;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTypeCulture(): ?string
    {
        return $this->typeCulture;
    }

    public function setTypeCulture(?string $typeCulture): static
    {
        $this->typeCulture = $typeCulture;

        return $this;
    }

    public function getSuperficie(): ?string
    {
        return $this->superficie;
    }

    public function setSuperficie(?string $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateRecolte(): ?\DateTimeInterface
    {
        return $this->dateRecolte;
    }

    public function setDateRecolte(?\DateTimeInterface $dateRecolte): static
    {
        $this->dateRecolte = $dateRecolte;

        return $this;
    }

    public function getRecolteEstime(): ?string
    {
        return $this->recolteEstime;
    }

    public function setRecolteEstime(?string $recolteEstime): static
    {
        $this->recolteEstime = $recolteEstime;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getIdAcheteur(): ?int
    {
        return $this->idAcheteur;
    }

    public function setIdAcheteur(?int $idAcheteur): static
    {
        $this->idAcheteur = $idAcheteur;

        return $this;
    }

    public function getDateVente(): ?\DateTimeInterface
    {
        return $this->dateVente;
    }

    public function setDateVente(?\DateTimeInterface $dateVente): static
    {
        $this->dateVente = $dateVente;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(?float $prixVente): static
    {
        $this->prixVente = $prixVente;

        return $this;
    }
}
