<?php
require_once '../Functions/lyoutFunctions.php';

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
$html = getWebContent($url);

$tags = ['h1','h2','h3','h4', 'h5','h6','button','textarea','img','p', 'label', 'input','a'];

$result = findInlineTags($html, $tags);

header('Content-Type: application/json');
echo json_encode($result);