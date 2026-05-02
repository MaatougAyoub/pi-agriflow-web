<?php

namespace App\Entity;

use App\Repository\CollabRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabRequestRepository::class)]
#[ORM\Table(name: 'collab_requests')]
#[ORM\HasLifecycleCallbacks]
class CollabRequest
{
    /** @deprecated Utilisez APPROVED — conservé pour les tests / ancien code */
    public const STATUS_OPEN = 'APPROVED';

    /** @deprecated Utilisez REJECTED — conservé pour les tests / ancien code */
    public const STATUS_CLOSED = 'REJECTED';

    public const STATUSES = [
        'En attente' => 'PENDING',
        'Approuvée' => 'APPROVED',
        'Refusée' => 'REJECTED',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    /** @phpstan-ignore-next-line */
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\Positive(message: 'Le nombre de personnes doit être au moins 1.')]
    private int $neededPeople = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\PositiveOrZero(message: 'Le salaire ne peut pas être négatif.')]
    private ?string $salary = '0.00';

    #[ORM\Column(length: 50, options: ['default' => 'PENDING'])]
    private string $status = 'PENDING';

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'requester_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $requester = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    /** @var Collection<int, CollabApplication> */
    #[ORM\OneToMany(targetEntity: CollabApplication::class, mappedBy: 'request', cascade: ['remove'], orphanRemoval: true)]
    private Collection $applications;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude !== null ? (float) $this->latitude : null;
    }

    public function setLatitude(float|string|null $latitude): static
    {
        $this->latitude = $latitude !== null ? (string) $latitude : null;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude !== null ? (float) $this->longitude : null;
    }

    public function setLongitude(float|string|null $longitude): static
    {
        $this->longitude = $longitude !== null ? (string) $longitude : null;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNeededPeople(): int
    {
        return $this->neededPeople;
    }

    public function setNeededPeople(int $neededPeople): static
    {
        $this->neededPeople = $neededPeople;

        return $this;
    }

    public function getSalary(): ?float
    {
        return $this->salary !== null ? (float) $this->salary : null;
    }

    public function getSalaryPerDay(): ?float
    {
        return $this->salary !== null ? (float) $this->salary : null;
    }

    public function setSalary(float|string|null $salary): static
    {
        $this->salary = $salary !== null ? (string) $salary : null;

        return $this;
    }

    public function setSalaryPerDay(float|string|null $salaryPerDay): static
    {
        $this->salary = $salaryPerDay !== null ? (string) $salaryPerDay : null;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getRequester(): ?Utilisateur
    {
        return $this->requester;
    }

    public function setRequester(?Utilisateur $requester): static
    {
        $this->requester = $requester;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection<int, CollabApplication>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    /** Demande publiée et ouverte aux candidatures (workflow AgriFlow). */
    public function isOpen(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isExpired(): bool
    {
        if ($this->endDate === null) {
            return false;
        }

        $today = new \DateTime('today');

        return $this->endDate < $today;
    }
}
