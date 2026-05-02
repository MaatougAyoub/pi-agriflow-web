<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParcelleRepository;

#[ORM\Entity(repositoryClass: ParcelleRepository::class)]
#[ORM\Table(name: 'parcelle')]
#[ORM\HasLifecycleCallbacks]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'agriculteur_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $agriculteur_id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $superficie = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $type_terre = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $localisation = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getAgriculteur_id(): ?int
    {
        return $this->getAgriculteurId();
    }

    public function setAgriculteur_id(int $agriculteur_id): self
    {
        return $this->setAgriculteurId($agriculteur_id);
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(?float $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getType_terre(): ?string
    {
        return $this->type_terre;
    }

    public function setType_terre(?string $type_terre): self
    {
        $this->type_terre = $type_terre;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    protected function setDate_creation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getAgriculteur(): ?Utilisateur
    {
        return $this->agriculteur_id;
    }

    public function setAgriculteur(?Utilisateur $agriculteur): static
    {
        $this->agriculteur_id = $agriculteur;

        return $this;
    }

    public function getAgriculteurId(): ?int
    {
        return $this->agriculteur_id?->getId();
    }

    public function setAgriculteurId(int $agriculteur_id): static
    {
        $agriculteur = new Utilisateur();
        $agriculteur->setId($agriculteur_id);
        $this->agriculteur_id = $agriculteur;

        return $this;
    }

    public function getTypeTerre(): ?string
    {
        return $this->type_terre;
    }

    public function setTypeTerre(?string $type_terre): static
    {
        $this->type_terre = $type_terre;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    protected function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    #[ORM\PrePersist]
    public function initializeDateCreation(): void
    {
        if (null === $this->date_creation) {
            $this->date_creation = new \DateTimeImmutable();
        }
    }
}
