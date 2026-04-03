<?php

namespace App\Entity;

use App\Enum\TypeTerre;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Parcelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $superficie = null;

    #[ORM\Column]
    private ?int $agriculteurId = null;

    #[ORM\Column(enumType: TypeTerre::class)]
    private TypeTerre $typeTerre = TypeTerre::AUTRE;

    #[ORM\Column]
    private \DateTimeImmutable $dateCreation;

    /**
     * @var Collection<int, Culture>
     */
    #[ORM\OneToMany(mappedBy: 'parcelle', targetEntity: Culture::class, orphanRemoval: false)]
    private Collection $cultures;

    public function __construct()
    {
        $this->cultures = new ArrayCollection();
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getAgriculteurId(): ?int
    {
        return $this->agriculteurId;
    }

    public function setAgriculteurId(int $agriculteurId): static
    {
        $this->agriculteurId = $agriculteurId;

        return $this;
    }

    public function getTypeTerre(): TypeTerre
    {
        return $this->typeTerre;
    }

    public function setTypeTerre(TypeTerre $typeTerre): static
    {
        $this->typeTerre = $typeTerre;

        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, Culture>
     */
    public function getCultures(): Collection
    {
        return $this->cultures;
    }

    public function addCulture(Culture $culture): static
    {
        if (!$this->cultures->contains($culture)) {
            $this->cultures->add($culture);
            $culture->setParcelle($this);
        }

        return $this;
    }

public function removeCulture(Culture $culture): static
{
    $this->cultures->removeElement($culture);

    return $this;
}
}
