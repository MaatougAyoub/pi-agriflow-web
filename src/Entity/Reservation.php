<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ReservationRepository;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\Table(name: 'reservations')]
class Reservation
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
    private ?int $annonce_id = null;

    public function getAnnonce_id(): ?int
    {
        return $this->annonce_id;
    }

    public function setAnnonce_id(int $annonce_id): self
    {
        $this->annonce_id = $annonce_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $demandeur_id = null;

    public function getDemandeur_id(): ?int
    {
        return $this->demandeur_id;
    }

    public function setDemandeur_id(int $demandeur_id): self
    {
        $this->demandeur_id = $demandeur_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $proprietaire_id = null;

    public function getProprietaire_id(): ?int
    {
        return $this->proprietaire_id;
    }

    public function setProprietaire_id(int $proprietaire_id): self
    {
        $this->proprietaire_id = $proprietaire_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_debut = null;

    public function getDate_debut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDate_debut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $date_fin = null;

    public function getDate_fin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDate_fin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantite = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $prix_total = null;

    public function getPrix_total(): ?float
    {
        return $this->prix_total;
    }

    public function setPrix_total(float $prix_total): self
    {
        $this->prix_total = $prix_total;
        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: true)]
    private ?float $caution = null;

    public function getCaution(): ?float
    {
        return $this->caution;
    }

    public function setCaution(?float $caution): self
    {
        $this->caution = $caution;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $statut = null;

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message_demande = null;

    public function getMessage_demande(): ?string
    {
        return $this->message_demande;
    }

    public function setMessage_demande(?string $message_demande): self
    {
        $this->message_demande = $message_demande;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $reponse_proprietaire = null;

    public function getReponse_proprietaire(): ?string
    {
        return $this->reponse_proprietaire;
    }

    public function setReponse_proprietaire(?string $reponse_proprietaire): self
    {
        $this->reponse_proprietaire = $reponse_proprietaire;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_demande = null;

    public function getDate_demande(): ?\DateTimeInterface
    {
        return $this->date_demande;
    }

    public function setDate_demande(\DateTimeInterface $date_demande): self
    {
        $this->date_demande = $date_demande;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_reponse = null;

    public function getDate_reponse(): ?\DateTimeInterface
    {
        return $this->date_reponse;
    }

    public function setDate_reponse(?\DateTimeInterface $date_reponse): self
    {
        $this->date_reponse = $date_reponse;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_creation = null;

    public function getDate_creation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDate_creation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $contrat_url = null;

    public function getContrat_url(): ?string
    {
        return $this->contrat_url;
    }

    public function setContrat_url(?string $contrat_url): self
    {
        $this->contrat_url = $contrat_url;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $contrat_signe = null;

    public function isContrat_signe(): ?bool
    {
        return $this->contrat_signe;
    }

    public function setContrat_signe(?bool $contrat_signe): self
    {
        $this->contrat_signe = $contrat_signe;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_signature_contrat = null;

    public function getDate_signature_contrat(): ?\DateTimeInterface
    {
        return $this->date_signature_contrat;
    }

    public function setDate_signature_contrat(?\DateTimeInterface $date_signature_contrat): self
    {
        $this->date_signature_contrat = $date_signature_contrat;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $paiement_effectue = null;

    public function isPaiement_effectue(): ?bool
    {
        return $this->paiement_effectue;
    }

    public function setPaiement_effectue(?bool $paiement_effectue): self
    {
        $this->paiement_effectue = $paiement_effectue;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_paiement = null;

    public function getDate_paiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDate_paiement(?\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $mode_paiement = null;

    public function getMode_paiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function setMode_paiement(?string $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;
        return $this;
    }

    public function getAnnonceId(): ?int
    {
        return $this->annonce_id;
    }

    public function setAnnonceId(int $annonce_id): static
    {
        $this->annonce_id = $annonce_id;

        return $this;
    }

    public function getDemandeurId(): ?int
    {
        return $this->demandeur_id;
    }

    public function setDemandeurId(int $demandeur_id): static
    {
        $this->demandeur_id = $demandeur_id;

        return $this;
    }

    public function getProprietaireId(): ?int
    {
        return $this->proprietaire_id;
    }

    public function setProprietaireId(int $proprietaire_id): static
    {
        $this->proprietaire_id = $proprietaire_id;

        return $this;
    }

    public function getDateDebut(): ?\DateTime
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTime $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTime $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getPrixTotal(): ?string
    {
        return $this->prix_total;
    }

    public function setPrixTotal(string $prix_total): static
    {
        $this->prix_total = $prix_total;

        return $this;
    }

    public function getMessageDemande(): ?string
    {
        return $this->message_demande;
    }

    public function setMessageDemande(?string $message_demande): static
    {
        $this->message_demande = $message_demande;

        return $this;
    }

    public function getReponseProprietaire(): ?string
    {
        return $this->reponse_proprietaire;
    }

    public function setReponseProprietaire(?string $reponse_proprietaire): static
    {
        $this->reponse_proprietaire = $reponse_proprietaire;

        return $this;
    }

    public function getDateDemande(): ?\DateTime
    {
        return $this->date_demande;
    }

    public function setDateDemande(\DateTime $date_demande): static
    {
        $this->date_demande = $date_demande;

        return $this;
    }

    public function getDateReponse(): ?\DateTime
    {
        return $this->date_reponse;
    }

    public function setDateReponse(?\DateTime $date_reponse): static
    {
        $this->date_reponse = $date_reponse;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getContratUrl(): ?string
    {
        return $this->contrat_url;
    }

    public function setContratUrl(?string $contrat_url): static
    {
        $this->contrat_url = $contrat_url;

        return $this;
    }

    public function isContratSigne(): ?bool
    {
        return $this->contrat_signe;
    }

    public function setContratSigne(?bool $contrat_signe): static
    {
        $this->contrat_signe = $contrat_signe;

        return $this;
    }

    public function getDateSignatureContrat(): ?\DateTime
    {
        return $this->date_signature_contrat;
    }

    public function setDateSignatureContrat(?\DateTime $date_signature_contrat): static
    {
        $this->date_signature_contrat = $date_signature_contrat;

        return $this;
    }

    public function isPaiementEffectue(): ?bool
    {
        return $this->paiement_effectue;
    }

    public function setPaiementEffectue(?bool $paiement_effectue): static
    {
        $this->paiement_effectue = $paiement_effectue;

        return $this;
    }

    public function getDatePaiement(): ?\DateTime
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(?\DateTime $date_paiement): static
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?string $mode_paiement): static
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

}
