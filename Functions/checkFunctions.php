<?php

// Désactiver les avertissements
error_reporting(E_ERROR | E_PARSE);

/*
* Fonction pour tester si une balise est un bouton ou non
*/
function isButton($element)
{
    // Vérifie si l'élément est une balise <button> avec l'attribut type="button"
    if ($element->tagName === 'button' ) {
        return true;
    }

    // Vérifie si l'élément est une balise <input> avec l'attribut type="button" ou type="submit"
    if ($element->tagName === 'input' && in_array($element->getAttribute('type'), ['button', 'submit'])) {
        return true;
    }

    // Sinon, l'élément n'est pas un bouton
    return false;
}


/*
* Fonction pour tester si le parent  d'une balise($element) n'est pas dans la liste fournie ($list)
*/
function isFirstParentNotInList($element, $list) { 
    // Récupérer le premier parent de l'élément 
    $parent = $element->parentNode;

    //Verifier que le parent n'est pas dans la liste
    return !in_array($parent->tagName, $list); 
} 