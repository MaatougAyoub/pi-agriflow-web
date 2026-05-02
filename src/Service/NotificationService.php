<?php

namespace App\Service;

use App\Entity\Diagnosti;
use App\Entity\PlansIrrigation;
use App\Entity\Reclamation;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

class NotificationService
{
    private LoggerInterface $logger;

    public function __construct(
        private HubInterface $hub,
        private MailerInterface $mailer,
        LoggerInterface $logger,
        private string $adminEmail = 'noreply@agri-smart.com'
    ) {
        $this->logger = $logger;
    }

    // =============================================
    // MERCURE : Notifications temps réel
    // =============================================

    public function notifyNewDiagnostic(Diagnosti $diagnostic): void
    {
        try {
            $update = new Update(
                'diagnostics/nouveau',
                $this->encodePayload([
                    'type'        => 'nouveau_diagnostic',
                    'id'          => $diagnostic->getIdDiagnostic(),
                    'nomCulture'  => $diagnostic->getNomCulture(),
                    'description' => mb_substr($diagnostic->getDescription() ?? '', 0, 100),
                    'date'        => $diagnostic->getDateEnvoi()?->format('d/m/Y H:i') ?? (new \DateTime())->format('d/m/Y H:i'),
                    'agriculteur' => $diagnostic->getUtilisateur()?->getEmail() ?? 'Inconnu',
                    'message'     => sprintf(
                        'Nouveau diagnostic pour "%s" envoyé par %s',
                        $diagnostic->getNomCulture(),
                        $diagnostic->getUtilisateur()?->getEmail() ?? 'un agriculteur'
                    ),
                ])
            );
            $this->hub->publish($update);
            $this->logger->info('[MERCURE OK] notifyNewDiagnostic envoyé');
        } catch (\Exception $e) {
            $this->logger->error('[MERCURE ERREUR] notifyNewDiagnostic: ' . $e->getMessage());
        }
    }

    public function notifyDiagnosticResponse(Diagnosti $diagnostic): void
    {
        $userId = $diagnostic->getUtilisateur()?->getId();
        if (!$userId) {
            $this->logger->warning('[MERCURE] notifyDiagnosticResponse: pas de userId');
            return;
        }

        try {
            $update = new Update(
                sprintf('agriculteur/%d/diagnostics', $userId),
                $this->encodePayload([
                    'type'       => 'diagnostic_traite',
                    'id'         => $diagnostic->getIdDiagnostic(),
                    'nomCulture' => $diagnostic->getNomCulture(),
                    'message'    => sprintf(
                        'Votre diagnostic pour "%s" a reçu une réponse de l\'expert !',
                        $diagnostic->getNomCulture()
                    ),
                    'date'       => (new \DateTime())->format('d/m/Y H:i'),
                ])
            );
            $this->hub->publish($update);
            $this->logger->info('[MERCURE OK] notifyDiagnosticResponse envoyé à user ' . $userId);
        } catch (\Exception $e) {
            $this->logger->error('[MERCURE ERREUR] notifyDiagnosticResponse: ' . $e->getMessage());
        }
    }

    public function notifyNewIrrigationPlan(PlansIrrigation $plan): void
    {
        try {
            $planId = $this->getPlanIdentifier($plan);

            $update = new Update(
                'irrigation/nouveau',
                $this->encodePayload([
                    'type'       => 'nouveau_plan',
                    'id'         => $planId,
                    'nomCulture' => $plan->getNomCulture(),
                    'volumeEau'  => $plan->getVolumeEauPropose(),
                    'message'    => sprintf(
                        'Nouveau plan d\'irrigation pour "%s" (Volume: %s L)',
                        $plan->getNomCulture(),
                        $plan->getVolumeEauPropose()
                    ),
                    'date'       => $plan->getDateDemande()?->format('d/m/Y H:i') ?? (new \DateTime())->format('d/m/Y H:i'),
                ])
            );
            $this->hub->publish($update);
            $this->logger->info('[MERCURE OK] notifyNewIrrigationPlan envoyé');
        } catch (\Exception $e) {
            $this->logger->error('[MERCURE ERREUR] notifyNewIrrigationPlan: ' . $e->getMessage());
        }
    }

    public function notifyIrrigationPlanFilled(PlansIrrigation $plan): void
    {
        $culture = $plan->getCulture();
        $proprietaire = $culture?->getProprietaire();
        $proprietaireId = $proprietaire?->getId();

        if (!$proprietaireId) {
            $this->logger->warning('[MERCURE] notifyIrrigationPlanFilled: pas de propriétaire');
            return;
        }

        try {
            $planId = $this->getPlanIdentifier($plan);

            $update = new Update(
                sprintf('agriculteur/%d/irrigation', $proprietaireId),
                $this->encodePayload([
                    'type'    => 'plan_mis_a_jour',
                    'id'      => $planId,
                    'nom'     => $plan->getNomCulture(),
                    'message' => sprintf(
                        'Votre plan d\'irrigation pour "%s" a été rempli par l\'expert !',
                        $plan->getNomCulture()
                    ),
                    'date'    => (new \DateTime())->format('d/m/Y H:i'),
                ])
            );
            $this->hub->publish($update);
            $this->logger->info('[MERCURE OK] notifyIrrigationPlanFilled envoyé à user ' . $proprietaireId);
        } catch (\Exception $e) {
            $this->logger->error('[MERCURE ERREUR] notifyIrrigationPlanFilled: ' . $e->getMessage());
        }
    }

    public function notifyNewReclamation(Reclamation $reclamation): void
    {
        $author = $reclamation->getUtilisateur();
        $authorIdentity = trim((string) (($author?->getNom() ?? '') . ' ' . ($author?->getPrenom() ?? '')));
        if ($authorIdentity === '') {
            $authorIdentity = $author?->getEmail() ?? 'Utilisateur inconnu';
        }

        try {
            $update = new Update(
                'admin/reclamations',
                $this->encodePayload([
                    'type' => 'nouvelle_reclamation',
                    'id' => $reclamation->getId(),
                    'statut' => $reclamation->getStatut(),
                    'categorie' => $reclamation->getCategorie(),
                    'titre' => $reclamation->getTitre(),
                    'auteur' => $authorIdentity,
                    'date' => (new \DateTime())->format('d/m/Y H:i'),
                    'message' => sprintf(
                        'Nouvelle reclamation EN_ATTENTE de %s: %s',
                        $authorIdentity,
                        (string) $reclamation->getTitre()
                    ),
                ])
            );

            $this->hub->publish($update);
            $this->logger->info('[MERCURE OK] notifyNewReclamation envoye');
        } catch (\Exception $e) {
            $this->logger->error('[MERCURE ERREUR] notifyNewReclamation: '.$e->getMessage());
        }
    }

    // =============================================
    // MAILER : Envoi d'emails
    // =============================================

    public function sendDiagnosticResponseEmail(Diagnosti $diagnostic): void
    {
        $agriculteur = $diagnostic->getUtilisateur();
        if (!$agriculteur || !$agriculteur->getEmail()) {
            $this->logger->warning('[MAIL] sendDiagnosticResponseEmail: pas d\'email agriculteur');
            return;
        }

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($this->adminEmail, 'AgriSmart - Plateforme Agricole'))
                ->to(new Address($agriculteur->getEmail()))
                ->subject(sprintf('Votre diagnostic pour "%s" est prêt !', $diagnostic->getNomCulture()))
                ->htmlTemplate('emails/diagnostic_response.html.twig')
                ->context([
                    'diagnostic'  => $diagnostic,
                    'agriculteur' => $agriculteur,
                    'dateReponse' => $diagnostic->getDateReponse() ?? new \DateTime(),
                ]);

            $this->mailer->send($email);
            $this->logger->info('[MAIL OK] Email diagnostic envoyé à ' . $agriculteur->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('[MAIL ERREUR] sendDiagnosticResponseEmail: ' . $e->getMessage());
        }
    }

    public function sendIrrigationPlanEmail(PlansIrrigation $plan): void
    {
        $culture = $plan->getCulture();
        if (!$culture) {
            $this->logger->warning('[MAIL] sendIrrigationPlanEmail: pas de culture');
            return;
        }

        $proprietaire = $culture->getProprietaire();
        if (!$proprietaire || !$proprietaire->getEmail()) {
            $this->logger->warning('[MAIL] sendIrrigationPlanEmail: pas d\'email propriétaire');
            return;
        }

        try {
            $email = (new TemplatedEmail())
                ->from(new Address($this->adminEmail, 'AgriSmart - Plateforme Agricole'))
                ->to(new Address($proprietaire->getEmail()))
                ->subject(sprintf('Plan d\'irrigation pour "%s" prêt !', $plan->getNomCulture()))
                ->htmlTemplate('emails/irrigation_plan_ready.html.twig')
                ->context([
                    'plan'         => $plan,
                    'proprietaire' => $proprietaire,
                ]);

            $this->mailer->send($email);
            $this->logger->info('[MAIL OK] Email irrigation envoyé à ' . $proprietaire->getEmail());
        } catch (\Exception $e) {
            $this->logger->error('[MAIL ERREUR] sendIrrigationPlanEmail: ' . $e->getMessage());
        }
    }

    // =============================================
    // HELPER : Récupérer l'identifiant du plan
    // =============================================

    /**
     * Récupère l'identifiant du plan de manière sûre.
     * PlansIrrigation n'a PAS de getId(), on essaie les getters disponibles.
     */
    private function getPlanIdentifier(PlansIrrigation $plan): int
    {
        return $plan->getPlanId() ?? $plan->getIdCulture() ?? 0;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function encodePayload(array $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR);
    }
}
