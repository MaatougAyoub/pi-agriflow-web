<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\AnnonceStatut;
use App\Enum\ReservationStatut;

final class SellerMarketplaceService
{
    public function isAnnonceOwner(?Utilisateur $user, Annonce $annonce): bool
    {
        return $user instanceof Utilisateur
            && null !== $user->getId()
            && $annonce->getProprietaireId() === $user->getId();
    }

    public function isReservationOwner(?Utilisateur $user, Reservation $reservation): bool
    {
        return $user instanceof Utilisateur
            && null !== $user->getId()
            && $reservation->getProprietaireId() === $user->getId();
    }

    public function assignAnnonceOwner(Annonce $annonce, Utilisateur $user): void
    {
        if (null === $user->getId()) {
            throw new \DomainException('Utilisateur vendeur invalide.');
        }

        // houni nrobtu l annonce b sahibha direct men session bech ownership yeb9a s7i7
        $annonce->setProprietaireId($user->getId());
    }

    public function ensureCanReserve(Utilisateur $user, Annonce $annonce): void
    {
        if ($this->isAnnonceOwner($user, $annonce)) {
            throw new \DomainException('Vous ne pouvez pas reserver votre propre annonce.');
        }

        if ($annonce->getStatut() !== AnnonceStatut::DISPONIBLE) {
            throw new \DomainException('Cette annonce n est plus disponible.');
        }
    }

    public function acceptReservation(Reservation $reservation): void
    {
        $annonce = $reservation->getAnnonce();

        if (null === $annonce) {
            throw new \DomainException('Reservation invalide sans annonce.');
        }

        if ($reservation->getStatut() !== ReservationStatut::EN_ATTENTE) {
            throw new \DomainException('Seules les demandes en attente peuvent etre acceptees.');
        }

        if ($reservation->getQuantite() > $annonce->getQuantiteDisponible()) {
            throw new \DomainException('Le stock disponible ne permet plus d accepter cette demande.');
        }

        $remainingQuantity = $annonce->getQuantiteDisponible() - $reservation->getQuantite();

        $reservation->setStatut(ReservationStatut::ACCEPTEE);
        $annonce->setQuantiteDisponible($remainingQuantity);

        if ($remainingQuantity === 0) {
            $annonce->setStatut($annonce->isLocation() ? AnnonceStatut::LOUEE : AnnonceStatut::VENDUE);
        } else {
            $annonce->setStatut(AnnonceStatut::DISPONIBLE);
        }
    }

    public function refuseReservation(Reservation $reservation): void
    {
        if ($reservation->getStatut() !== ReservationStatut::EN_ATTENTE) {
            throw new \DomainException('Seules les demandes en attente peuvent etre refusees.');
        }

        $reservation->setStatut(ReservationStatut::REFUSEE);
    }
}
