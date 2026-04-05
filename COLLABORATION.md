# Module Collaboration – AgriFlow Web (Symfony 6.4)

Implémentation du module Collaboration (Front Office + Back Office) dans le projet Symfony, équivalent à la branche Java `collaboration` du dépôt `Esprit-PIDEV-3A14-2025-2026-AgriFlow`.

---

## Lancer la migration

```bash
php bin/console doctrine:migrations:migrate
```

---

## Routes principales

### Front Office (`/collab`)

| Route | URL | Description |
|---|---|---|
| `collab_index` | `GET /collab` | Explorer les demandes ouvertes (paginé) |
| `collab_show` | `GET /collab/{id}` | Détail d'une demande |
| `collab_new` | `GET|POST /collab/new` | Publier une demande *(auth requis)* |
| `collab_edit` | `GET|POST /collab/{id}/edit` | Modifier sa demande *(propriétaire)* |
| `collab_apply` | `GET|POST /collab/{id}/apply` | Postuler à une demande *(auth requis)* |
| `collab_my_requests` | `GET /collab/my/requests` | Mes demandes *(auth requis)* |
| `collab_my_applications` | `GET /collab/my/applications` | Mes candidatures *(auth requis)* |
| `collab_delete` | `POST /collab/{id}/delete` | Supprimer sa demande *(propriétaire)* |

### Back Office (`/admin/collab`)

| Route | URL | Description |
|---|---|---|
| `admin_collab_requests` | `GET /admin/collab/requests` | Liste filtrée des demandes (ROLE_ADMIN) |
| `admin_collab_request_show` | `GET /admin/collab/requests/{id}` | Détail + candidatures avec score IA |
| `admin_collab_request_status` | `POST /admin/collab/requests/{id}/status` | Changer le statut d'une demande |
| `admin_collab_request_delete` | `POST /admin/collab/requests/{id}/delete` | Supprimer une demande |
| `admin_collab_applications` | `GET /admin/collab/applications` | Liste filtrée des candidatures |
| `admin_collab_application_status` | `POST /admin/collab/applications/{id}/status` | Changer le statut (`pending`/`accepted`/`rejected`) |
| `admin_collab_application_delete` | `POST /admin/collab/applications/{id}/delete` | Supprimer une candidature |

---

## Architecture

```
src/
├── Entity/
│   ├── CollabRequest.php        # Demande de collaboration
│   └── CollabApplication.php   # Candidature à une demande
├── Repository/
│   ├── CollabRequestRepository.php
│   └── CollabApplicationRepository.php
├── Form/
│   ├── CollabRequestType.php
│   └── CollabApplicationType.php
├── Service/
│   ├── CollabRequestService.php      # Logique: publier, modifier, supprimer
│   ├── CollabApplicationService.php  # Logique: postuler, mettre à jour statut
│   ├── CandidateMatchingService.php  # Score IA candidat ↔ demande
│   └── ContentModerationService.php  # Modération contenu (extensible API)
└── Controller/
    ├── CollabController.php           # FO
    └── Admin/CollabAdminController.php  # BO

templates/
├── collab/
│   ├── index.html.twig          # Explorer les demandes
│   ├── show.html.twig           # Détail
│   ├── new.html.twig            # Formulaire publication
│   ├── edit.html.twig           # Formulaire édition
│   ├── apply.html.twig          # Formulaire candidature
│   ├── my_requests.html.twig    # Mes demandes
│   └── my_applications.html.twig  # Mes candidatures
└── admin/collab/
    ├── requests.html.twig        # BO liste demandes
    ├── request_show.html.twig    # BO détail + ranking IA
    └── applications.html.twig   # BO liste candidatures

migrations/
└── Version20260405000001.php    # Création tables collab_requests + collab_applications

tests/Service/
├── CollabRequestServiceTest.php
├── CollabApplicationServiceTest.php
├── CandidateMatchingServiceTest.php
└── ContentModerationServiceTest.php
```

---

## Règles métier appliquées

- **Pas d'auto-candidature** : impossible de postuler à sa propre demande
- **Demande ouverte** : seules les demandes avec statut `open` acceptent des candidatures
- **Date limite** : impossible de postuler si `end_date` est dépassée
- **Pas de double candidature** : contrainte DB (`UNIQUE INDEX uq_candidate_request`) + vérification applicative
- **Modération contenu** : détection de mots-clés suspects avant publication (`ContentModerationService`)

---

## Configuration IA / API (point d'extension)

Le service `ContentModerationService` fournit un fallback déterministe par liste de mots-clés.  
Pour brancher une API réelle (Gemini, Perspective API, etc.), il suffit de :

1. Créer un service adaptateur qui implémente la même interface `isFlagged(string $text): bool`
2. Remplacer l'injection dans `CollabRequestService` via `config/services.yaml`

Le service `CandidateMatchingService` calcule un score local (expérience, salaire, disponibilité).  
Pour une version IA, le même pattern d'injection s'applique.

---

## Tests

```bash
php bin/phpunit tests/Service/CollabRequestServiceTest.php
php bin/phpunit tests/Service/CollabApplicationServiceTest.php
php bin/phpunit tests/Service/CandidateMatchingServiceTest.php
php bin/phpunit tests/Service/ContentModerationServiceTest.php
```

26 tests / 47 assertions — tous passent ✅
