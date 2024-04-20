<?php



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
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames)) {
        //Verifier sa classe n'est pas :<<non-menu>> # <<only-menu>>
        if(!containsNonMenuClass($element,"non-menu")){
            $elementData = [
                formatTextMenu($element->textContent) => formatURL($element->getAttribute('href')),
                //$element->textContent=> formatURL($element->getAttribute('href')),
            ];
        }
    }

    return $elementData;
}
