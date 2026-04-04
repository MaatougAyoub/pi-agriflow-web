<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Reservation;

class ReservationPricingService
{
    public const COMMISSION_RATE = 0.05;

    public function hydrateReservation(Reservation $reservation): void
    {
        $annonce = $reservation->getAnnonce();

        if (null === $annonce) {
            throw new \DomainException('Impossible de calculer une reservation sans annonce.');
        }

        if ($reservation->getQuantite() > $annonce->getQuantiteDisponible()) {
            throw new \DomainException('La quantite demandee depasse le stock disponible.');
        }

        // houni n7sbou prix de base fi blasa wahda bech controller yeb9a khfif
        $basePrice = $annonce->isLocation()
            ? $annonce->getPrixAsFloat() * max(1, $reservation->getNombreJours())
            : $annonce->getPrixAsFloat() * $reservation->getQuantite();

        $commission = round($basePrice * self::COMMISSION_RATE, 2);

        $reservation->setCommission($commission);
        $reservation->setPrixTotal($basePrice + $commission);
        $reservation->setProprietaireId($annonce->getProprietaireId());
    }
}
