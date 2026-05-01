<?php

namespace App\Entity;

use App\Repository\PlansIrrigationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlansIrrigationRepository::class)]
#[ORM\Table(name: 'plans_irrigation')]
class PlansIrrigation
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private ?int $plan_id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $culture_id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nom_culture = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_demande = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $volume_eau_propose = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $temp_irrigation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $temp = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $donnees_meteo_json = null;

    #[ORM\ManyToOne(targetEntity: Culture::class)]
    #[ORM\JoinColumn(name: 'culture_id', referencedColumnName: 'id')]
    private ?Culture $culture = null;

    /**
     * @var Collection<int, PlansIrrigationJour>
     */
    #[ORM\OneToMany(mappedBy: 'plan', targetEntity: PlansIrrigationJour::class)]
    private Collection $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
    }

    public function getPlanId(): ?int
    {
        return $this->plan_id;
    }

    public function setPlanId(int $plan_id): self
    {
        $this->plan_id = $plan_id;

        return $this;
    }

    public function getIdCulture(): ?int
    {
        return $this->culture_id;
    }

    public function setIdCulture(?int $culture_id): self
    {
        $this->culture_id = $culture_id;

        return $this;
    }

    public function getNomCulture(): ?string
    {
        return $this->nom_culture;
    }

    public function setNomCulture(?string $nom_culture): self
    {
        $this->nom_culture = $nom_culture;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->date_demande;
    }

    public function setDateDemande(?\DateTimeInterface $date_demande): self
    {
        $this->date_demande = $date_demande;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getVolumeEauPropose(): ?float
    {
        return $this->volume_eau_propose;
    }

    public function setVolumeEauPropose(?float $volume_eau_propose): self
    {
        $this->volume_eau_propose = $volume_eau_propose;

        return $this;
    }

    public function getTempIrrigation(): ?\DateTimeInterface
    {
        return $this->temp_irrigation;
    }

    public function setTempIrrigation(?\DateTimeInterface $temp_irrigation): self
    {
        $this->temp_irrigation = $temp_irrigation;

        return $this;
    }

    public function getTemp(): ?\DateTimeInterface
    {
        return $this->temp;
    }

    public function setTemp(?\DateTimeInterface $temp): self
    {
        $this->temp = $temp;

        return $this;
    }

    public function getDonneesMeteoJson(): ?string
    {
        return $this->donnees_meteo_json;
    }

    public function setDonneesMeteoJson(?string $donnees_meteo_json): self
    {
        $this->donnees_meteo_json = $donnees_meteo_json;

        return $this;
    }

    public function getCulture(): ?Culture
    {
        return $this->culture;
    }

    public function setCulture(?Culture $culture): self
    {
        $this->culture = $culture;
        $this->culture_id = $culture?->getId();

        return $this;
    }

    /**
     * @return Collection<int, PlansIrrigationJour>
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(PlansIrrigationJour $jour): self
    {
        if (!$this->jours->contains($jour)) {
            $this->jours[] = $jour;
            $jour->setPlan($this);
        }

        return $this;
    }
}
