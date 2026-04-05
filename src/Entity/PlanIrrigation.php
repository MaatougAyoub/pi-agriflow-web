<?php

namespace App\Entity;

use App\Repository\PlanIrrigationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanIrrigationRepository::class)]
#[ORM\Table(name: 'plans_irrigation')]
class PlanIrrigation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'plan_id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Culture::class, inversedBy: 'plansIrrigation')]
    #[ORM\JoinColumn(name: 'id_culture', referencedColumnName: 'id', nullable: true)]
    private ?Culture $culture = null;

    #[ORM\Column(name: 'volume_eau_propose', nullable: true)]
    private ?float $besoinEau = 0.0;

    #[ORM\Column(name: 'nom_culture', length: 100, nullable: true)]
    private ?string $nomCulture = null;

    #[ORM\Column(name: 'statut', length: 50, nullable: true)]
    private ?string $statut = 'brouillon';

    #[ORM\Column(name: 'date_demande', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToMany(mappedBy: 'planIrrigation', targetEntity: PlanIrrigationJour::class, cascade: ['persist', 'remove'])]
    private Collection $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getCulture(): ?Culture { return $this->culture; }
    public function setCulture(?Culture $culture): static { $this->culture = $culture; return $this; }
    public function getBesoinEau(): ?float { return $this->besoinEau; }
    public function setBesoinEau(float $v): static { $this->besoinEau = $v; return $this; }
    public function getNomCulture(): ?string { return $this->nomCulture; }
    public function setNomCulture(?string $v): static { $this->nomCulture = $v; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $v): static { $this->statut = $v; return $this; }
    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $v): static { $this->dateCreation = $v; return $this; }
    public function getJours(): Collection { return $this->jours; }

    public function addJour(PlanIrrigationJour $jour): static
    {
        if (!$this->jours->contains($jour)) {
            $this->jours->add($jour);
            $jour->setPlanIrrigation($this);
        }
        return $this;
    }
}