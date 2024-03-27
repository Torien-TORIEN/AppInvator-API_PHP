<?php

function afficherBalisesEnfantsDirects($html) {
    // Créer un objet DOMDocument pour analyser la chaîne HTML
    $dom = new DOMDocument();
    // Supprimer les avertissements relatifs à la structure du HTML
    libxml_use_internal_errors(true);
    // Charger la chaîne HTML dans l'objet DOMDocument
    $dom->loadHTML($html);

    // Récupérer le corps du document
    $body = $dom->getElementsByTagName('body')->item(0);
    
    // Vérifier si le corps du document existe
    if ($body) {
        // Parcourir les enfants du corps du document
        foreach ($body->childNodes as $child) {
            // Vérifier si l'enfant est un élément (balise HTML)
            if ($child instanceof DOMElement) {
                // Afficher le nom de la balise de l'enfant
                echo $child->tagName . PHP_EOL;
            }
        }
    }
}





// Exemple d'utilisation avec une chaîne HTML
$html = '<html>
    <head><title>Test</title></head>
    <body>
        <div>
            <p>Contenu du paragraphe</p>
        </div>
        <dl>
            <dt>Coffee</dt>
            <dd>Black hot drink</dd>
            <dt>Milk</dt>
            <dd>White cold drink</dd>
        </dl>
    </body>
</html>';
afficherBalisesEnfantsDirects($html);
?>
