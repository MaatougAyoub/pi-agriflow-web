<?php

namespace App\Entity;

use App\Repository\DiagnosticRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticRepository::class)]
#[ORM\Table(name: 'diagnosti')]
class Diagnostic
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_diagnostic')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_agriculteur', nullable: true)]
    private ?int $idAgriculteur = null;

    #[ORM\Column(name: 'nom_culture', length: 100, nullable: true)]
    private ?string $nomCulture = null;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(name: 'image_path', length: 255, nullable: true)]
    private ?string $imagePath = null;

    #[ORM\Column(name: 'reponse_expert', type: 'text', nullable: true)]
    private ?string $reponseExpert = null;

    #[ORM\Column(name: 'statut', length: 50, nullable: true)]
    private ?string $statut = 'en_attente';

    #[ORM\Column(name: 'date_envoi', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateEnvoi = null;

    public function __construct()
    {
        $this->dateEnvoi = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getIdAgriculteur(): ?int { return $this->idAgriculteur; }
    public function setIdAgriculteur(int $v): static { $this->idAgriculteur = $v; return $this; }
    public function getNomCulture(): ?string { return $this->nomCulture; }
    public function setNomCulture(string $v): static { $this->nomCulture = $v; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $v): static { $this->description = $v; return $this; }
    public function getImagePath(): ?string { return $this->imagePath; }
    public function setImagePath(?string $v): static { $this->imagePath = $v; return $this; }
    public function getReponseExpert(): ?string { return $this->reponseExpert; }
    public function setReponseExpert(?string $v): static { $this->reponseExpert = $v; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $v): static { $this->statut = $v; return $this; }
    public function getDateEnvoi(): ?\DateTimeInterface { return $this->dateEnvoi; }
    public function setDateEnvoi(\DateTimeInterface $v): static { $this->dateEnvoi = $v; return $this; }
}