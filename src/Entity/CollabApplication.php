<?php

namespace App\Entity;

use App\Repository\CollabApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollabApplicationRepository::class)]
#[ORM\Table(name: 'collab_applications')]
#[ORM\HasLifecycleCallbacks]
class CollabApplication
{
    public const STATUS_PENDING = 'PENDING';

    public const STATUS_ACCEPTED = 'APPROVED';

    public const STATUS_REJECTED = 'REJECTED';

    public const STATUSES = [
        'En attente' => self::STATUS_PENDING,
        'Acceptée' => self::STATUS_ACCEPTED,
        'Refusée' => self::STATUS_REJECTED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CollabRequest::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(name: 'request_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CollabRequest $request = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'candidate_id', referencedColumnName: 'id', nullable: false)]
    private ?Utilisateur $candidate = null;

    #[ORM\Column(length: 255)]
    #[Assert\Sequentially([
        new Assert\NotBlank(message: 'Veuillez compléter le nom complet.'),
        new Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Le nom complet doit contenir au moins {{ limit }} caractères.',
            maxMessage: 'Le nom complet ne peut pas dépasser {{ limit }} caractères.',
        ),
    ])]
    private ?string $fullName = null;

    #[ORM\Column(length: 20)]
    #[Assert\Sequentially([
        new Assert\NotBlank(message: 'Veuillez compléter le numéro de téléphone.'),
        new Assert\Regex(pattern: '/^[0-9]+$/', message: 'Le téléphone ne doit contenir que des chiffres.'),
        new Assert\Length(
            min: 8,
            max: 20,
            minMessage: 'Le téléphone doit contenir au moins {{ limit }} chiffres.',
            maxMessage: 'Le téléphone ne peut pas dépasser {{ limit }} chiffres.',
        ),
    ])]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    #[Assert\Sequentially([
        new Assert\NotBlank(message: 'Veuillez compléter l’adresse e-mail.'),
        new Assert\Email(message: 'Veuillez saisir une adresse e-mail valide.'),
        new Assert\Length(max: 100, maxMessage: 'L’e-mail ne peut pas dépasser {{ limit }} caractères.'),
    ])]
    private ?string $email = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\NotNull(message: 'Les années d\'expérience sont obligatoires.')]
    #[Assert\Range(
        notInRangeMessage: 'Les années d\'expérience doivent être comprises entre {{ min }} et {{ max }}.',
        min: 0,
        max: 50,
    )]
    private ?int $yearsOfExperience = 0;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Sequentially([
        new Assert\NotBlank(message: 'Veuillez compléter la lettre de motivation.'),
        new Assert\Length(
            min: 30,
            max: 8000,
            minMessage: 'La lettre de motivation doit contenir au moins {{ limit }} caractères.',
            maxMessage: 'La lettre de motivation ne peut pas dépasser {{ limit }} caractères.',
        ),
    ])]
    private ?string $motivation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\Sequentially([
        new Assert\NotBlank(message: 'Veuillez compléter le salaire attendu (indiquez 0 si le salaire de l’offre vous convient).'),
        new Assert\Range(
            min: 0,
            max: 99999.99,
            notInRangeMessage: 'Le salaire attendu doit être compris entre {{ min }} et {{ max }} DT.',
        ),
    ])]
    private ?string $expectedSalary = '0.00';

    #[ORM\Column(length: 50, options: ['default' => 'PENDING'])]
    private ?string $status = 'PENDING';

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $appliedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->appliedAt = new \DateTime();
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
        $this->fullName = $fullName ?? '';

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone ?? '';

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email ?? '';

        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(?int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience ?? 0;

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation ?? '';

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

    public function setExpectedSalary(string|float|int|null $expectedSalary): static
    {
        if ($expectedSalary === null || $expectedSalary === '') {
            $this->expectedSalary = '';

            return $this;
        }

        $this->expectedSalary = \is_string($expectedSalary)
            ? $expectedSalary
            : number_format((float) $expectedSalary, 2, '.', '');

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

    public function getAppliedAt(): ?\DateTimeInterface
    {
        return $this->appliedAt;
    }

    public function setAppliedAt(\DateTimeInterface $appliedAt): static
    {
        $this->appliedAt = $appliedAt;

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
}
