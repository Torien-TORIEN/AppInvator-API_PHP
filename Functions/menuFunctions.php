<?php

// Fonction pour vérifier si l'élément contient la classe $classValue
function containsNonMenuClass($element, $classValue) {
    $classes = explode(' ', $element->getAttribute('class'));
    return in_array($classValue, $classes);
}



/* Menu SideBar
* Recuperer les  textes de  la balise a
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractMenuElement($element,$html)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $tagNames = ['a'];
    $parentTagNamesNotAccepted = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li','button','textarea'];
    $childMidleTagNames=['a','b','span','em','i','strong'];
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames)) {
        //Verifier sa classe n'est pas :<<non-menu>>
        if(!containsNonMenuClass($element,"non-menu")){
            $elementData = [
                formatText($element->textContent) => formatURL($element->getAttribute('href')),
            ];
        }
    }

    return $elementData;
}
