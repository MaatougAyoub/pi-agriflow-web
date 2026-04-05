<?php

<<<<<<< HEAD
declare(strict_types=1);

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
namespace App\Entity;

use App\Repository\CollabApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabApplicationRepository::class)]
#[ORM\Table(name: 'collab_applications')]
<<<<<<< HEAD
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
=======
#[ORM\HasLifecycleCallbacks]
class CollabApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CollabRequest::class)]
    #[ORM\JoinColumn(name: 'request_id', referencedColumnName: 'id', nullable: false)]
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    private ?CollabRequest $request = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'candidate_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $candidate = null;

<<<<<<< HEAD
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
=======
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom complet est obligatoire.')]
    private ?string $fullName = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone est obligatoire.')]
    #[Assert\Regex(pattern: '/^[0-9]+$/', message: 'Le téléphone ne doit contenir que des chiffres.')]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    private ?string $email = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\NotNull(message: 'Les années d\'expérience sont obligatoires.')]
    #[Assert\PositiveOrZero(message: 'Les années d\'expérience ne peuvent pas être négatives.')]
    private ?int $yearsOfExperience = 0;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La motivation est obligatoire.')]
    private ?string $motivation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\PositiveOrZero(message: 'Le salaire attendu ne peut pas être négatif.')]
    private ?string $expectedSalary = '0.00';

    #[ORM\Column(length: 50, options: ['default' => 'PENDING'])]
    private ?string $status = 'PENDING';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $appliedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))

    public function __construct()
    {
        $this->appliedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

<<<<<<< HEAD
    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
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
<<<<<<< HEAD

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getCandidate(): ?Utilisateur
    {
        return $this->candidate;
    }

    public function setCandidate(?Utilisateur $candidate): static
    {
        $this->candidate = $candidate;
<<<<<<< HEAD

=======
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

<<<<<<< HEAD
    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

=======
    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

<<<<<<< HEAD
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

=======
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

<<<<<<< HEAD
    public function setEmail(?string $email): static
    {
        $this->email = $email;

=======
    public function setEmail(string $email): static
    {
        $this->email = $email;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

<<<<<<< HEAD
    public function setYearsOfExperience(?int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience;

=======
    public function setYearsOfExperience(int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

<<<<<<< HEAD
    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation;

=======
    public function setMotivation(string $motivation): static
    {
        $this->motivation = $motivation;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getExpectedSalary(): ?string
    {
        return $this->expectedSalary;
    }

<<<<<<< HEAD
    public function getExpectedSalaryAsFloat(): float
    {
        return (float) ($this->expectedSalary ?? 0);
    }

    public function setExpectedSalary(string|float|null $expectedSalary): static
    {
        $this->expectedSalary = $expectedSalary !== null ? (string) $expectedSalary : null;

=======
    public function setExpectedSalary(string $expectedSalary): static
    {
        $this->expectedSalary = $expectedSalary;
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

<<<<<<< HEAD
    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAppliedAt(): ?\DateTime
=======
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getAppliedAt(): ?\DateTimeInterface
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    {
        return $this->appliedAt;
    }

<<<<<<< HEAD
    public function getUpdatedAt(): ?\DateTime
=======
    public function setAppliedAt(\DateTimeInterface $appliedAt): static
    {
        $this->appliedAt = $appliedAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
>>>>>>> bfa3c6f (feat: add collaboration module FO/BO (controllers, entities, forms, templates))
    {
        return $this->updatedAt;
    }

<<<<<<< HEAD
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
