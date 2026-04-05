<?php

namespace App\Entity;

use App\Repository\ProduitPhytosanitaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitPhytosanitaireRepository::class)]
#[ORM\Table(name: 'produits_phytosanitaires')]
class ProduitPhytosanitaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_produit')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom_produit', length: 100, nullable: true)]
    private ?string $nomProduit = null;

    #[ORM\Column(name: 'dosage', length: 100, nullable: true)]
    private ?string $dosage = null;

    #[ORM\Column(name: 'frequence_application', length: 100, nullable: true)]
    private ?string $frequenceApplication = null;

    #[ORM\Column(name: 'remarques', type: 'text', nullable: true)]
    private ?string $remarques = null;

    public function getId(): ?int { return $this->id; }
    public function getNomProduit(): ?string { return $this->nomProduit; }
    public function setNomProduit(string $v): static { $this->nomProduit = $v; return $this; }
    public function getDosage(): ?string { return $this->dosage; }
    public function setDosage(?string $v): static { $this->dosage = $v; return $this; }
    public function getFrequenceApplication(): ?string { return $this->frequenceApplication; }
    public function setFrequenceApplication(?string $v): static { $this->frequenceApplication = $v; return $this; }
    public function getRemarques(): ?string { return $this->remarques; }
    public function setRemarques(?string $v): static { $this->remarques = $v; return $this; }
}