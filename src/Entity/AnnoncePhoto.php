<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\AnnoncePhotoRepository;

#[ORM\Entity(repositoryClass: AnnoncePhotoRepository::class)]
#[ORM\Table(name: 'annonce_photos')]
class AnnoncePhoto
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $url_photo = null;

    public function getUrl_photo(): ?string
    {
        return $this->url_photo;
    }

    public function setUrl_photo(string $url_photo): self
    {
        $this->url_photo = $url_photo;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $ordre = null;

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;
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

    public function getUrlPhoto(): ?string
    {
        return $this->url_photo;
    }

    public function setUrlPhoto(string $url_photo): static
    {
        $this->url_photo = $url_photo;

        return $this;
    }

}
