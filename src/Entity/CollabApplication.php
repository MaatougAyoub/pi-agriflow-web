<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CollabApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabApplicationRepository::class)]
#[ORM\Table(name: 'collab_applications')]
#[ORM\Index(columns: ['status'], name: 'idx_collab_app_status')]
#[ORM\UniqueConstraint(name: 'uq_candidate_request', columns: ['candidate_id', 'request_id'])]
#[ORM\HasLifecycleCallbacks]
class CollabApplication
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        'En attente' => self::STATUS_PENDING,
        'Acceptée'   => self::STATUS_ACCEPTED,
        'Refusée'    => self::STATUS_REJECTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CollabRequest::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(name: 'request_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CollabRequest $request = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'candidate_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $candidate = null;

    #[Assert\NotBlank(message: 'Le nom complet est obligatoire.')]
    #[Assert\Length(min: 3, max: 100, minMessage: 'Le nom doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(name: 'full_name', length: 100)]
    private ?string $fullName = null;

    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire.')]
    #[Assert\Regex(pattern: '/^\+?[0-9\s\-]{8,20}$/', message: 'Numéro de téléphone invalide.')]
    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: 'Email invalide.')]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[Assert\NotNull(message: "Le nombre d'années d'expérience est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Les années d'expérience ne peuvent pas être négatives.")]
    #[Assert\LessThanOrEqual(value: 50, message: "Les années d'expérience semblent incorrectes.")]
    #[ORM\Column(name: 'years_of_experience')]
    private ?int $yearsOfExperience = null;

    #[Assert\NotBlank(message: 'La lettre de motivation est obligatoire.')]
    #[Assert\Length(min: 50, minMessage: 'La motivation doit contenir au moins {{ limit }} caractères.')]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $motivation = null;

    #[Assert\PositiveOrZero(message: 'Le salaire attendu ne peut pas être négatif.')]
    #[ORM\Column(name: 'expected_salary', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => 0])]
    private ?string $expectedSalary = '0.00';

    #[Assert\Choice(choices: [self::STATUS_PENDING, self::STATUS_ACCEPTED, self::STATUS_REJECTED], message: 'Statut invalide.')]
    #[ORM\Column(length: 20, options: ['default' => 'pending'])]
    private ?string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'applied_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $appliedAt = null;

    #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->appliedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
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

    public function getRequest(): ?CollabRequest
    {
        return $this->request;
    }

    public function setRequest(?CollabRequest $request): static
    {
        $this->request = $request;

        return $this;
    }

    public function getCandidate(): ?Utilisateur
    {
        return $this->candidate;
    }

    public function setCandidate(?Utilisateur $candidate): static
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(?int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience;

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getExpectedSalary(): ?string
    {
        return $this->expectedSalary;
    }

    public function getExpectedSalaryAsFloat(): float
    {
        return (float) ($this->expectedSalary ?? 0);
    }

    public function setExpectedSalary(string|float|null $expectedSalary): static
    {
        $this->expectedSalary = $expectedSalary !== null ? (string) $expectedSalary : null;

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

    public function getAppliedAt(): ?\DateTime
    {
        return $this->appliedAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }
}
