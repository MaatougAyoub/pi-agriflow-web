<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProduitPhytosanitaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitPhytosanitaireRepository::class)]
#[ORM\Table(name: 'produits_phytosanitaires')]
class ProduitPhytosanitaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_produit')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_produit', length: 100)]
    private string $nomProduit = '';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dosage = null;

    #[ORM\Column(name: 'frequence_application', length: 100, nullable: true)]
    private ?string $frequenceApplication = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $remarques = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;

        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(?string $dosage): static
    {
        $this->dosage = $dosage;

        return $this;
    }

    public function getFrequenceApplication(): ?string
    {
        return $this->frequenceApplication;
    }

    public function setFrequenceApplication(?string $frequenceApplication): static
    {
        $this->frequenceApplication = $frequenceApplication;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): static
    {
        $this->remarques = $remarques;

        return $this;
    }
}
