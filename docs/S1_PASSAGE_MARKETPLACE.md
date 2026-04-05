# Passage S1 Marketplace

## Etat reel de ma partie

- Module presente: Marketplace
- Projet ouvert: `C:\Users\mino2\OneDrive\Desktop\Symfony 1\agriflow-integre`
- Branche git a montrer si besoin: `MarketPlace`
- Dernier commit Marketplace pousse: `2fd20bc`

Ce que je couvre dans l integration:

- `Annonce` + `Reservation`
- CRUD vendeur
- CRUD admin
- validation serveur Symfony
- front-office marketplace
- espace vendeur agriculteur
- logique metier de reservation / stock / statut

Ce que je ne dois pas presenter comme si c etait mon travail perso:

- tout le module user
- tout le module site hors marketplace
- les fichiers SQL

## Commande de lancement

Ouvrir le projet dans VS Code:

`C:\Users\mino2\OneDrive\Desktop\Symfony 1\agriflow-integre`

Lancer le serveur:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public public/index.php
```

Avant la seance:

- verifier que MySQL est bien demarre dans XAMPP
- faire `Ctrl + F5`
- ne pas ouvrir `agriflow9.sql`
- ne pas ouvrir `src/Controller/SiteController.php`

## Comptes de demo

- `client.demo@agriflow.tn` / `Admin123`
- `admin.demo@agriflow.tn` / `Admin123`

## Scenario exact a montrer

### 1. Introduction

Dire:

`Ena bech n9adem partie mte3i fil projet integre AgriFlow. El module mte3i houwa Marketplace, yani gestion des annonces w des reservations. Khedemt b Symfony, Doctrine, Twig, w 3malt zouz entites principales: Annonce w Reservation, ma3 relation binethom.`

### 2. Partie agriculteur

1. Ouvrir `http://127.0.0.1:8000/login`
2. Se connecter avec `client.demo@agriflow.tn`
3. Ouvrir `http://127.0.0.1:8000/marketplace`
4. Ouvrir une annonce
5. Dire:

`Houni l user AGRICULTEUR ynajjem yal3ab zouz roles metier. Ynajjem ykoun vendeur, yani ypublishi annonce mte3ou w ygiriha, w zeda ynajjem ykoun client w ya3mel reservation 3la annonce mte3 user e5er.`

6. Ouvrir `http://127.0.0.1:8000/mon-espace/annonces`
7. Montrer `Mes annonces`
8. Ouvrir `Publier une annonce`
9. Montrer qu il y a validation visible si les donnees sont invalides
10. Revenir a `Mes annonces`
11. Ouvrir `http://127.0.0.1:8000/mon-espace/demandes`
12. Montrer `Demandes recues`
13. Dire:

`Ki yji client ya3mel demande, el demande tamchi En attente. Ba3d vendeur ynajjem ya9belha wala yrefodhha. Ki tet9bal, el stock yethabet automatiquement, w ken ywali stock 0, statut mte3 l annonce yetbadel selon vente wala location.`

14. Ouvrir `http://127.0.0.1:8000/mes-reservations`
15. Montrer les demandes envoyees par l agriculteur comme client

### 3. Partie admin

1. Se deconnecter
2. Se connecter avec `admin.demo@agriflow.tn`
3. Ouvrir `http://127.0.0.1:8000/admin`
4. Montrer `/admin/annonces`
5. Montrer `/admin/reservations`
6. Dire:

`Houni ADMIN mahouche vendeur simple. Houni andou vue globale 3la les annonces lkol w les reservations lkol, donc supervision globale mte3 l application.`

## Regle de passage

- commencer par agriculteur
- ba3d admin
- ne pas partir dans les autres modules
- si le prof y9ollek user module, t9oul:

`Module user mawjouda fil integration, ama ma responsabilite principale houni hiya Marketplace.`

## Reponses prates aux questions

### Pourquoi un service ?

`5ater logique metier ma lazimhech tkoun fi controller. Haka code ywali adh7a, reusable, w testable.`

Fichier utile:

- `src/Service/SellerMarketplaceService.php`

### Pourquoi un repository personnalise ?

`Bech ma tti7ech page admin ki fama reservation ancienne marbouta b annonce tfas5et.`

Fichier utile:

- `src/Repository/ReservationRepository.php`

### Pourquoi l agriculteur a deux roles metier ?

`5ater marketplace logiqueha haka: nafs user ynajjem ybi3 wala yakhoj offre, w zeda ynajjem yesthak service wala yreservi 3and 8irou.`

Fichiers utiles:

- `src/Controller/MarketplaceController.php`
- `src/Controller/ReservationController.php`
- `src/Controller/Seller/SellerAnnonceController.php`

### Pourquoi une annonce peut ne pas apparaitre dans /marketplace ?

`5ater public marketplace ywarri ken les annonces Disponible. Ken Reservee, Vendue, wala Louee, tab9a tban fi espace vendeur, ama moch fi catalogue public.`

### Pourquoi tu dis validation serveur ?

`5ater meme ken user y7awel y3addi donnees ghalta, Symfony Validator fil back ywa9efhom. Mouch validation HTML bark.`

Fichiers utiles:

- `src/Entity/Annonce.php`
- `src/Entity/Reservation.php`
- `src/Form/AnnonceFormType.php`
- `src/Form/ReservationFormType.php`

## Fichiers a ouvrir si le prof demande le code

- `src/Entity/Annonce.php`
- `src/Entity/Reservation.php`
- `src/Controller/MarketplaceController.php`
- `src/Controller/ReservationController.php`
- `src/Controller/Seller/SellerAnnonceController.php`
- `src/Controller/Seller/SellerRequestController.php`
- `src/Service/SellerMarketplaceService.php`
- `src/Repository/ReservationRepository.php`

## Ce qui repond a la grille

### 1. CRUD sur 2 entites + relation

Oui:

- `Annonce`
- `Reservation`
- relation Doctrine
- CRUD vendeur
- CRUD admin

### 2. Controles de saisie

Oui:

- validation serveur Symfony
- erreurs visibles dans les formulaires
- pas seulement HTML5 / JS

### 3. Fonctionnalites metier de base

Oui:

- recherche publique
- agriculteur vendeur + client
- impossible de reserver sa propre annonce
- demandes recues
- acceptation / refus
- mise a jour du stock et du statut

### 4. Scenario + donnees de test

Oui si je respecte l ordre de demo ci-dessus.

### 5. Partie graphique

Oui pour mon scope:

- front-office marketplace
- espace vendeur
- back-office admin

### 6. Maitrise du sujet

Ca depend de mon oral.

### 7. Valeur ajoutee

Oui:

- plus qu un CRUD simple
- espace vendeur
- logique stock / statut
- roles
- validation visible

### 8. GitHub + integration

Oui:

- branche `MarketPlace`
- commit pousse
- travail integre dans le projet equipe

## Verifications deja faites

- `phpunit` OK
- `lint:twig` OK
- `lint:container` OK
- checks HTTP utiles deja verifies sur:
  - front marketplace
  - espace vendeur
  - back-office admin

