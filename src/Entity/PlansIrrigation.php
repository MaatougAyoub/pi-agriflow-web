<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\PlansIrrigationRepository;

#[ORM\Entity(repositoryClass: PlansIrrigationRepository::class)]
#[ORM\Table(name: 'plans_irrigation')]
class PlansIrrigation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $id_culture = null;

    public function getId_culture(): ?int
    {
        return $this->id_culture;
    }

    public function setId_culture(?int $id_culture): self
    {
        $this->id_culture = $id_culture;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom_culture = null;

    public function getNom_culture(): ?string
    {
        return $this->nom_culture;
    }

    public function setNom_culture(?string $nom_culture): self
    {
        $this->nom_culture = $nom_culture;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_demande = null;

    public function getDate_demande(): ?\DateTimeInterface
    {
        return $this->date_demande;
    }

    public function setDate_demande(?\DateTimeInterface $date_demande): self
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $volume_eau_propose = null;

    public function getVolume_eau_propose(): ?float
    {
        return $this->volume_eau_propose;
    }

    public function setVolume_eau_propose(?float $volume_eau_propose): self
    {
        $this->volume_eau_propose = $volume_eau_propose;
        return $this;
    }

    #[ORM\Column(type: 'time', nullable: true)]
    private ?string $temp_irrigation = null;

    public function getTemp_irrigation(): ?string
    {
        return $this->temp_irrigation;
    }

    public function setTemp_irrigation(?string $temp_irrigation): self
    {
        $this->temp_irrigation = $temp_irrigation;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $temp = null;

    public function getTemp(): ?\DateTimeInterface
    {
        return $this->temp;
    }

    public function setTemp(?\DateTimeInterface $temp): self
    {
        $this->temp = $temp;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $donnees_meteo_json = null;

    public function getDonnees_meteo_json(): ?string
    {
        return $this->donnees_meteo_json;
    }

    public function setDonnees_meteo_json(?string $donnees_meteo_json): self
    {
        $this->donnees_meteo_json = $donnees_meteo_json;
        return $this;
    }

    public function getPlanId(): ?int
    {
        return $this->plan_id;
    }

    public function getIdCulture(): ?int
    {
        return $this->id_culture;
    }

    public function setIdCulture(?int $id_culture): static
    {
        $this->id_culture = $id_culture;

        return $this;
    }

    public function getNomCulture(): ?string
    {
        return $this->nom_culture;
    }

    public function setNomCulture(?string $nom_culture): static
    {
        $this->nom_culture = $nom_culture;

        return $this;
    }

    public function getDateDemande(): ?\DateTime
    {
        return $this->date_demande;
    }

    public function setDateDemande(?\DateTime $date_demande): static
    {
        $this->date_demande = $date_demande;

        return $this;
    }

    public function getVolumeEauPropose(): ?float
    {
        return $this->volume_eau_propose;
    }

    public function setVolumeEauPropose(?float $volume_eau_propose): static
    {
        $this->volume_eau_propose = $volume_eau_propose;

        return $this;
    }

    public function getTempIrrigation(): ?\DateTime
    {
        return $this->temp_irrigation;
    }

    public function setTempIrrigation(?\DateTime $temp_irrigation): static
    {
        $this->temp_irrigation = $temp_irrigation;

        return $this;
    }

    public function getDonneesMeteoJson(): ?string
    {
        return $this->donnees_meteo_json;
    }

    public function setDonneesMeteoJson(?string $donnees_meteo_json): static
    {
        $this->donnees_meteo_json = $donnees_meteo_json;

        return $this;
    }

}
