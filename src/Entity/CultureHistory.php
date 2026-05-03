<?php

namespace App\Entity;

use App\Repository\CultureHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CultureHistoryRepository::class)]
#[ORM\Table(name: 'culture_history')]
#[ORM\HasLifecycleCallbacks]
class CultureHistory
{
    public const ACTION_CREATED = 'CREATED';
    public const ACTION_UPDATED = 'UPDATED';
    public const ACTION_PUBLISHED = 'PUBLISHED';
    public const ACTION_PUBLICATION_CANCELLED = 'PUBLICATION_CANCELLED';
    public const ACTION_PURCHASED = 'PURCHASED';
    public const ACTION_HARVESTED = 'HARVESTED';
    public const ACTION_HARVEST_CANCELLED = 'HARVEST_CANCELLED';

    private const ACTION_LABELS = [
        self::ACTION_CREATED => 'Culture creee',
        self::ACTION_UPDATED => 'Culture modifiee',
        self::ACTION_PUBLISHED => 'Mise en vente',
        self::ACTION_PUBLICATION_CANCELLED => 'Publication annulee',
        self::ACTION_PURCHASED => 'Culture achetee',
        self::ACTION_HARVESTED => 'Culture recoltee',
        self::ACTION_HARVEST_CANCELLED => 'Recolte annulee',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Culture::class)]
    #[ORM\JoinColumn(name: 'culture_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Culture $culture = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $action = self::ACTION_CREATED;

    #[ORM\Column(name: 'performed_at', type: 'datetime')]
    private \DateTimeInterface $performedAt;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'utilisateur_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $details = null;

    public function getId(): ?int
    {
        return isset($this->id) ? $this->id : null;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCulture(): ?Culture
    {
        return $this->culture;
    }

    public function setCulture(?Culture $culture): self
    {
        $this->culture = $culture;

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getPerformedAt(): ?\DateTimeInterface
    {
        return isset($this->performedAt) ? $this->performedAt : null;
    }

    protected function setPerformedAt(\DateTimeInterface $performedAt): self
    {
        $this->performedAt = $performedAt;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getActionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }

    public function getUtilisateurDisplayName(): ?string
    {
        if (!$this->utilisateur instanceof Utilisateur) {
            return null;
        }

        $fullName = trim(sprintf('%s %s', $this->utilisateur->getPrenom() ?? '', $this->utilisateur->getNom() ?? ''));

        if ('' !== $fullName) {
            return $fullName;
        }

        return $this->utilisateur->getEmail();
    }

    #[ORM\PrePersist]
    public function initializePerformedAt(): void
    {
        if (!isset($this->performedAt)) {
            $this->performedAt = new \DateTime();
        }
    }
}
