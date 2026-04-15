<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\UtilisateurRepository;
use Nucleos\DompdfBundle\Wrapper\DompdfWrapperInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ReservationPdfService
{
    public function __construct(
        private readonly DompdfWrapperInterface $dompdfWrapper,
        private readonly Environment $twig,
        private readonly UtilisateurRepository $utilisateurRepository,
        private readonly AnnonceBusinessDiagnosticService $businessDiagnosticService,
        private readonly AnnonceEnvironmentInsightService $environmentInsightService,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    public function streamReservationQuote(Reservation $reservation, string $filenamePrefix = 'devis-reservation'): Response
    {
        // pdf: njiib client w vendeur bech devis ykoun mafhoum lel admin/vendeur/client
        $client = $this->utilisateurRepository->find($reservation->getClientId());
        $vendeur = $this->utilisateurRepository->find($reservation->getProprietaireId());
        $filename = sprintf('%s-%d.pdf', $filenamePrefix, $reservation->getId() ?? 0);
        $annonce = $reservation->getAnnonce();
        $environmentInsights = null;
        $businessDiagnostic = null;

        if (null !== $annonce) {
            // diagnostic: devis yhez nafs analyse metier mta3 fiche annonce bech PDF yban plus pro
            $environmentInsights = $this->environmentInsightService->buildForAnnonce($annonce);
            $businessDiagnostic = $this->businessDiagnosticService->buildForAnnonce($annonce, $environmentInsights);
        }

        $logoFile = $this->projectDir.'/public/uploads/logo/logo.png';

        if (!is_file($logoFile)) {
            $logoFile = $this->projectDir.'/public/template/assets/img/logo2-header.png';
        }

        // pdf: twig y7adher html w Dompdf y7awlou fichier PDF
        $html = $this->twig->render('pdf/reservation_quote.html.twig', [
            'reservation' => $reservation,
            'annonce' => $annonce,
            'client' => $client,
            'vendeur' => $vendeur,
            'environmentInsights' => $environmentInsights,
            'businessDiagnostic' => $businessDiagnostic,
            'generatedAt' => new \DateTimeImmutable(),
            'logoPath' => 'file:///'.str_replace('\\', '/', $logoFile),
        ]);

        return new Response($this->dompdfWrapper->getPdf($html), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }
}
