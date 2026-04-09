<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ReservationStatut;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull(message: 'L annonce est obligatoire.')]
    // relation: kol reservation lazimha tkoun marbouta b annonce wa7da
    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Annonce $annonce = null;

    #[Assert\Positive(message: 'Le client doit avoir un ID valide.')]
    // owner: demandeur_id houwa client eli ba3ath demande
    #[ORM\Column(name: 'demandeur_id')]
    private int $clientId = 1;

    // owner: proprietaire_id houwa vendeur mta3 annonce eli tetsab 3lih demande
    #[ORM\Column]
    private int $proprietaireId = 1;

    #[Assert\NotNull(message: 'La date de debut est obligatoire.')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateDebut = null;

    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dateFin = null;

    #[Assert\Positive(message: 'La quantite doit etre strictement positive.')]
    #[ORM\Column]
    private int $quantite = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $prixTotal = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $commission = '0.00';

    #[ORM\Column(enumType: ReservationStatut::class, length: 20)]
    private ReservationStatut $statut = ReservationStatut::EN_ATTENTE;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getProprietaireId(): int
    {
        return $this->proprietaireId;
    }

    public function setProprietaireId(int $proprietaireId): self
    {
        $this->proprietaireId = $proprietaireId;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeImmutable
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeImmutable
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getQuantite(): int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrixTotal(): string
    {
        return $this->prixTotal;
    }

    public function getPrixTotalAsFloat(): float
    {
        return (float) $this->prixTotal;
    }

    public function setPrixTotal(string|float|int $prixTotal): self
    {
        $this->prixTotal = number_format((float) $prixTotal, 2, '.', '');

        return $this;
    }

    public function getCommission(): string
    {
        return $this->commission;
    }

    public function getCommissionAsFloat(): float
    {
        return (float) $this->commission;
    }

    public function setCommission(string|float|int $commission): self
    {
        $this->commission = number_format((float) $commission, 2, '.', '');

        return $this;
    }

    public function getStatut(): ReservationStatut
    {
        return $this->statut;
    }

    public function setStatut(ReservationStatut $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = null !== $message ? trim($message) : null;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getNombreJours(): int
    {
        if (null === $this->dateDebut || null === $this->dateFin) {
            return 0;
        }

        // date: nzidou nhar bech period inclusive men debut lel fin
        return (int) $this->dateDebut->diff($this->dateFin)->days + 1;
    }

    #[Assert\Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        // validation: front w admin yestafdou men nafs check mta3 tartib dates
        if (null === $this->dateDebut || null === $this->dateFin) {
            return;
        }

        if ($this->dateFin < $this->dateDebut) {
            $context->buildViolation('La date de fin doit etre apres la date de debut.')
                ->atPath('dateFin')
                ->addViolation();
        }
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        // date: createdAt yet7at automatiquement wa9t tetsna3 reservation
        $this->createdAt ??= new \DateTimeImmutable();
    }
}
