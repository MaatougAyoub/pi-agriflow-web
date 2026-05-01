<?php

namespace App\Entity;

use App\Repository\PlansIrrigationJourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlansIrrigationJourRepository::class)]
#[ORM\Table(name: 'plans_irrigation_jour')]
class PlansIrrigationJour
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $plan_id = null;

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

    #[ORM\ManyToOne(targetEntity: PlansIrrigation::class, inversedBy: 'jours')]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'plan_id', nullable: false)]
    private ?PlansIrrigation $plan = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getPlanId(): ?int
    {
        return $this->plan_id;
    }

    public function setPlanId(int $plan_id): static
    {
        $this->plan_id = $plan_id;

        return $this;
    }

    public function getPlan(): ?PlansIrrigation
    {
        return $this->plan;
    }

    public function setPlan(?PlansIrrigation $plan): static
    {
        $this->plan = $plan;
        $this->plan_id = $plan?->getPlanId();

        return $this;
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
