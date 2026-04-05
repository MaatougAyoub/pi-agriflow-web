<?php

namespace App\Entity;

use App\Repository\PlanIrrigationJourRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanIrrigationJourRepository::class)]
#[ORM\Table(name: 'plans_irrigation_jour')]
class PlanIrrigationJour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PlanIrrigation::class, inversedBy: 'jours')]
    #[ORM\JoinColumn(name: 'plan_id', referencedColumnName: 'plan_id', nullable: false)]
    private ?PlanIrrigation $planIrrigation = null;

    #[ORM\Column(name: 'jour', type: 'integer', nullable: true)]
    private ?int $jour = null;

    #[ORM\Column(name: 'eau_mm', nullable: true)]
    private ?float $eauMm = 0.0;

    #[ORM\Column(name: 'temps_min', nullable: true)]
    private ?int $dureeMin = 0;

    #[ORM\Column(name: 'temp_c', nullable: true)]
    private ?float $temperature = null;

    #[ORM\Column(name: 'humidite', nullable: true)]
    private ?float $humidite = null;

    #[ORM\Column(name: 'pluie', nullable: true)]
    private ?float $pluieMm = null;

    #[ORM\Column(name: 'semaine_debut', nullable: true)]
    private ?int $dateSemaine = null;

    public function getId(): ?int { return $this->id; }
    public function getPlanIrrigation(): ?PlanIrrigation { return $this->planIrrigation; }
    public function setPlanIrrigation(?PlanIrrigation $plan): static { $this->planIrrigation = $plan; return $this; }
    public function getJour(): ?int { return $this->jour; }
    public function setJour(int $jour): static { $this->jour = $jour; return $this; }
    public function getEauMm(): ?float { return $this->eauMm; }
    public function setEauMm(float $v): static { $this->eauMm = $v; return $this; }
    public function getDureeMin(): ?int { return $this->dureeMin; }
    public function setDureeMin(int $v): static { $this->dureeMin = $v; return $this; }
    public function getTemperature(): ?float { return $this->temperature; }
    public function setTemperature(?float $v): static { $this->temperature = $v; return $this; }
    public function getHumidite(): ?float { return $this->humidite; }
    public function setHumidite(?float $v): static { $this->humidite = $v; return $this; }
    public function getPluieMm(): ?float { return $this->pluieMm; }
    public function setPluieMm(?float $v): static { $this->pluieMm = $v; return $this; }
    public function getDateSemaine(): ?int { return $this->dateSemaine; }
    public function setDateSemaine(?int $v): static { $this->dateSemaine = $v; return $this; }
}