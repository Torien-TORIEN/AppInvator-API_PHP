<?php

// les données sous forme JSON : { "key": "value", "key": "value" }

//Cette page va recevoir  une requette POST => donc traiter ces données afficher sur le console

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtenir l'URL de la page actuelle
    $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $url .= "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    echo "url : $url \n";

    // Récupérer les données JSON envoyées
    $data = file_get_contents('php://input');

    // Vérifier si des données ont été reçues
    if (!empty($data)) {
        // Convertir les données JSON en tableau associatif
        $jsonData = json_decode($data, true);

        // Vérifier si le décodage JSON a réussi
        if ($jsonData !== null) {
            // Afficher les données sur la console
            echo "Données reçues :\n";
            print_r($jsonData);
        } else {
            echo "Erreur : Impossible de décoder les données JSON.\n";
        }
    } else {
        echo "Erreur : Aucune donnée reçue.\n";
    }
} else {
    echo "Erreur : Cette page ne supporte que les requêtes POST.\n";
}


