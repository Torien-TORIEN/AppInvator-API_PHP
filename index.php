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



//Exemple 
$htmlContente='<!DOCTYPE html>
<html>
<head>
<style>
    .button {
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    }

    .button1 {background-color: #04AA6D;} 
    .button2 {background-color: #008CBA;} 
    h1 {color:red;}
    button {background-color:green;}
    
</style>
</head>
<body>

<div>
    <div>
        <a href="https://www.w3schools.com">Lien W3school</a>
        <h1 name="h1">The button element - Styled with CSS</h1>
        <h3 name="h3">Aller à W3Schools , cliquer <a href="https://www.w3schools.com">Ici</a><h3>
        <p name="p">Change the background color of a button with the background-color property:</p>
    </div>
</div>

<div>
 <img src="https://i.ytimg.com/vi/JJt9tVcrXRw/maxresdefault.jpg" alt="LOGO">
  <button class="button button1">Green</button>
  <input type="button"class="button button2" value="Blue">
</div>

<div>
	<form action="">
      <label for="fname">First name:</label>
      <input type="text" id="fname" name="fname" value="TOTO" readonly><br><br>
      <label for="lname">Last name:</label>
      <input type="text" id="lname" name="lname" placeholder="your name"><br><br>
      <label for="pwd">Password:</label>
      <input type="password" id="pwd" name="pwd"><br><br>
      <label for="w3review">Review of W3Schools:</label>
        <textarea id="w3review" name="w3review" rows="4" cols="50">
        At w3schools.com you will learn how to make a website. They offer free tutorials in all web development technologies.
        </textarea>
      <input type="submit" value="Submit">
      <button class="button button1"><a href="https://www.w3schools.com">Visit W3Schools.com!</a></button>
    </form>
</div>



</body>
</html>
';

$htmlContente='<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .button {
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        .button1 {
            background-color: #04AA6D;
        }

        .button2 {
            background-color: #008CBA;
        }

        h1 {
            color: #333;
        }

        h3 {
            color: #666;
        }

        p {
            color: #888;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #04AA6D;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <div style="background-color:#888">
        <a href="https://www.w3schools.com">Lien W3school</a>
        <h1>The button element - Styled with CSS</h1>
        <h3>Aller à W3Schools, cliquer <a href="https://www.w3schools.com">Ici</a></h3>
        <p>Change the background color of a button with the background-color property:</p>
    </div>

    <div>
        <img src="https://i.ytimg.com/vi/JJt9tVcrXRw/maxresdefault.jpg" alt="LOGO">
        <button class="button button1"><a href="https://95e6f78e-fbee-44e9-a287-0dc7d82f0911-00-wrpt78pl4e69.spock.replit.dev/contact.html">Contact page</a></button>
        <input type="button" class="button button2" value="Blue">
    </div>

    <div>
        <form action="">
            <label for="fname">First name:</label>
            <input type="text" id="fname" name="fname" value="TOTO" readonly><br><br>
            <label for="lname">Last name:</label>
            <input type="text" id="lname" name="lname" placeholder="Your name"><br><br>
            <label for="pwd">Password:</label>
            <input type="password" id="pwd" name="pwd"><br><br>
            <label for="w3review">Review of W3Schools:</label>
            <textarea id="w3review" name="w3review" rows="4" cols="50">At w3schools.com you will learn how to make a website. They offer free tutorials in all web development technologies.</textarea>
            <input type="submit" value="Submit">
            <button class="button button1"><a href="https://www.w3schools.com" style="text-decoration: none; color: white;">Visit W3Schools.com!</a></button>
        </form>
    </div>
</div>

</body>';


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

// Envoie les données sous forme de JSON
//header('Content-Type: application/json');
$json=[
    '$Menu'=>$menus,
    '$Components'=>$data,
];

echo json_encode($json,JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE : pour ne pas encoder les caractères spéciaux en unicode exemple "à" en "\u00e0"


?>