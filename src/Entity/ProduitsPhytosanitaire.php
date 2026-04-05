<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ProduitsPhytosanitaireRepository;

#[ORM\Entity(repositoryClass: ProduitsPhytosanitaireRepository::class)]
#[ORM\Table(name: 'produits_phytosanitaires')]
class ProduitsPhytosanitaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_produit = null;

    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function setId_produit(int $id_produit): self
    {
        $this->id_produit = $id_produit;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom_produit = null;

    public function getNom_produit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNom_produit(string $nom_produit): self
    {
        $this->nom_produit = $nom_produit;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $dosage = null;

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(?string $dosage): self
    {
        $this->dosage = $dosage;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $frequence_application = null;

    public function getFrequence_application(): ?string
    {
        return $this->frequence_application;
    }

    public function setFrequence_application(?string $frequence_application): self
    {
        $this->frequence_application = $frequence_application;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $remarques = null;

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;
        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function getNomProduit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNomProduit(string $nom_produit): static
    {
        $this->nom_produit = $nom_produit;

        return $this;
    }

    public function getFrequenceApplication(): ?string
    {
        return $this->frequence_application;
    }

    public function setFrequenceApplication(?string $frequence_application): static
    {
        $this->frequence_application = $frequence_application;

        return $this;
    }

}
