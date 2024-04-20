<?php

//Imports
require_once 'Functions/extractComponentsFunctions.php';
require_once 'Functions/checkFunctions.php';
require_once 'Functions/colorCodeFunctions.php';
require_once 'Functions/lyoutFunctions.php';
require_once 'Functions/styleFunctions.php';
require_once 'Functions/menuFunctions.php';
require_once 'Functions/formatingFunctions.php';

// Désactiver les avertissements
error_reporting(E_ERROR | E_PARSE);

// Récupère l'URL de la page à analyser depuis les paramètres de la requête GET
$url = isset($_GET['url']) ? $_GET['url'] : '';
if (empty($url)) {
    // Si l'URL n'est pas fournie, renvoie une erreur
    header('Content-Type: application/json');
    $response=[
        '$Menu'=>[],
        '$Components' => [[
            '$Type'=>'VerticalArrangement',
            '$AlignHorizontal'=>1,
            '$AlignVertical'=>1,
            '$BackgroundColor'=>'[255,255,255]',
            '$Visible'=>true,
            '$Components'=>[[
                '$Type'=>'Label',
                '$Text'=>formatText('L\'url not provided !'),
                '$FontSize'=>14,
                '$FontBold'=>true,
                '$TextColor'=>'[255,0,0]',
                '$Visible'=>true,

            ]],
        ]],
    ];
    echo json_encode($response);
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

//GET URL de l'API (SERVEUR)
function getAPI_URL_COMPLETE(){
    $apiURL = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $apiURL .= "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    return $apiURL;
}

// Fonction pour obtenir l'URL de base
function getAPI_URL_BASE() {
    $apiURL = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $apiURL .= "://$_SERVER[HTTP_HOST]";
    $apiURL .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
    $apiURL=str_replace('\\', '/', $apiURL);
    return $apiURL;
}

/**
 * Récupère l'URL de base à partir d'une URL complète.
 *
 * @param string $url L'URL complète.
 * @return string L'URL de base extraite.
 */
function getRequestBaseURL() {
    $url = isset($_GET['url']) ? $_GET['url'] : '';
    // Trouver la position de la première barre oblique après le schéma
    $schemePos = strpos($url, '://');
    if ($schemePos === false) {
        return ''; // Retourner une chaîne vide si le schéma n'est pas trouvé
    }
    $slashPos = strpos($url, '/', $schemePos + 3);

    // Trouver la position de la première ancre s'il y en a
    $hashPos = strpos($url, '#');

    // Si une ancre est présente, prendre la partie avant l'ancre, sinon prendre toute la chaîne
    if ($hashPos !== false) {
        $url = substr($url, 0, $hashPos);
    }

    // Si une barre oblique est présente après le schéma, prendre la partie avant la barre oblique, sinon prendre toute la chaîne
    if ($slashPos !== false) {
        $url = substr($url, 0, $slashPos);
    }

    return $url;
}

// Récupère le contenu HTML de la page
$htmlContent = getWebContent($url);

// Affiche le contenu HTML 
//echo $htmlContent;//Seulement pour le debogage




// Utilise une classe DOMDocument pour analyser le HTML
$doc = new DOMDocument();
@$doc->loadHTML($htmlContent);

// Initialise un tableau pour stocker les données extraites
$data = [];

// Fonction récursive pour extraire les données et les styles pour les balises spécifiques
function extractElementData($element, $html)
{
    //echo "TagName  $element->tagName, styles =";
    $elementData=[];
	//Tester Style CSS
	$styleCSS=[];
    
	/*if(($Style=getStyle($element,$html))!=null ){
        $styleCSS=$Style;
    }*/
	
	
    if(($Lyout=extractLyout($element,$html))!=null ){
        $elementData=$Lyout;
    }
	
	
    
    

    if($elementData!=null)
        return $elementData;
	//return $styleCSS; //Test
}

//Fonction qui extraits les éléments de Menu
function extractSidebar($element, $html){
    $sideBar=[];
    if(($Menu=extractMenuElement($element, $html))!=null ){
        $sideBar=$Menu;
    }
    if($sideBar!=null)
        return $sideBar;
    
}



// Initialise un tableau pour stocker les données extraites
$data = [];
$menus=[];

// Parcourir tous les éléments du document HTML
$allElements = $doc->getElementsByTagName('*');
foreach ($allElements as $element) {
    $data[] = extractElementData($element, $htmlContent);
    $menus[] = extractSidebar($element, $htmlContent);
}


// Supprimer les éléments null et ne conserver que les valeurs
$data = removeNullElements($data);
$menus = removeNullElements($menus);
$menus = removeNullAttributs($menus);



/*echo "Menus :\n";
//print_r($menus);

$je=json_encode($menus);
if($je){
    print_r($je);
    print_r($menus);
}else{
    echo "NO :\n";
    print_r($menus);
}*/



if (json_encode($menus, JSON_UNESCAPED_UNICODE)==false) {
    $menus =[["Erreur encodage" => " peut etre du aux caractères spéciaux"]];
}
if (json_encode($data, JSON_UNESCAPED_UNICODE)==false) {
    $data =[];
}

// Envoie les données sous forme de JSON
//header('Content-Type: application/json');
$json = [
    '$Menu' => $menus,
    '$Components' => $data,
];




echo json_encode($json,JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE : pour ne pas encoder les caractères spéciaux en unicode exemple "à" en "\u00e0"


?>