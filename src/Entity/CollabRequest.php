<?php

<<<<<<< HEAD
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CollabRequestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
=======
namespace App\Entity;

use App\Repository\CollabRequestRepository;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabRequestRepository::class)]
#[ORM\Table(name: 'collab_requests')]
<<<<<<< HEAD
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
=======
#[ORM\HasLifecycleCallbacks]
class CollabRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, minMessage: 'Le titre doit faire au moins {{ limit }} caractères.')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    private ?string $location = null;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

<<<<<<< HEAD
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
=======
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[Assert\Expression(
        "this.getStartDate() === null or value > this.getStartDate()",
        message: 'La date de fin doit être ultérieure à la date de début.'
    )]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\NotBlank(message: 'Le nombre de personnes est obligatoire.')]
    #[Assert\Positive(message: 'Le nombre de personnes doit être au moins de 1.')]
    private ?int $neededPeople = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\PositiveOrZero(message: 'Le salaire ne peut pas être négatif.')]
    private ?string $salary = '0.00';

    #[ORM\Column(length: 50, options: ['default' => 'PENDING'])]
    private ?string $status = 'PENDING';

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'requester_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $requester = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $publisher = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

<<<<<<< HEAD
=======
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

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): static
    {
        $this->longitude = $longitude;
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

    public function getNeededPeople(): ?int
    {
        return $this->neededPeople;
    }

    public function setNeededPeople(int $neededPeople): static
    {
        $this->neededPeople = $neededPeople;
        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): static
    {
        $this->salary = $salary;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    public function getRequester(): ?Utilisateur
    {
        return $this->requester;
    }

    public function setRequester(?Utilisateur $requester): static
    {
        $this->requester = $requester;
<<<<<<< HEAD

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

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): static
    {
        $this->publisher = $publisher;
<<<<<<< HEAD

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
=======
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    {
        return $this->createdAt;
    }

<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTime
=======
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    {
        return $this->updatedAt;
    }

<<<<<<< HEAD
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
=======
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTime();
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    }
}
