<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ParcelleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParcelleRepository::class)]
#[ORM\Table(name: 'parcelle')]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'agriculteur_id')]
    private int $agriculteurId = 0;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $superficie = null;

    #[ORM\Column(name: 'type_terre', length: 20, nullable: true)]
    private ?string $typeTerre = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAgriculteurId(): int
    {
        return $this->agriculteurId;
    }

    public function setAgriculteurId(int $agriculteurId): static
    {
        $this->agriculteurId = $agriculteurId;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?string
    {
        return $this->superficie;
    }

    public function setSuperficie(?string $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getTypeTerre(): ?string
    {
        return $this->typeTerre;
    }

    public function setTypeTerre(?string $typeTerre): static
    {
        $this->typeTerre = $typeTerre;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }
}
