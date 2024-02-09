<?php
// Fonction pour extraire un bouton avec les propriétés spécifiées
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
function extractButton($element)
{
    if(isButton($element)){
        $buttonData = [
            'type' => 'button',
            'text' => $element->nodeValue,
            'style' => $element->getAttribute('style'),
            'action' => '',
        ];

        // Vérifie s'il y a un lien à l'intérieur du bouton
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            $buttonData['action'] = 'redirect';
            $buttonData['link'] = $link->getAttribute('href');
        } else {
            // Si aucun lien, l'action est définie sur 'submit'
            $buttonData['action'] = 'submit';
        }

        return $buttonData;
    }else{
        return null;
    }
}

function extractElementText($element)
{
    $tagNames = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'h7', 'label', 'p', 'input', 'textarea'];
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames)) {
        $elementData = [
            'type' => $element->tagName,
            'text' => $element->nodeValue,
            'style' => $element->getAttribute('style'),
        ];

        // Vérifie s'il y a un lien à l'intérieur de l'élément
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            $elementData['link'] = $link->getAttribute('href');
        }

        // Si l'élément est un champ de texte en lecture seule
        if (($element->tagName === 'input' && $element->getAttribute('readonly') === 'readonly') || ($element->tagName === 'textarea' && $element->getAttribute('readonly') === 'readonly')) {
            $elementData['readonly'] = true;
        }

        // Si l'élément est une balise de titre, détermine le niveau
        if (preg_match('/h([1-7])/', $element->tagName, $matches)) {
            $elementData['level'] = intval($matches[1]);
        }
    }

    return $elementData;
}