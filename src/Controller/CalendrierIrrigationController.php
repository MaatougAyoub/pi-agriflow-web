<?php

namespace App\Controller;

use App\Entity\PlansIrrigation;
use App\Entity\PlansIrrigationJour;
use App\Entity\Utilisateur;
use App\Repository\PlansIrrigationRepository;
use App\Repository\PlansIrrigationJourRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendrier')]
class CalendrierIrrigationController extends AbstractController
{
    // ==================== PAGE CALENDRIER ====================

    #[Route('/irrigation', name: 'calendrier_irrigation')]
    public function index(): Response
    {
        return $this->render('calendrier/irrigation.html.twig');
    }

    // ==================== API JSON POUR FULLCALENDAR ====================

    #[Route('/irrigation/events', name: 'calendrier_irrigation_events', methods: ['GET'])]
    public function events(
        PlansIrrigationRepository $planRepo,
        PlansIrrigationJourRepository $jourRepo
    ): JsonResponse {
        $user = $this->getUser();
        $events = [];

        // Couleurs par statut
        $couleurs = [
            'en_attente' => ['bg' => '#f59e0b', 'border' => '#d97706', 'text' => '#fff'],
            'rempli'     => ['bg' => '#3b82f6', 'border' => '#2563eb', 'text' => '#fff'],
            'approuve'   => ['bg' => '#10b981', 'border' => '#059669', 'text' => '#fff'],
        ];

        // Icônes par statut
        $icones = [
            'en_attente' => '⏳',
            'rempli'     => '💧',
            'approuve'   => '✅',
        ];

        // Récupérer les plans selon le rôle
        if ($this->isGranted('ROLE_EXPERT')) {
            /** @var list<PlansIrrigation> $plans */
            $plans = $planRepo->findAll();
        } else {
            if (!$user instanceof Utilisateur) {
                return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
            }

            $userId = $user->getId();
            if ($userId === null) {
                return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
            }

            /** @var list<PlansIrrigation> $plans */
            $plans = $planRepo->findByProprietaire($userId);
        }

        foreach ($plans as $plan) {
            $statut = $plan->getStatut() ?? 'en_attente';
            $couleur = $couleurs[$statut] ?? $couleurs['en_attente'];
            $icone = $icones[$statut] ?? '📋';
            $nomCulture = $plan->getNomCulture() ?? 'Culture inconnue';
            $volumeEau = $plan->getVolumeEauPropose() ?? 0;

            // Récupérer l'identifiant du plan
            $planId = $plan->getPlanId() ?? $plan->getIdCulture();

            // Événement principal : date de demande du plan
            $dateDemande = $plan->getDateDemande();
            if ($dateDemande) {
                $events[] = [
                    'id'              => 'plan-' . $planId,
                    'title'           => $icone . ' ' . $nomCulture,
                    'start'           => $dateDemande->format('Y-m-d'),
                    'backgroundColor' => $couleur['bg'],
                    'borderColor'     => $couleur['border'],
                    'textColor'       => $couleur['text'],
                    'extendedProps'   => [
                        'planId'     => $planId,
                        'statut'     => $statut,
                        'volumeEau'  => $volumeEau,
                        'nomCulture' => $nomCulture,
                        'type'       => 'plan',
                    ],
                ];
            }

            // Événements journaliers (si le plan a des jours remplis)
            if ($planId) {
                $jours = $jourRepo->findBy(['plan' => $plan]);
                $joursMap = [
                    'LUN' => 'Monday', 'MAR' => 'Tuesday', 'MER' => 'Wednesday',
                    'JEU' => 'Thursday', 'VEN' => 'Friday', 'SAM' => 'Saturday', 'DIM' => 'Sunday',
                ];

                foreach ($jours as $jour) {
                    $jourKey = PlansIrrigationJour::normalizeJourKey($jour->getJour());
                    $eauMm = $jour->getEauMm() ?? 0;
                    $tempsMin = $jour->getTempsMin() ?? 0;

                    if ($eauMm <= 0 && $tempsMin <= 0) {
                        continue; // Pas d'irrigation ce jour
                    }

                    // Calculer la date du jour dans la semaine courante
                    $semaineDebut = $jour->getSemaineDebut();
                    if ($semaineDebut && $jourKey && isset($joursMap[$jourKey])) {
                        $dateJour = \DateTimeImmutable::createFromInterface($semaineDebut);
                        $jourAnglais = $joursMap[$jourKey];

                        // Trouver le bon jour de la semaine
                        while ($dateJour->format('l') !== $jourAnglais) {
                            $dateJour = $dateJour->modify('+1 day');
                        }

                        $events[] = [
                            'id'              => 'jour-' . $planId . '-' . $jourKey,
                            'title'           => '💧 ' . $nomCulture . ' (' . $eauMm . 'mm / ' . $tempsMin . 'min)',
                            'start'           => $dateJour->format('Y-m-d'),
                            'backgroundColor' => '#0ea5e9',
                            'borderColor'     => '#0284c7',
                            'textColor'       => '#fff',
                            'extendedProps'   => [
                                'planId'     => $planId,
                                'nomCulture' => $nomCulture,
                                'eauMm'      => $eauMm,
                                'tempsMin'   => $tempsMin,
                                'tempC'      => $jour->getTempC() ?? 0,
                                'humidite'   => $jour->getHumidite() ?? 0,
                                'pluie'      => $jour->getPluie() ?? 0,
                                'type'       => 'jour',
                            ],
                        ];
                    }
                }
            }
        }

        return new JsonResponse($events);
    }
}
