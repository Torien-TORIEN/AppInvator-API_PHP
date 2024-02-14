<?php

require_once 'functions.php';

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



//Exemple 
$htmlContent='<!DOCTYPE html>
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
</style>
</head>
<body>

<div>
    <div>
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

// Utilise une classe DOMDocument pour analyser le HTML
$doc = new DOMDocument();
@$doc->loadHTML($htmlContent);

// Initialise un tableau pour stocker les données extraites
$data = [];

// Fonction récursive pour extraire les données et les styles pour les balises spécifiques
function extractElementData($element, $doc)
{

    $elementData=[];
	//Tester Style CSS
	$styleCSS=[];
	if(($Style=getStyle($element))!=null ){
        $styleCSS=$Style;
    }
	
	
    if(($Lyout=extractLyout($element))!=null ){
        $elementData=$Lyout;
    }
	
	/*
    if(($button=extractButton($element))!=null){
        $elementData=$button;
    //$elementData=($element);
    }elseif(($Label=extractElementText($element))!=null){
        $elementData=$Label;
    }elseif(($TextBox=extractTextBoxElements($element))!=null){
        $elementData=$TextBox;
    }elseif(($PasswordTextBox=extractPasswordTextBoxElements($element))!=null){
        $elementData=$PasswordTextBox;
    }elseif(($Image=extractImage($element))!=null){
        $elementData=$Image;
    }*/
    

    if($elementData!=null)
        return $elementData;
		//return $styleCSS; //Test
}
// Initialise un tableau pour stocker les données extraites
$data = [];

// Parcourir tous les éléments du document HTML
$allElements = $doc->getElementsByTagName('*');
foreach ($allElements as $element) {
    $data[] = extractElementData($element, $doc);
}

// Supprimer les éléments null et ne conserver que les valeurs
$data = removeNullElements($data);

// Envoie les données sous forme de JSON
header('Content-Type: application/json');
$json=[
    '$Components'=>$data,
];

echo json_encode($json);


?>