<?php

namespace App\Controller;

use App\Entity\Culture;
use App\Entity\Parcelle;
use App\Entity\Utilisateur;
use App\Repository\CultureHistoryRepository;
use App\Repository\CultureRepository;
use App\Repository\ParcelleRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cultures', name: 'app_admin_culture_')]
#[IsGranted('ROLE_ADMIN')]
final class AdminCultureController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        UtilisateurRepository $utilisateurRepository,
    ): Response {
        $filters = [
            'search' => (string) $request->query->get('search', ''),
            'type_culture' => (string) $request->query->get('type_culture', ''),
            'parcelle_id' => (string) $request->query->get('parcelle_id', ''),
            'etat' => (string) $request->query->get('etat', ''),
            'sort' => (string) $request->query->get('sort', 'date_creation'),
            'direction' => (string) $request->query->get('direction', 'desc'),
        ];
        $parcelles = $parcelleRepository->findFilteredForAdmin([
            'search' => '',
            'type_terre' => '',
            'sort' => 'nom',
            'direction' => 'asc',
        ]);

        $cultures = $cultureRepository->findFilteredForAdmin($filters);
        $ownerNamesById = $this->buildUserDisplayNamesByIds($cultures, $utilisateurRepository, static fn (Culture $culture): ?int => $culture->getProprietaireId());
        $buyerNamesById = $this->buildUserDisplayNamesByIds($cultures, $utilisateurRepository, static fn (Culture $culture): ?int => $culture->getAcheteurId());

        return $this->render('admin/culture/index.html.twig', [
            'cultures' => $cultures,
            'filters' => $filters,
            'type_culture_choices' => $cultureRepository->findTypeCultureChoicesForAdmin(),
            'etat_choices' => $cultureRepository->findEtatChoicesForAdmin(),
            'parcelles' => $parcelles,
            'parcelles_by_id' => $this->indexParcellesById($parcelles),
            'owner_names_by_id' => $ownerNamesById,
            'buyer_names_by_id' => $buyerNamesById,
            'sort_choices' => [
                'nom' => 'Nom',
                'superficie' => 'Superficie',
                'recolte_estime' => 'Recolte estimee',
                'date_creation' => 'Date de creation',
            ],
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        int $id,
        CultureRepository $cultureRepository,
        ParcelleRepository $parcelleRepository,
        UtilisateurRepository $utilisateurRepository,
        CultureHistoryRepository $cultureHistoryRepository,
    ): Response {
        $culture = $this->findCulture($id, $cultureRepository);
        $parcelle = $culture->getParcelleId() ? $parcelleRepository->find($culture->getParcelleId()) : null;
        $proprietaire = $culture->getProprietaireId() ? $utilisateurRepository->find($culture->getProprietaireId()) : null;
        $acheteur = $culture->getAcheteurId() ? $utilisateurRepository->find($culture->getAcheteurId()) : null;

        return $this->render('admin/culture/show.html.twig', [
            'culture' => $culture,
            'parcelle' => $parcelle,
            'proprietaire' => $proprietaire instanceof Utilisateur ? $proprietaire : null,
            'acheteur' => $acheteur instanceof Utilisateur ? $acheteur : null,
            'history_entries' => $cultureHistoryRepository->findByCultureOrderedDesc($culture),
            'can_delete' => $culture->isModifiableOrSuppressible(),
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(
        int $id,
        Request $request,
        CultureRepository $cultureRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $culture = $this->findCulture($id, $cultureRepository);

        if (!$culture->isModifiableOrSuppressible()) {
            $this->addFlash('warning', 'Suppression impossible : cette culture est en lecture seule car elle est vendue ou recoltee.');

            return $this->redirectToRoute('app_admin_culture_show', ['id' => $culture->getId()]);
        }

        if ($this->isCsrfTokenValid('admin_delete_culture_'.$culture->getId(), (string) $request->request->get('_token'))) {
            $entityManager->remove($culture);
            $entityManager->flush();

            $this->addFlash('success', 'La culture a ete supprimee.');
        }

        return $this->redirectToRoute('app_admin_culture_index');
    }

    /**
     * @param Parcelle[] $parcelles
     * @return array<int, Parcelle>
     */
    private function indexParcellesById(array $parcelles): array
    {
        $indexedParcelles = [];

        foreach ($parcelles as $parcelle) {
            if (null === $parcelle->getId()) {
                continue;
            }

            $indexedParcelles[$parcelle->getId()] = $parcelle;
        }

        return $indexedParcelles;
    }

    private function findCulture(int $id, CultureRepository $cultureRepository): Culture
    {
        $culture = $cultureRepository->find($id);

        if (!$culture instanceof Culture) {
            throw $this->createNotFoundException('Culture introuvable.');
        }

        return $culture;
    }

    /**
     * @param Culture[] $cultures
     * @param callable(Culture): ?int $idResolver
     * @return array<int, string>
     */
    private function buildUserDisplayNamesByIds(array $cultures, UtilisateurRepository $utilisateurRepository, callable $idResolver): array
    {
        $namesById = [];
        $userIds = [];

        foreach ($cultures as $culture) {
            $userId = $idResolver($culture);

            if (null !== $userId) {
                $userIds[$userId] = true;
            }
        }

        foreach (array_keys($userIds) as $userId) {
            $user = $utilisateurRepository->find($userId);

            if (!$user instanceof Utilisateur) {
                continue;
            }

            $fullName = trim(sprintf('%s %s', $user->getPrenom() ?? '', $user->getNom() ?? ''));
            $namesById[$userId] = '' !== $fullName ? $fullName : ((string) $user->getEmail() ?: 'Utilisateur');
        }

        return $namesById;
    }
}
