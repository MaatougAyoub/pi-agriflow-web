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
    #[Assert\NotBlank(message: 'Le nom complet est obligatoire.')]
    private ?string $fullName = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le téléphone est obligatoire.')]
    private ?string $phone = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire.')]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\Column(options: ['default' => 0])]
    #[Assert\PositiveOrZero(message: 'Les années d\'expérience ne peuvent pas être négatives.')]
    private ?int $yearsOfExperience = 0;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La motivation est obligatoire.')]
    private ?string $motivation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    #[Assert\PositiveOrZero(message: 'Le salaire attendu ne peut pas être négatif.')]
    private ?float $expectedSalary = 0.00;

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

    public function getExpectedSalary(): ?float
    {
        return $this->expectedSalary;
    }

    public function setExpectedSalary(float $expectedSalary): static
    {
        $this->expectedSalary = $expectedSalary;

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
