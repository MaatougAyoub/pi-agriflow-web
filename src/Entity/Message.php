<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\MessageRepository;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
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
    private ?int $expediteur_id = null;

    public function getExpediteur_id(): ?int
    {
        return $this->expediteur_id;
    }

    public function setExpediteur_id(int $expediteur_id): self
    {
        $this->expediteur_id = $expediteur_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $destinataire_id = null;

    public function getDestinataire_id(): ?int
    {
        return $this->destinataire_id;
    }

    public function setDestinataire_id(int $destinataire_id): self
    {
        $this->destinataire_id = $destinataire_id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $sujet = null;

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(?string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $contenu = null;

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $annonce_id = null;

    public function getAnnonce_id(): ?int
    {
        return $this->annonce_id;
    }

    public function setAnnonce_id(?int $annonce_id): self
    {
        $this->annonce_id = $annonce_id;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reservation_id = null;

    public function getReservation_id(): ?int
    {
        return $this->reservation_id;
    }

    public function setReservation_id(?int $reservation_id): self
    {
        $this->reservation_id = $reservation_id;
        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $lu = null;

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(?bool $lu): self
    {
        $this->lu = $lu;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $date_lecture = null;

    public function getDate_lecture(): ?\DateTimeInterface
    {
        return $this->date_lecture;
    }

    public function setDate_lecture(?\DateTimeInterface $date_lecture): self
    {
        $this->date_lecture = $date_lecture;
        return $this;
    }

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $date_envoi = null;

    public function getDate_envoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDate_envoi(\DateTimeInterface $date_envoi): self
    {
        $this->date_envoi = $date_envoi;
        return $this;
    }

    public function getExpediteurId(): ?int
    {
        return $this->expediteur_id;
    }

    public function setExpediteurId(int $expediteur_id): static
    {
        $this->expediteur_id = $expediteur_id;

        return $this;
    }

    public function getDestinataireId(): ?int
    {
        return $this->destinataire_id;
    }

    public function setDestinataireId(int $destinataire_id): static
    {
        $this->destinataire_id = $destinataire_id;

        return $this;
    }

    public function getAnnonceId(): ?int
    {
        return $this->annonce_id;
    }

    public function setAnnonceId(?int $annonce_id): static
    {
        $this->annonce_id = $annonce_id;

        return $this;
    }

    public function getReservationId(): ?int
    {
        return $this->reservation_id;
    }

    public function setReservationId(?int $reservation_id): static
    {
        $this->reservation_id = $reservation_id;

        return $this;
    }

    public function getDateLecture(): ?\DateTime
    {
        return $this->date_lecture;
    }

    public function setDateLecture(?\DateTime $date_lecture): static
    {
        $this->date_lecture = $date_lecture;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTime
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTime $date_envoi): static
    {
        $this->date_envoi = $date_envoi;

        return $this;
    }

}
