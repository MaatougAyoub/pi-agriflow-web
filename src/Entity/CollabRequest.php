<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CollabRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabRequestRepository::class)]
#[ORM\Table(name: 'collab_requests')]
#[ORM\Index(columns: ['status'], name: 'idx_collab_req_status')]
#[ORM\Index(columns: ['end_date'], name: 'idx_collab_req_end_date')]
#[ORM\HasLifecycleCallbacks]
class CollabRequest
{
    public const STATUS_OPEN   = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_DRAFT  = 'draft';

    public const STATUSES = [
        'Ouverte'  => self::STATUS_OPEN,
        'Fermée'   => self::STATUS_CLOSED,
        'Brouillon' => self::STATUS_DRAFT,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'requester_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $requester = null;

    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 5, max: 150, minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.', maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.')]
    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 20, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    #[ORM\Column(name: 'start_date', type: Types::DATE_MUTABLE)]
    private ?\DateTime $startDate = null;

    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de fin doit être après la date de début.')]
    #[ORM\Column(name: 'end_date', type: Types::DATE_MUTABLE)]
    private ?\DateTime $endDate = null;

    #[Assert\NotNull(message: 'Le nombre de personnes est obligatoire.')]
    #[Assert\Positive(message: 'Le nombre de personnes doit être positif.')]
    #[Assert\LessThanOrEqual(value: 1000, message: 'Le nombre de personnes ne peut pas dépasser {{ compared_value }}.')]
    #[ORM\Column(name: 'needed_people')]
    private ?int $neededPeople = null;

    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Choice(choices: [self::STATUS_OPEN, self::STATUS_CLOSED, self::STATUS_DRAFT], message: 'Statut invalide.')]
    #[ORM\Column(length: 20, options: ['default' => 'open'])]
    private ?string $status = self::STATUS_OPEN;

    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, options: ['default' => 'Non spécifié'])]
    private ?string $location = 'Non spécifié';

    #[Assert\PositiveOrZero(message: 'Le salaire ne peut pas être négatif.')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?string $salary = '0.00';

    #[Assert\PositiveOrZero(message: 'Le salaire journalier ne peut pas être négatif.')]
    #[ORM\Column(name: 'salary_per_day', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?string $salaryPerDay = '0.00';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\OneToMany(targetEntity: CollabApplication::class, mappedBy: 'request', cascade: ['remove'], orphanRemoval: true)]
    private Collection $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
        $this->createdAt    = new \DateTime();
        $this->updatedAt    = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getNeededPeople(): ?int
    {
        return $this->neededPeople;
    }

    public function setNeededPeople(?int $neededPeople): static
    {
        $this->neededPeople = $neededPeople;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function getSalaryAsFloat(): float
    {
        return (float) ($this->salary ?? 0);
    }

    public function setSalary(string|float|null $salary): static
    {
        $this->salary = $salary !== null ? (string) $salary : null;

        return $this;
    }

    public function getSalaryPerDay(): ?string
    {
        return $this->salaryPerDay;
    }

    public function getSalaryPerDayAsFloat(): float
    {
        return (float) ($this->salaryPerDay ?? 0);
    }

    public function setSalaryPerDay(string|float|null $salaryPerDay): static
    {
        $this->salaryPerDay = $salaryPerDay !== null ? (string) $salaryPerDay : null;

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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string|float|null $latitude): static
    {
        $this->latitude = $latitude !== null ? (string) $latitude : null;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string|float|null $longitude): static
    {
        $this->longitude = $longitude !== null ? (string) $longitude : null;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /** @return Collection<int, CollabApplication> */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isExpired(): bool
    {
        return $this->endDate !== null && $this->endDate < new \DateTime('today');
    }
}
