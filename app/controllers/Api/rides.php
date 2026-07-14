<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Connexion à la base de données
    $pdo = new PDO(
        'mysql:host=localhost;dbname=test;charset=utf8mb4',
        'root',
        ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query 1 : Les trajets
    $trajets = $pdo->query("
SELECT 
  covoiturage_id as id,
  date_depart,
  heure_depart,
  lieu_depart,
  date_arrivee,
  heure_arrivee,
  lieu_arrivee,
  statut,
  nb_place,
  prix_personne,
  is_eco,
  voiture_id,
  created_at
FROM covoiturage
ORDER BY covoiturage_id
        ")->fetchAll(PDO::FETCH_ASSOC);

    // Query 2 : Les voitures
    $voitures = $pdo->query("
SELECT 
    voiture_id as id,
    marque_id,
    proprietaire_id,
    modele,
    immatriculation,
    energie,
    couleur,
    date_premiere_immatriculation
FROM voiture")->fetchAll(PDO::FETCH_ASSOC);

    // Query 3 : Les utilisateurs
    $utilisateurs = $pdo->query("
SELECT 
    utilisateur_id as id,
    nom as Nom,
    prenom as Prenom,
    pseudo as Pseudo,
    photo as PhotoProfil,
    email,
    role_id
FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);

    // Query 4 : Les préférences des conducteurs


    $preferences = $pdo->query("
SELECT 
    preference_id,
    covoiturage_id,
    bagages,
    non_fumeur,
    animaux,
    note
FROM preferences_conducteur")->fetchAll(PDO::FETCH_ASSOC);

    // Convertir les tinyint en boolean

    foreach ($trajets as &$trajet) {
        $trajet['is_eco'] = (bool)$trajet['is_eco'];
    }
    foreach ($preferences as &$pref) {
        $pref['non_fumeur'] = (bool)$pref['non_fumeur'];
        $pref['animaux'] = (bool)$pref['animaux'];
    }

    // Retourner les 4 tableaux séparés
    echo json_encode([
        'success' => true,
        'trajets' => $trajets,
        'voitures' => $voitures,
        'utilisateurs' => $utilisateurs,
        'preferences' => $preferences
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur de connexion à la base de données',
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur',
        'message' => $e->getMessage()
    ]);
}
