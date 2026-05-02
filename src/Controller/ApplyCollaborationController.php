<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\CollabApplication;
use App\Entity\CollabRequest;
use App\Entity\Utilisateur;
use App\Form\CollabApplicationType;
use App\Repository\CollabApplicationRepository;
use App\Repository\CollabRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/apply-collaboration')]
class ApplyCollaborationController extends AbstractController
{
    #[Route('/{id}', name: 'apply_collaboration', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function apply(
        int $id,
        Request $request,
        CollabRequestRepository $reqRepo,
        CollabApplicationRepository $appRepo,
        EntityManagerInterface $em
    ): Response {
        $collabRequest = $reqRepo->find($id);
        if (!$collabRequest instanceof CollabRequest) {
            throw $this->createNotFoundException('Demande introuvable.');
        }

        $user = $this->getUser();
        if (!$user instanceof Utilisateur) {
            $this->addFlash('error', "Erreur d'authentification: vous devez etre connecte pour postuler a cette offre.");
            return $this->redirectToRoute('app_login');
        }
        $currentUser = $user;

        // Vérifier si déjà postulé
        if ($appRepo->hasApplied($currentUser, $collabRequest)) {
            $this->addFlash('info', 'Vous avez déjà postulé à cette demande.');
            return $this->redirectToRoute('collab_request_details', ['id' => $id]);
        }

        $application = new CollabApplication();
        $application->setCandidate($currentUser);
        $application->setFullName($currentUser->getPrenom() . ' ' . $currentUser->getNom());
        $application->setEmail($currentUser->getEmail());

        $form = $this->createForm(CollabApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $application->setRequest($collabRequest);
                $application->setStatus('PENDING');
                $application->setAppliedAt(new \DateTime());
                $application->setUpdatedAt(new \DateTime());

                $em->persist($application);
                $em->flush();

                $this->addFlash('success', 'Votre candidature a été envoyée avec succès !');

                // Notification (similaire à TelegramNotifier dans ESPRIT-PIDEV)
                $this->addFlash('info', sprintf(
                    'Nouvelle candidature pour la demande : "%s"<br>Candidat : %s<br>Email : %s<br>Téléphone : %s',
                    $collabRequest->getTitle(),
                    $application->getFullName(),
                    $application->getEmail(),
                    $application->getPhone()
                ));

                return $this->redirectToRoute('collab_request_list');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur de base de données: ' . $e->getMessage());
            }
        }

        return $this->render('apply_collaboration/apply.html.twig', [
            'form' => $form,
            'collabRequest' => $collabRequest,
            'requestTitle' => $collabRequest->getTitle()
        ]);
    }

    #[Route('/cancel', name: 'apply_collaboration_cancel', methods: ['POST'])]
    public function cancel(): Response
    {
        $this->addFlash('info', 'Opération annulée.');
        return $this->redirectToRoute('collab_request_list');
    }
}
