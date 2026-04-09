<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class ContactRequest
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 120,
        minMessage: 'Le nom doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le nom ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $nom = null;

    #[Assert\NotBlank(message: 'L email est obligatoire.')]
    #[Assert\Email(message: 'Merci de saisir une adresse email valide.')]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Le sujet est obligatoire.')]
    #[Assert\Length(
        min: 4,
        max: 150,
        minMessage: 'Le sujet doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le sujet ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $sujet = null;

    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(
        min: 15,
        max: 1200,
        minMessage: 'Le message doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le message ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $message = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = trim($nom);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = trim($email);

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = trim($sujet);

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = trim($message);

        return $this;
    }
}
