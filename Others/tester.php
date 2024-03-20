<?php
require_once '../Functions/styleFunctions.php';
require_once '../Functions/formatingFunctions.php';

/* Spinner : Select balises
* Recuperer les options de la balise select  [select ]
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractSelectElements($element,$html)
{
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if ($element->tagName==="select") {
        // Extraction des options
        $options = $element->getElementsByTagName('option');
        $elements = [];
        foreach ($options as $option) {
            $elements[] = formatText($option->textContent);
        }

        // Sélection par défaut
        $defaultSelection = '';
        foreach ($options as $option) {
            if ($option->hasAttribute('selected')) {
                $defaultSelection = formatText($option->textContent);
                break;
            }
        }

        $elementData = [
            '$Type' => "Spinner",
            //'$Name' => $element->getAttribute('name'),
            '$ElementsFromString' => implode(',', $elements),
            '$Selection' => $defaultSelection,
            //'$HeightPercent' => 10,
            //'$WidthPercent' => 100,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$Visible' => true,
        ];
        
    }

    //Mise à jour des Styles
    $elementData=setStyle($elementData["style"],$elementData);

    return $elementData;
}


$htmlContent='<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact</title>
</head>

<body>
  <button class="button button1"> <a href="/">Homme Page</a> </button>

  <div class="container">
    <h1>Contactez-nous</h1>
    <form action="http://devbox.u-angers.fr/~torientorien5901/data.php" method="post">
      <label for="name">Nom:</label>
      <input type="text" id="name" name="name" required>
      <input type="text" id="fname" name="fname" required>
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      <label for="message">Message:</label>
      <textarea id="message" name="message" required></textarea>
      <hr>
      <label for="cars">Choose a car:</label>
      <select name="cars" id="cars">
        <option value="volvo">Volvo</option>
        <option value="saab">Saab</option>
        <option value="mercedes">Mercedes</option>
        <option value="audi">Audi</option>
      </select> 
      <hr>
      <button type="submit">Envoyer</button>
    </form>
  </div>
</body>

</html>';

// Utilise une classe DOMDocument pour analyser le HTML
$doc = new DOMDocument();
@$doc->loadHTML($htmlContent);

// Parcourir tous les éléments du document HTML
$allElements = $doc->getElementsByTagName('*');
foreach ($allElements as $element) {
    echo "TagName :$element->tagName\n";
    $data[] = extractSelectElements($element, $htmlContent);
}



// Envoie les données sous forme de JSON
//header('Content-Type: application/json');
$json=[
    '$data'=>$data
];

echo json_encode($json,JSON_UNESCAPED_UNICODE);