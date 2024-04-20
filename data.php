<?php

require_once 'database/config.db.php';

// les données sous forme JSON : { "key": "value", "key": "value" }

//Cette page va recevoir  une requette POST => donc traiter ces données afficher sur le console

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   

    // Obtenir l'URL de la page actuelle
    $url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $url .= "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    // Récupérer les données JSON envoyées
    $data = file_get_contents('php://input');



    //La reponse à envoyer
    $Response="";

    // Vérifier si des données ont été reçues
    if (!empty($data)) {

        // Convertir les données JSON en tableau associatif
        $jsonData = json_decode($data, true);

        // Vérifier si le décodage JSON a réussi
        if ($jsonData !== null) {

            // Enregistrer les données dans la base de données
            $database = new Database();
            $db = $database->connect_to_db();

            // Vérifier si la connexion à la base de données est établie
            if ($db !== null) {
                try {
                    // Préparer la requête SQL pour l'insertion des données
                    $stmt = $db->prepare("INSERT INTO app_invator (contents) VALUES (:contents)");

                    // Liaison des valeurs et exécution de la requête
                    $stmt->bindParam(':contents', json_encode($jsonData));
                    $stmt->execute();

                    $Response= "Données enregistrées avec succès dans la base de données.\n";
                } catch (PDOException $e) {
                    $Response= "Erreur lors de l'enregistrement des données dans la base de données : " . $e->getMessage() . "\n";
                }
                
            } else {
                $Response= "Erreur : Impossible de se connecter à la base de données.\n";
            }


        } else {
            $Response= "Erreur : Impossible de décoder les données JSON.\n";
        }
    } else {
        $Response= "Erreur : Aucune donnée reçue.\n";
        
    }
} else {
    $Response= "Erreur : Cette page ne supporte que les requêtes POST.\n";
}

$Result=[
    '$Type' => "Label",
    '$Text' => $Response
];
echo json_encode($Result,JSON_UNESCAPED_UNICODE);

