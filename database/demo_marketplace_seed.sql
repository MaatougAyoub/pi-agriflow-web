SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM reservations;
DELETE FROM annonces;

ALTER TABLE reservations AUTO_INCREMENT = 1;
ALTER TABLE annonces AUTO_INCREMENT = 1;

INSERT INTO annonces (
    id,
    titre,
    description,
    type,
    statut,
    prix,
    categorie,
    image_url,
    localisation,
    proprietaire_id,
    quantite_disponible,
    unite_prix,
    date_creation,
    date_modification
) VALUES
(
    1,
    'Tracteur New Holland T5',
    'Tracteur moderne disponible pour location journaliere avec bon rendement pour les travaux agricoles moyens et grands.',
    'LOCATION',
    'DISPONIBLE',
    280.00,
    'Materiel agricole',
    'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?auto=format&fit=crop&w=1200&q=80',
    'Ariana',
    12,
    2,
    'jour',
    NOW(),
    NOW()
),
(
    2,
    'Pompe d irrigation mobile',
    'Pompe mobile pour irrigation de parcelles avec installation rapide et debit stable.',
    'LOCATION',
    'DISPONIBLE',
    95.00,
    'Irrigation',
    'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1200&q=80',
    'Nabeul',
    18,
    4,
    'jour',
    NOW(),
    NOW()
),
(
    3,
    'Lot de caisses de tomates bio',
    'Annonce de vente pour un lot de tomates bio pretes pour livraison au marche ou au client final.',
    'VENTE',
    'DISPONIBLE',
    45.00,
    'Produits frais',
    'https://images.unsplash.com/photo-1546470427-e5ac89cd0b20?auto=format&fit=crop&w=1200&q=80',
    'Sousse',
    27,
    50,
    'piece',
    NOW(),
    NOW()
);

INSERT INTO reservations (
    id,
    demandeur_id,
    proprietaire_id,
    date_debut,
    date_fin,
    quantite,
    prix_total,
    commission,
    statut,
    message,
    date_creation,
    annonce_id
) VALUES
(
    1,
    31,
    12,
    '2026-04-10',
    '2026-04-12',
    1,
    882.00,
    42.00,
    'EN_ATTENTE',
    'Nheb n7ajjem bih khidmet terrain pendant trois jours.',
    NOW(),
    1
),
(
    2,
    44,
    27,
    '2026-04-08',
    '2026-04-08',
    20,
    945.00,
    45.00,
    'ACCEPTEE',
    'Commande de test pour montrer le cas vente avec commission.',
    NOW(),
    3
);

SET FOREIGN_KEY_CHECKS = 1;
