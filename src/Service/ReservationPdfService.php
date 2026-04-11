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

        // pdf: twig y7adher html w Dompdf y7awlou fichier PDF
        $html = $this->twig->render('pdf/reservation_quote.html.twig', [
            'reservation' => $reservation,
            'annonce' => $reservation->getAnnonce(),
            'client' => $client,
            'vendeur' => $vendeur,
            'generatedAt' => new \DateTimeImmutable(),
            'logoPath' => 'file:///'.str_replace('\\', '/', $this->projectDir.'/public/template/assets/img/logo2-header.png'),
        ]);

        return new Response($this->dompdfWrapper->getPdf($html), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
    }
}
