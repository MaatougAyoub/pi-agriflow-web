<?php

namespace App\Entity;

use App\Enum\EtatCulture;
use App\Enum\TypeCulture;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[Assert\Callback('validateDates')]
class Culture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?float $superficie = null;

    #[ORM\Column]
    private ?int $proprietaireId = null;

    #[ORM\Column(enumType: TypeCulture::class)]
    private TypeCulture $typeCulture = TypeCulture::AUTRE;

    #[ORM\Column(enumType: EtatCulture::class)]
    private EtatCulture $etat = EtatCulture::EN_COURS;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateRecolte = null;

    #[ORM\Column]
    private \DateTimeImmutable $dateCreation;

    #[ORM\ManyToOne(inversedBy: 'cultures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Parcelle $parcelle = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getProprietaireId(): ?int
    {
        return $this->proprietaireId;
    }

    public function setProprietaireId(int $proprietaireId): static
    {
        $this->proprietaireId = $proprietaireId;

        return $this;
    }

    public function getTypeCulture(): TypeCulture
    {
        return $this->typeCulture;
    }

    public function setTypeCulture(TypeCulture $typeCulture): static
    {
        $this->typeCulture = $typeCulture;

        return $this;
    }

    public function getEtat(): EtatCulture
    {
        return $this->etat;
    }

    public function setEtat(EtatCulture $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDateRecolte(): ?\DateTimeInterface
    {
        return $this->dateRecolte;
    }

    public function setDateRecolte(?\DateTimeInterface $dateRecolte): static
    {
        $this->dateRecolte = $dateRecolte;

        return $this;
    }

    public function getDateCreation(): \DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeImmutable $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getParcelle(): ?Parcelle
    {
        return $this->parcelle;
    }

    public function setParcelle(?Parcelle $parcelle): static
    {
        $this->parcelle = $parcelle;

        return $this;
    }

    public function validateDates(ExecutionContextInterface $context): void
    {
        if (null === $this->dateRecolte) {
            return;
        }

        $dateRecolte = \DateTimeImmutable::createFromInterface($this->dateRecolte)->setTime(0, 0);
        $dateCreation = $this->dateCreation->setTime(0, 0);

        if ($dateRecolte < $dateCreation) {
            $context
                ->buildViolation('La date de recolte doit etre superieure ou egale a la date de creation.')
                ->atPath('dateRecolte')
                ->addViolation();
        }
    }
}
