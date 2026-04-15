<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use App\Service\AnnonceGeocodingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:marketplace:geocode-annonces',
    description: 'Enrichit les anciennes annonces Marketplace avec latitude, longitude et adresse normalisee.'
)]
final class MarketplaceGeocodeAnnoncesCommand extends Command
{
    public function __construct(
        private readonly AnnonceRepository $annonceRepository,
        private readonly AnnonceGeocodingService $annonceGeocodingService,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'ID annonce precise a geocoder.')
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Nombre maximum d annonces a traiter.', 20)
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Teste sans sauvegarder en base.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $id = $input->getOption('id');
        $limit = max(1, (int) $input->getOption('limit'));

        // demo: commande hedhi tkhalli Open-Meteo yban fil fiche annonce
        $annonces = null !== $id
            ? $this->findOneAnnonce((int) $id)
            : $this->findAnnoncesWithoutCoordinates($limit);

        if ([] === $annonces) {
            $io->success('Aucune annonce Marketplace a geocoder.');

            return Command::SUCCESS;
        }

        $rows = [];

        foreach ($annonces as $index => $annonce) {
            $before = [
                'latitude' => $annonce->getLatitude(),
                'longitude' => $annonce->getLongitude(),
                'localisationNormalisee' => $annonce->getLocalisationNormalisee(),
            ];

            // geocoding: n3awdou n3amrou coordonnees lel annonces l9dom
            $outcome = $this->annonceGeocodingService->enrichAnnonce($annonce);

            $rows[] = [
                $annonce->getId(),
                $annonce->getTitre(),
                $annonce->getLocalisation(),
                $outcome['status'],
                null !== $annonce->getLatitude() ? (string) $annonce->getLatitude() : '-',
                null !== $annonce->getLongitude() ? (string) $annonce->getLongitude() : '-',
                $outcome['message'] ?? '-',
            ];

            if ($dryRun) {
                // fallback: ken API tfaili ma nkassrouch data l9dima
                $annonce
                    ->setLatitude($before['latitude'])
                    ->setLongitude($before['longitude'])
                    ->setLocalisationNormalisee($before['localisationNormalisee']);
            }

            if ($index < count($annonces) - 1) {
                usleep(1000000);
            }
        }

        $io->table(
            ['ID', 'Titre', 'Localisation', 'Statut', 'Latitude', 'Longitude', 'Message'],
            $rows
        );

        if ($dryRun) {
            $io->warning('Dry-run: aucune modification sauvegardee.');

            return Command::SUCCESS;
        }

        $this->entityManager->flush();
        $io->success(sprintf('%d annonce(s) geocodee(s).', count($annonces)));

        return Command::SUCCESS;
    }

    /**
     * @return list<Annonce>
     */
    private function findOneAnnonce(int $id): array
    {
        $annonce = $this->annonceRepository->find($id);

        return $annonce instanceof Annonce ? [$annonce] : [];
    }

    /**
     * @return list<Annonce>
     */
    private function findAnnoncesWithoutCoordinates(int $limit): array
    {
        return $this->annonceRepository
            ->createQueryBuilder('a')
            ->andWhere('a.latitude IS NULL OR a.longitude IS NULL')
            ->orderBy('a.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
