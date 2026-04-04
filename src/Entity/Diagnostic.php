<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DiagnosticRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticRepository::class)]
#[ORM\Table(name: 'diagnosti')]
class Diagnostic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_diagnostic')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_agriculteur')]
    private int $idAgriculteur = 0;

    #[ORM\Column(name: 'nom_culture', length: 100)]
    private string $nomCulture = '';

    #[ORM\Column(name: 'image_path', length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $description = '';

    #[ORM\Column(name: 'reponse_expert', type: Types::TEXT, nullable: true)]
    private ?string $reponseExpert = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $statut = 'En attente';

    #[ORM\Column(name: 'date_envoi', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnvoi = null;

    #[ORM\Column(name: 'date_reponse', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateReponse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAgriculteur(): int
    {
        return $this->idAgriculteur;
    }

    public function setIdAgriculteur(int $idAgriculteur): static
    {
        $this->idAgriculteur = $idAgriculteur;

        return $this;
    }

    public function getNomCulture(): string
    {
        return $this->nomCulture;
    }

    public function setNomCulture(string $nomCulture): static
    {
        $this->nomCulture = $nomCulture;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReponseExpert(): ?string
    {
        return $this->reponseExpert;
    }

    public function setReponseExpert(?string $reponseExpert): static
    {
        $this->reponseExpert = $reponseExpert;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateReponse(): ?\DateTimeInterface
    {
        return $this->dateReponse;
    }

    public function setDateReponse(?\DateTimeInterface $dateReponse): static
    {
        $this->dateReponse = $dateReponse;

        return $this;
    }
}
