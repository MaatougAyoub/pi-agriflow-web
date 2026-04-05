<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\PlansIrrigationJourRepository;

#[ORM\Entity(repositoryClass: PlansIrrigationJourRepository::class)]
#[ORM\Table(name: 'plans_irrigation_jour')]
class PlansIrrigationJour
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
    private ?int $plan_id = null;

    public function getPlan_id(): ?int
    {
        return $this->plan_id;
    }

    public function setPlan_id(int $plan_id): self
    {
        $this->plan_id = $plan_id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $jour = null;

    public function getJour(): ?string
    {
        return $this->jour;
    }

    public function setJour(string $jour): self
    {
        $this->jour = $jour;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $eau_mm = null;

    public function getEau_mm(): ?float
    {
        return $this->eau_mm;
    }

    public function setEau_mm(?float $eau_mm): self
    {
        $this->eau_mm = $eau_mm;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $temps_min = null;

    public function getTemps_min(): ?int
    {
        return $this->temps_min;
    }

    public function setTemps_min(?int $temps_min): self
    {
        $this->temps_min = $temps_min;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $temp_c = null;

    public function getTemp_c(): ?float
    {
        return $this->temp_c;
    }

    public function setTemp_c(?float $temp_c): self
    {
        $this->temp_c = $temp_c;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $semaine_debut = null;

    public function getSemaine_debut(): ?\DateTimeInterface
    {
        return $this->semaine_debut;
    }

    public function setSemaine_debut(\DateTimeInterface $semaine_debut): self
    {
        $this->semaine_debut = $semaine_debut;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $humidite = null;

    public function getHumidite(): ?float
    {
        return $this->humidite;
    }

    public function setHumidite(?float $humidite): self
    {
        $this->humidite = $humidite;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $pluie = null;

    public function getPluie(): ?float
    {
        return $this->pluie;
    }

    public function setPluie(?float $pluie): self
    {
        $this->pluie = $pluie;
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

    public function getSemaineDebut(): ?\DateTime
    {
        return $this->semaine_debut;
    }

    public function setSemaineDebut(\DateTime $semaine_debut): static
    {
        $this->semaine_debut = $semaine_debut;

        return $this;
    }

}
