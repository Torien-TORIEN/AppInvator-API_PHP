<?php
function findInlineTags($html, $tags) {
    // Créer un nouveau document DOM
    $dom = new DOMDocument();
    // Charger le HTML dans le document DOM (la suppression du '@' devant permet de masquer les avertissements)
    @$dom->loadHTML($html);

    // Tableau pour stocker les balises alignées
    $inlineTags = [];

    // Récupérer toutes les balises du document HTML
    $elements = $dom->getElementsByTagName('*');

    // Variables pour suivre le groupe actuel d'alignement et l'index actuel à l'intérieur de ce groupe
    $currentInlineGroup = 0;
    $currentInlineIndex = 0;

    // Parcourir toutes les balises du document HTML
    foreach ($elements as $element) {
        // Récupérer le nom de la balise
        $tagName = $element->tagName;

        // Vérifier si la balise fait partie des balises spécifiées
        if (in_array($tagName, $tags)) {
            // Vérifier si le style est défini pour l'élément
            $style = $element->getAttribute('style');
            $styleArray = explode(';', $style);

            $isInline = false;

            // Vérifier si le style "display" est "inline"
            foreach ($styleArray as $styleProp) {
                list($property, $value) = explode(':', $styleProp);
                if (trim($property) === 'display' && trim($value) === 'inline') {
                    $isInline = true;
                    break;
                }
            }

            // Vérifier si c'est une balise <br>
            $hasBr = false;
            $brNode = $element->getElementsByTagName('br');
            if ($brNode->length > 0) {
                $hasBr = true;
            }

            // Si la balise est alignée ou si c'est une balise <br>, l'ajouter au tableau des balises alignées
            if ($isInline || $tagName === 'br') {
                // Créer un nouveau groupe si nécessaire
                if (!isset($inlineTags[$currentInlineGroup])) {
                    $inlineTags[$currentInlineGroup] = [];
                }

                // Ajouter la balise au groupe actuel
                $inlineTags[$currentInlineGroup][$currentInlineIndex] = ['tagName' => $tagName, 'id' => $element->getAttribute('id'), 'name' => $element->getAttribute('name')];

                // Passer au prochain groupe ou à l'index à l'intérieur du groupe
                if (!$isInline) {
                    $currentInlineGroup++;
                    $currentInlineIndex = 0;
                } else {
                    $currentInlineIndex++;
                }
            }
        }
    }

    // Retourner le tableau des balises alignées
    return $inlineTags;
}

// Test de la fonction avec l'exemple donné
$html = '<!DOCTYPE html>
<html>
<head>
</head>
<body>
<div>
  <h1>This is a heading</h1>
  <p>This is a paragraph.</p>
</div>

<div>
  <label id="l1">genre  : </label>
  <label  id="l2">Mean</label>
  <label  id="l3" style="display:block">Next</label>
  <label  id="l4">Year :</label>
  <label  id="l5">2022</label>
</div>
<form action="/action_page.php">
  <label  id="l6" for="fname">First name:</label>
  <input  id="i1" type="text" id="fname" name="fname"><br><br>
  <label  id="l7"for="lname">Last name:</label>
  <input  id="i2"type="text" id="lname" name="lname"><br><br>
  <input  id="i3"type="submit" value="Submit">
</form>
</body>
</html>';

$tags = ['h1', 'p', 'label', 'input',];

$result = findInlineTags($html, $tags);

// Afficher le résultat
print_r($result);

/*
[
  [
  	{ "tagName":"h1","id" : "","name" : ""}
  ],
  [
  	{ "tagName":"p","id" : "","name" : ""}
  ],
  [
  	{ "tagName":"label","id" : "l1","name" : ""},
    { "tagName":"label","id" : "l2","name" : ""}
  ],
  [
  	{ "tagName":"label","id" : "l3","name" : ""}
  ],
  [
  	{ "tagName":"label","id" : "l4","name" : ""},
    { "tagName":"label","id" : "l5","name" : ""}
  ],
  [
  	{ "tagName":"label","id" : "l6","name" : ""},
    { "tagName":"input","id" : "i1","name" : ""}
  ],
  [
  	{ "tagName":"label","id" : "l7","name" : ""},
    { "tagName":"input","id" : "i2","name" : ""}
  ]
  ,
  [
  	{ "tagName":"input","id" : "i3","name" : ""}
  ]
]
*/


/*
Utilisation de array_push() :
php
Copy code
$tab = []; // Initialisation du tableau

// Ajout de nouveaux éléments
array_push($tab, "nouvel_element1");
array_push($tab, "nouvel_element2");
array_push($tab, "nouvel_element3");
Utilisation de la notation crochet [] :
php
Copy code
$tab = []; // Initialisation du tableau

// Ajout de nouveaux éléments
$tab[] = "nouvel_element1";
$tab[] = "nouvel_element2";
$tab[] = "nouvel_element3";
*/