<?php

// Récupère l'URL de la page à analyser depuis les paramètres de la requête GET
$url = isset($_GET['url']) ? $_GET['url'] : '';
if (empty($url)) {
    // Si l'URL n'est pas fournie, renvoie une erreur
    header('Content-Type: application/json');
    echo json_encode(['error' => 'URL not provided']);
    exit;
}

// Fonction pour récupérer le contenu HTML d'une page
function getWebContent($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}

// Récupère le contenu HTML de la page
$htmlContent = getWebContent($url);

// Utilise une classe DOMDocument pour analyser le HTML
$doc = new DOMDocument();
@$doc->loadHTML($htmlContent);

// Initialise un tableau pour stocker les données extraites
$data = [];

// Fonction récursive pour extraire les données et les styles pour les balises spécifiques
function extractElementData($element, $doc)
{
    $elementData = [
        'type' => $element->tagName,
        'text' => $element->textContent,
    ];

    // Récupère les styles CSS associés à l'élément
    $styles = '';
    $styleAttribute = $element->getAttribute('style');
    if (!empty($styleAttribute)) {
        $styles .= $styleAttribute;
    }

    $elementData['styles'] = $styles;

    // Si l'élément a des enfants, récursion pour extraire les données des enfants
    if ($element->hasChildNodes()) {
        $elementData['elements'] = [];
        foreach ($element->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $elementData['elements'][] = extractElementData($child, $doc);
            }
        }
    }

    return $elementData;
}

// Définir les types d'éléments à extraire
$elementTypes = ['div', 'span', 'a', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'table', 'tr', 'td'];

// Extraire les données pour chaque type d'élément spécifié
foreach ($elementTypes as $elementType) {
    $elements = $doc->getElementsByTagName($elementType);
    foreach ($elements as $element) {
        $data[] = extractElementData($element, $doc);
    }
}

// Envoie les données sous forme de JSON
header('Content-Type: application/json');
echo json_encode($data);

?>
