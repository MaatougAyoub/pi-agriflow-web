<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlansIrrigationJourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlansIrrigationJourRepository::class)]
#[ORM\Table(name: 'plans_irrigation_jour')]
class PlansIrrigationJour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'plan_id')]
    private int $planId = 0;

    #[ORM\Column(length: 10)]
    private string $jour = '';

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $eauMm = 0.0;

    #[ORM\Column(name: 'temps_min', nullable: true)]
    private ?int $tempsMin = 0;

    #[ORM\Column(name: 'temp_c', type: Types::FLOAT, nullable: true)]
    private ?float $tempC = 0.0;

    #[ORM\Column(name: 'semaine_debut', type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $semaineDebut = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $humidite = 0.0;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $pluie = 0.0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function setPlanId(int $planId): static
    {
        $this->planId = $planId;

        return $this;
    }

    public function getJour(): string
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
        return $this->eauMm;
    }

    public function setEauMm(?float $eauMm): static
    {
        $this->eauMm = $eauMm;

        return $this;
    }

    public function getTempsMin(): ?int
    {
        return $this->tempsMin;
    }

    public function setTempsMin(?int $tempsMin): static
    {
        $this->tempsMin = $tempsMin;

        return $this;
    }

    public function getTempC(): ?float
    {
        return $this->tempC;
    }

    public function setTempC(?float $tempC): static
    {
        $this->tempC = $tempC;

        return $this;
    }

    public function getSemaineDebut(): ?\DateTimeInterface
    {
        return $this->semaineDebut;
    }

    public function setSemaineDebut(?\DateTimeInterface $semaineDebut): static
    {
        $this->semaineDebut = $semaineDebut;

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
