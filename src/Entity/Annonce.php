<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
#[ORM\Table(name: 'annonces')]
#[ORM\HasLifecycleCallbacks]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le titre ne doit pas depasser {{ limit }} caracteres.'
    )]
    #[ORM\Column(length: 150)]
    private ?string $titre = null;

    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 20,
        minMessage: 'La description doit contenir au moins {{ limit }} caracteres.'
    )]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: AnnonceType::class, length: 20)]
    private AnnonceType $type = AnnonceType::LOCATION;

    #[ORM\Column(enumType: AnnonceStatut::class, length: 20)]
    private AnnonceStatut $statut = AnnonceStatut::DISPONIBLE;

    #[Assert\NotBlank(message: 'Le prix est obligatoire.')]
    #[Assert\Positive(message: 'Le prix doit etre strictement positif.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $prix = '0.00';

    #[Assert\NotBlank(message: 'La categorie est obligatoire.')]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120)]
    private ?string $categorie = null;

    #[Assert\NotBlank(message: 'L image est obligatoire.')]
    #[Assert\Url(message: 'L image doit etre une URL valide.')]
    #[ORM\Column(name: 'image_url', length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120)]
    private ?string $localisation = null;

    #[Assert\Positive(message: 'Le proprietaire doit avoir un ID valide.')]
    #[ORM\Column]
    private int $proprietaireId = 1;

    #[Assert\Positive(message: 'La quantite disponible doit etre positive.')]
    #[ORM\Column]
    private int $quantiteDisponible = 1;

    #[Assert\NotBlank(message: 'L unite du prix est obligatoire.')]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20)]
    private string $unitePrix = 'jour';

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'date_modification', type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = trim($titre);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = trim($description);

        return $this;
    }

    public function getType(): AnnonceType
    {
        return $this->type;
    }

    public function setType(AnnonceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut(): AnnonceStatut
    {
        return $this->statut;
    }

    public function setStatut(AnnonceStatut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPrix(): string
    {
        return $this->prix;
    }

    public function getPrixAsFloat(): float
    {
        return (float) $this->prix;
    }

    public function setPrix(string|float|int $prix): self
    {
        $this->prix = number_format((float) $prix, 2, '.', '');

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = trim($categorie);

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = trim($imageUrl);

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = trim($localisation);

        return $this;
    }

    public function getProprietaireId(): int
    {
        return $this->proprietaireId;
    }

    public function setProprietaireId(int $proprietaireId): self
    {
        $this->proprietaireId = $proprietaireId;

        return $this;
    }

    public function getQuantiteDisponible(): int
    {
        return $this->quantiteDisponible;
    }

    public function setQuantiteDisponible(int $quantiteDisponible): self
    {
        $this->quantiteDisponible = $quantiteDisponible;

        return $this;
    }

    public function getUnitePrix(): string
    {
        return $this->unitePrix;
    }

    public function setUnitePrix(string $unitePrix): self
    {
        $this->unitePrix = trim($unitePrix);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setAnnonce($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getAnnonce() === $this) {
                $reservation->setAnnonce(null);
            }
        }

        return $this;
    }

    public function isLocation(): bool
    {
        return $this->type === AnnonceType::LOCATION;
    }

    public function isVente(): bool
    {
        return $this->type === AnnonceType::VENTE;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        // houni n7otou les dates automatiquement bech l code y9a3ed propre
        $now = new \DateTimeImmutable();
        $this->createdAt ??= $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        // kol modification tbadel updatedAt automatique
        $this->updatedAt = new \DateTimeImmutable();
    }
}
