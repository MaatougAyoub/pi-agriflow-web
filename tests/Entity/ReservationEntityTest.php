<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Enum\ReservationStatut;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ReservationEntityTest extends TestCase
{
    public function testReservationStoresAmountsAndDates(): void
    {
        $annonce = new Annonce();
        $start = new \DateTimeImmutable('2026-05-10');
        $end = new \DateTimeImmutable('2026-05-12');

        $reservation = (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(7)
            ->setProprietaireId(15)
            ->setDateDebut($start)
            ->setDateFin($end)
            ->setQuantite(3)
            ->setPrixTotal(450)
            ->setCommission(22.5)
            ->setStatut(ReservationStatut::ACCEPTEE)
            ->setMessage('  Demande urgente  ');

        self::assertSame($annonce, $reservation->getAnnonce());
        self::assertSame(7, $reservation->getClientId());
        self::assertSame(15, $reservation->getProprietaireId());
        self::assertSame($start, $reservation->getDateDebut());
        self::assertSame($end, $reservation->getDateFin());
        self::assertSame(3, $reservation->getQuantite());
        self::assertSame('450.00', $reservation->getPrixTotal());
        self::assertSame(450.0, $reservation->getPrixTotalAsFloat());
        self::assertSame('22.50', $reservation->getCommission());
        self::assertSame(22.5, $reservation->getCommissionAsFloat());
        self::assertSame(ReservationStatut::ACCEPTEE, $reservation->getStatut());
        self::assertSame('Demande urgente', $reservation->getMessage());
        self::assertSame(3, $reservation->getNombreJours());
    }

    public function testNombreJoursReturnsZeroWhenDatesAreMissing(): void
    {
        self::assertSame(0, (new Reservation())->getNombreJours());
    }

    public function testPrePersistInitializesCreationDate(): void
    {
        $reservation = new Reservation();

        self::assertNull($reservation->getCreatedAt());

        $reservation->onPrePersist();

        self::assertInstanceOf(\DateTimeImmutable::class, $reservation->getCreatedAt());
    }

    public function testValidateDatesAddsViolationWhenEndDateIsBeforeStartDate(): void
    {
        $reservation = (new Reservation())
            ->setDateDebut(new \DateTimeImmutable('2026-05-12'))
            ->setDateFin(new \DateTimeImmutable('2026-05-10'));

        $builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $builder->expects(self::once())
            ->method('atPath')
            ->with('dateFin')
            ->willReturnSelf();
        $builder->expects(self::once())->method('addViolation');

        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects(self::once())
            ->method('buildViolation')
            ->with('La date de fin doit etre apres la date de debut.')
            ->willReturn($builder);

        $reservation->validateDates($context);
    }
}
