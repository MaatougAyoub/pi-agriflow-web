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
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères.',
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 20,
        max: 10000,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.',
    )]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La localisation est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'La localisation doit faire au moins {{ limit }} caractères.',
        maxMessage: 'La localisation ne peut pas dépasser {{ limit }} caractères.',
    )]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $latitude = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 7, nullable: true)]
    private ?string $longitude = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        groups: ['collab_create'],
        message: 'La date de début ne peut pas être antérieure à aujourd\'hui.',
    )]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[Assert\Expression(
        'this.getStartDate() === null or value > this.getStartDate()',
        message: 'La date de fin doit être ultérieure à la date de début.',
    )]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        groups: ['collab_create'],
        message: 'La date de fin ne peut pas être antérieure à aujourd\'hui.',
    )]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(options: ['default' => 1])]
    #[Assert\NotNull(message: 'Le nombre de personnes est obligatoire.')]
    #[Assert\Range(
        notInRangeMessage: 'Le nombre de personnes doit être compris entre {{ min }} et {{ max }}.',
        min: 1,
        max: 50,
    )]
    private ?int $neededPeople = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\NotBlank(message: 'Le salaire journalier est obligatoire.')]
    #[Assert\Range(
        min: 0,
        max: 99999.99,
        notInRangeMessage: 'Le salaire par jour doit être compris entre {{ min }} et {{ max }} DT.',
    )]
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

    /** Salaire journalier (même champ que `salary` dans le schéma AgriFlow). */
    public function getSalaryPerDayAsFloat(): float
    {
        return (float) ($this->salary ?? 0);
    }

    /** @deprecated Utilisez getSalaryPerDayAsFloat() */
    public function getSalaryAsFloat(): float
    {
        return $this->getSalaryPerDayAsFloat();
    }

    public function setSalary(string|float|int $salary): static
    {
        $this->salary = \is_string($salary) ? $salary : number_format((float) $salary, 2, '.', '');

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
