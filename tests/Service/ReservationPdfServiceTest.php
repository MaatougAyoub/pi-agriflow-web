<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Enum\AnnonceStatut;
use App\Enum\AnnonceType;
use App\Repository\UtilisateurRepository;
use App\Service\AnnonceBusinessDiagnosticService;
use App\Service\AnnonceEnvironmentInsightService;
use App\Service\ReservationPdfService;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpClient\MockHttpClient;
use Twig\Environment;

final class ReservationPdfServiceTest extends TestCase
{
    public function testStreamReservationQuoteReturnsPdfResponseWithExpectedHeaders(): void
    {
        $reservation = $this->createReservationWithAnnonce();
        $this->setEntityId($reservation, 7);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('pdf/reservation_quote.html.twig', self::callback(function (array $context): bool {
                self::assertInstanceOf(Reservation::class, $context['reservation']);
                self::assertInstanceOf(Annonce::class, $context['annonce']);
                self::assertInstanceOf(Utilisateur::class, $context['client']);
                self::assertInstanceOf(Utilisateur::class, $context['vendeur']);
                self::assertIsArray($context['businessDiagnostic']);

                return true;
            }))
            ->willReturn('<html>pdf</html>');

        $dompdf = $this->createMock(DompdfWrapperInterface::class);
        $dompdf->expects(self::once())
            ->method('getPdf')
            ->with('<html>pdf</html>')
            ->willReturn('%PDF-test');

        $repository = $this->createMock(UtilisateurRepository::class);
        $repository->method('find')->willReturnMap([
            [15, $this->createUser(15, 'Client Test')],
            [9, $this->createUser(9, 'Vendeur Test')],
        ]);

        $service = new ReservationPdfService(
            $dompdf,
            $twig,
            $repository,
            new AnnonceBusinessDiagnosticService(),
            new AnnonceEnvironmentInsightService(new MockHttpClient(), new NullLogger(), 'https://api.open-meteo.com', 'https://air-quality-api.open-meteo.com'),
            sys_get_temp_dir()
        );

        $response = $service->streamReservationQuote($reservation);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('application/pdf', $response->headers->get('Content-Type'));
        self::assertSame('attachment; filename="devis-reservation-7.pdf"', $response->headers->get('Content-Disposition'));
        self::assertSame('%PDF-test', $response->getContent());
    }

    public function testStreamReservationQuoteKeepsAnnonceContextNullable(): void
    {
        $reservation = (new Reservation())
            ->setClientId(20)
            ->setProprietaireId(21)
            ->setQuantite(1);
        $this->setEntityId($reservation, 11);

        $twig = $this->createMock(Environment::class);
        $twig->expects(self::once())
            ->method('render')
            ->with('pdf/reservation_quote.html.twig', self::callback(function (array $context): bool {
                self::assertNull($context['annonce']);
                self::assertNull($context['environmentInsights']);
                self::assertNull($context['businessDiagnostic']);
                self::assertNull($context['logoPath']);

                return true;
            }))
            ->willReturn('<html>empty</html>');

        $dompdf = $this->createMock(DompdfWrapperInterface::class);
        $dompdf->method('getPdf')->willReturn('%PDF-empty');

        $repository = $this->createMock(UtilisateurRepository::class);
        $repository->method('find')->willReturn(null);

        $service = new ReservationPdfService(
            $dompdf,
            $twig,
            $repository,
            new AnnonceBusinessDiagnosticService(),
            new AnnonceEnvironmentInsightService(new MockHttpClient(), new NullLogger(), 'https://api.open-meteo.com', 'https://air-quality-api.open-meteo.com'),
            sys_get_temp_dir()
        );

        $response = $service->streamReservationQuote($reservation, 'devis-vendeur');

        self::assertSame('attachment; filename="devis-vendeur-11.pdf"', $response->headers->get('Content-Disposition'));
    }

    private function createReservationWithAnnonce(): Reservation
    {
        $annonce = (new Annonce())
            ->setTitre('Moissonneuse')
            ->setDescription('Moissonneuse disponible pour recolte avec equipement complet et entretien recent.')
            ->setType(AnnonceType::LOCATION)
            ->setStatut(AnnonceStatut::DISPONIBLE)
            ->setPrix(220)
            ->setCategorie('Materiel agricole')
            ->setImageUrl('https://cdn.example.org/moissonneuse.jpg')
            ->setLocalisation('Beja')
            ->setProprietaireId(9)
            ->setQuantiteDisponible(3)
            ->setUnitePrix('jour');

        return (new Reservation())
            ->setAnnonce($annonce)
            ->setClientId(15)
            ->setProprietaireId(9)
            ->setDateDebut(new \DateTimeImmutable('2026-04-20'))
            ->setDateFin(new \DateTimeImmutable('2026-04-22'))
            ->setQuantite(1)
            ->setPrixTotal(693)
            ->setCommission(33)
            ->setMessage('Besoin pour trois jours');
    }

    private function createUser(int $id, string $nom): Utilisateur
    {
        return (new Utilisateur())
            ->setId($id)
            ->setNom($nom)
            ->setPrenom('Demo')
            ->setEmail(strtolower(str_replace(' ', '.', $nom)).'@example.com')
            ->setMotDePasse('secret')
            ->setRole('AGRICULTEUR')
            ->setCin(12345678)
            ->setSignature('signature')
            ->setDateCreation(new \DateTime());
    }

    private function setEntityId(object $entity, int $id): void
    {
        $property = new \ReflectionProperty($entity, 'id');
        $property->setValue($entity, $id);
    }
}
