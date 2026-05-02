<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PlansIrrigationJourRepository;

#[ORM\Entity(repositoryClass: PlansIrrigationJourRepository::class)]
#[ORM\Table(name: 'plans_irrigation_jour')]
class PlansIrrigationJour
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]   // ← Auto-incrément ajouté
    #[ORM\Column(type: 'integer')]
    /** @phpstan-ignore-next-line */
    private ?int $id = null;


    #[ORM\ManyToOne(targetEntity: PlansIrrigation::class, inversedBy: 'jours')]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'plan_id', nullable: false)]
    private ?PlansIrrigation $plan = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $jour = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $eau_mm = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $temps_min = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $temp_c = null;

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $semaine_debut = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $humidite = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $pluie = null;

    // --- Getters / Setters (versions camelCase utilisées dans le contrôleur) ---

    public function getId(): ?int
    {
        return $this->id;
    }

    // Pas de setId() car auto-généré

    public function getPlan(): ?PlansIrrigation
    {
        return $this->plan;
    }

    public function setPlan(?PlansIrrigation $plan): static
    {
        $this->plan = $plan;
        return $this;
    }

    public function getPlanId(): ?int
    {
        return $this->plan?->getPlanId();
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): static
    {
        $this->jour = $jour;
        return $this;
    }

    public function getEauMm(): ?float
    {
        return $this->eau_mm;
    }

    public function setEauMm(?float $eau_mm): static
    {
        $this->eau_mm = $eau_mm;
        return $this;
    }

    public function getTempsMin(): ?int
    {
        return $this->temps_min;
    }

    public function setTempsMin(?int $temps_min): static
    {
        $this->temps_min = $temps_min;
        return $this;
    }

    public function getTempC(): ?float
    {
        return $this->temp_c;
    }

    public function setTempC(?float $temp_c): static
    {
        $this->temp_c = $temp_c;
        return $this;
    }

    public function getSemaineDebut(): ?\DateTimeInterface
    {
        return $this->semaine_debut;
    }

    public function setSemaineDebut(\DateTimeInterface $semaine_debut): static
    {
        $this->semaine_debut = $semaine_debut;
        return $this;
    }

    public function getHumidite(): ?float
    {
        return $this->humidite;
    }

    public function setHumidite(?float $humidite): static
    {
        $this->humidite = $humidite;
        return $this;
    }

    public function getPluie(): ?float
    {
        return $this->pluie;
    }

    public function setPluie(?float $pluie): static
    {
        $this->pluie = $pluie;
        return $this;
    }
}