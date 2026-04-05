<?php

namespace App\Entity;

use App\Repository\CultureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CultureRepository::class)]
#[ORM\Table(name: 'cultures')]
class Culture
{
    public const TYPES = [
        'BLE'            => 'Blé',
        'MAIS'           => 'Maïs',
        'TOMATE'         => 'Tomate',
        'POMME_DE_TERRE' => 'Pomme de terre',
        'OLIVE'          => 'Olivier',
        'VIGNE'          => 'Vigne',
        'FRAISE'         => 'Fraise',
        'AUTRE'          => 'Autre',
    ];

    private const BESOIN_BASE = [
        'BLE'            => 4.5,
        'MAIS'           => 6.0,
        'TOMATE'         => 5.5,
        'POMME_DE_TERRE' => 4.0,
        'OLIVE'          => 2.5,
        'VIGNE'          => 3.0,
        'FRAISE'         => 3.5,
        'AUTRE'          => 3.5,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id')]
    private ?int $id = null;

    #[ORM\Column(name: 'nom', length: 100)]
    private ?string $nom = null;

    #[ORM\Column(name: 'type_culture', length: 50)]
    private ?string $typeCulture = 'AUTRE';

    #[ORM\Column(name: 'superficie')]
    private ?float $superficie = 1.0;

    
    #[ORM\OneToMany(mappedBy: 'culture', targetEntity: PlanIrrigation::class, cascade: ['remove'])]
    private Collection $plansIrrigation;

    public function __construct()
    {
        $this->plansIrrigation = new ArrayCollection();
    }

    public function calculerBesoinEau(): float
    {
        $base = self::BESOIN_BASE[$this->typeCulture] ?? 3.5;
        return round($base * ($this->superficie ?? 1.0) * 7, 2);
    }

    public function getLibelleType(): string
    {
        return self::TYPES[$this->typeCulture] ?? $this->typeCulture;
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $v): static { $this->nom = $v; return $this; }
    public function getTypeCulture(): ?string { return $this->typeCulture; }
    public function setTypeCulture(string $v): static { $this->typeCulture = $v; return $this; }
    public function getSuperficie(): ?float { return $this->superficie; }
    public function setSuperficie(float $v): static { $this->superficie = $v; return $this; }
    public function getPlansIrrigation(): Collection { return $this->plansIrrigation; }
    public function __toString(): string { return $this->nom ?? ''; }
}