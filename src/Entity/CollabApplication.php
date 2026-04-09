<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CollabApplicationRepository;

#[ORM\Entity(repositoryClass: CollabApplicationRepository::class)]
#[ORM\Table(name: 'collab_applications')]
class CollabApplication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $request_id = null;

    public function getRequest_id(): ?int
    {
        return $this->request_id;
    }

    public function setRequest_id(int $request_id): self
    {
        $this->request_id = $request_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $candidate_id = null;

    public function getCandidate_id(): ?int
    {
        return $this->candidate_id;
    }

    public function setCandidate_id(int $candidate_id): self
    {
        $this->candidate_id = $candidate_id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $full_name = null;

    public function getFull_name(): ?string
    {
        return $this->full_name;
    }

    public function setFull_name(string $full_name): self
    {
        $this->full_name = $full_name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $phone = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $years_of_experience = null;

    public function getYears_of_experience(): ?int
    {
        return $this->years_of_experience;
    }

    public function setYears_of_experience(int $years_of_experience): self
    {
        $this->years_of_experience = $years_of_experience;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $motivation = null;

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(string $motivation): self
    {
        $this->motivation = $motivation;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $expected_salary = null;

    public function getExpected_salary(): ?float
    {
        return $this->expected_salary;
    }

    public function setExpected_salary(?float $expected_salary): self
    {
        $this->expected_salary = $expected_salary;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $status = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $applied_at = null;

    public function getApplied_at(): ?\DateTimeInterface
    {
        return $this->applied_at;
    }

    public function setApplied_at(\DateTimeInterface $applied_at): self
    {
        $this->applied_at = $applied_at;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $updated_at = null;

    public function getUpdated_at(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdated_at(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getRequestId(): ?int
    {
        return $this->request_id;
    }

    public function setRequestId(int $request_id): static
    {
        $this->request_id = $request_id;

        return $this;
    }

    public function getCandidateId(): ?int
    {
        return $this->candidate_id;
    }

    public function setCandidateId(int $candidate_id): static
    {
        $this->candidate_id = $candidate_id;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->full_name;
    }

    public function setFullName(string $full_name): static
    {
        $this->full_name = $full_name;

        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->years_of_experience;
    }

    public function setYearsOfExperience(int $years_of_experience): static
    {
        $this->years_of_experience = $years_of_experience;

        return $this;
    }

    public function getExpectedSalary(): ?string
    {
        return $this->expected_salary;
    }

    public function setExpectedSalary(?string $expected_salary): static
    {
        $this->expected_salary = $expected_salary;

        return $this;
    }

    public function getAppliedAt(): ?\DateTime
    {
        return $this->applied_at;
    }

    public function setAppliedAt(\DateTime $applied_at): static
    {
        $this->applied_at = $applied_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}
