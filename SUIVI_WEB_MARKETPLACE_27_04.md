# Suivi Web 27/04 - Marketplace

## Objectif

- Module presente: Marketplace
- Base de travail: dernier `origin/main`
- Scope: tests unitaires, phpStan, DoctrineDoctor, valeur ajoutee IA
- Hors scope: modules des autres membres

## Preuves techniques a montrer

```powershell
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Marketplace --testdox
C:\xampp\php\php.exe vendor\bin\phpstan analyse --configuration=phpstan.neon.dist --memory-limit=1G
C:\xampp\php\php.exe bin\console lint:container
C:\xampp\php\php.exe bin\console lint:twig templates
C:\xampp\php\php.exe bin\console debug:config doctrine_doctor
```

Resultats attendus:

- PHPUnit Marketplace: 25 tests, 105 assertions
- phpStan Marketplace: no errors
- Container Symfony: OK
- Twig: OK
- DoctrineDoctor: bundle actif en dev
- Secret check: aucune cle Groq dans les fichiers Git

## Tests unitaires Marketplace

- `AnnonceAiAssistantService`: cle absente, provider Groq, provider fallback, JSON brut, JSON entoure de texte, score borne
- `AnnonceGeocodingService`: geocodage reussi, aucun resultat, panne API non bloquante
- `ReservationPdfService`: generation PDF, headers, nom fichier, contexte Twig, annonce nullable
- `MarketplaceGeocodeAnnoncesCommand`: `--id`, `--limit`, `--dry-run`, aucune annonce trouvee
- Services metier deja couverts: pricing reservation, diagnostic annonce, demandes vendeur

Phrase a dire:

```text
Pour ce suivi, j'ai isole une suite de tests Marketplace pour ne pas dependre des modules des autres. Elle couvre l'IA, le geocodage, le PDF, la commande de backfill et la logique metier.
```

## Analyse statique phpStan

- Analyse limitee au scope Marketplace
- Niveau: 5
- Fichiers analyses: controllers Marketplace, services Marketplace, repositories, forms, entities Annonce/Reservation, commande et tests
- Objectif: detecter les erreurs de types et les problemes avant execution

Phrase a dire:

```text
phpStan est configure sur mon perimetre Marketplace. Je n'ai pas masque les erreurs avec une baseline: j'ai corrige ce qui etait dans mon scope.
```

## DoctrineDoctor

Routes a ouvrir en environnement dev avec le profiler:

- `/marketplace`
- `/marketplace/annonce/{id}`
- `/mon-espace/annonces/nouvelle`
- `/mon-espace/demandes`
- `/admin/reservations/{id}/devis`

Ce qu'il faut observer:

- nombre de requetes Doctrine
- relations chargees
- risques N+1
- requetes lentes
- hydratation excessive

Phrase a dire:

```text
DoctrineDoctor est active dans le profiler Symfony. Je l'utilise pour verifier les requetes ORM du Marketplace et detecter les problemes de performance comme N+1 ou requetes lentes.
```

## Valeur ajoutee IA

- Assistant IA Marketplace avec Groq
- Suggestions: titre, description, categorie, unite prix
- Analyse qualite: score et conseil
- Aucun enregistrement automatique
- Fallback propre si la cle manque ou si l'appel echoue
- Cle dans `.env.local`, jamais dans GitHub

Phrase a dire:

```text
L'IA n'est pas un auto-save. Elle propose une amelioration d'annonce, mais l'utilisateur garde la validation finale. Si la cle manque, le formulaire reste utilisable.
```

## Scenario de demo rapide

1. Lancer MySQL et Symfony.
2. Ouvrir `/marketplace`.
3. Ouvrir une fiche annonce avec localisation.
4. Montrer diagnostic, meteo, qualite air et offres similaires.
5. Ouvrir `/mon-espace/annonces/nouvelle`.
6. Montrer bouton IA.
7. Ouvrir `/mon-espace/demandes`.
8. Montrer devis PDF.
9. Ouvrir une route avec `_profiler` et l'onglet DoctrineDoctor.
10. Montrer les commandes PHPUnit/phpStan vertes.

## Si le prof parle des tests Collaboration

Reponse courte:

```text
La suite globale du projet contient des tests Collaboration casses dans le dernier main. Pour ne pas modifier le travail d'un autre membre, j'ai isole et valide ma testsuite Marketplace. Mon perimetre est vert: PHPUnit Marketplace, phpStan Marketplace, lint Symfony et DoctrineDoctor.
```

## Checklist avant validation

- Dernier `main` recupere
- MySQL lance
- Symfony lance sur `127.0.0.1:8000`
- `.env.local` contient la cle Groq si demo IA reelle
- `phpunit --testsuite Marketplace` vert
- `phpstan analyse` vert
- `lint:container` vert
- `lint:twig templates` vert
- DoctrineDoctor visible dans le profiler
- Ne pas ouvrir `.env.local` devant le prof
