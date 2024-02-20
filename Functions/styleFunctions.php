<?php

require_once 'colorCodeFunctions.php';

// Désactiver les avertissements
error_reporting(E_ERROR | E_PARSE);

/*Styles css 
* Fonction qui recupère les styles associées à un tag donné
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function getStyle($element, $html) {
    // Désactiver les avertissements
    error_reporting(E_ERROR | E_PARSE);

    $styles = [
        'tagName' => $element->tagName,
        'class' => $element->getAttribute('class'),
        'name' => $element->getAttribute('name'),
        'id' => $element->getAttribute('id'),
        'style' => [], // initialisation du tableau de styles
    ];

    // 1. Récupérer le style CSS inline de l'élément
    $inlineStyle = $element->getAttribute('style');
    if ($inlineStyle !== '') {
        // Convertir le style inline en tableau associatif
        $inlineStyles = [];
        $inlineDeclarations = explode(';', $inlineStyle);
        foreach ($inlineDeclarations as $declaration) {
            $declarationParts = explode(':', $declaration);
            if (count($declarationParts) === 2) {
                $property = trim($declarationParts[0]);
                $value = trim($declarationParts[1]);
                $inlineStyles[$property] = $value;
            }
        }
        // Ajouter les styles inline au tableau de styles
        $styles['style'] = $inlineStyles;
    }

    // 2. Récupérer les styles CSS définis dans les balises <style> : tagName,....{styles}
    //$styleTags = $element->ownerDocument->getElementsByTagName('style');

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $styleTags = $dom->getElementsByTagName('style');

    foreach ($styleTags as $styleTag) {
        $css = $styleTag->textContent;

        // Recherche de styles par tagName
        //$pattern = '/(?<=^|\})(?:' . $element->tagName . '){([^}]*)}/';
        //$pattern = '/' . $element->tagName . '+(?:,\s*\w+)*\s*{([^}]+)}/';
        $pattern = '/' . $element->tagName . '+(?:,\s*[a-zA-Z]+)*\s*{([^}]+)}/';
        preg_match($pattern, $css, $matches);
        if (!empty($matches)) {
            $declarations = explode(';', $matches[1]);
            foreach ($declarations as $declaration) {
                list($property, $value) = explode(':', $declaration);
                if(strlen(trim($property))!=0)
                    $styles['style'][trim($property)] = trim($value);
            }
        }
    }

    // 3. Récupérer les styles CSS par class : .class,....{styles}
    $class = $element->getAttribute('class');
    if (!empty($class)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            //Tableau des class
            $class_table = explode(" ", $class); //couper les class par un espace 
            foreach($class_table as $classe){
                //$pattern = '/(?<=^|\})(?:\.' . $class . '){([^}]*)}/';
                $pattern = '/.' . $classe . '+(?:,\s*.\w+)*\s*{([^}]+)}/';
                preg_match($pattern, $css, $matches);
                if (!empty($matches)) {
                    $declarations = explode(';', $matches[1]);
                    foreach ($declarations as $declaration) {
                        list($property, $value) = explode(':', $declaration);
                        if(strlen(trim($property))!=0)
                            $styles['style'][trim($property)] = trim($value);
                    }
                }
            }
        }
    }

    // 4. Récupérer les styles CSS par id
    $id = $element->getAttribute('id');
    if (!empty($id)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            //$pattern = "/(?<=^|\})(?:#' . $id . '){([^}]*)}/';
            //$pattern = '/#'.$id.'+\s*{[^}]+}/';
            $pattern = '/#' . $id . '+(?:,\s*#\w+)*\s*{([^}]+)}/';
            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                if (isset($matches[1])) {
                    $declarations = explode(';', $matches[1]);
                    foreach ($declarations as $declaration) {
                        list($property, $value) = explode(':', $declaration); #pour diviser une chaîne en plusieurs sous-chaînes delimitée par ; 
                        if(strlen(trim($property))!=0)
                            $styles['style'][trim($property)] = trim($value);
                    }
                }
            }             
            
        }
    }

    // 5. Récupérer les styles CSS par tagName et id :tagName#id,....{styles}
    $id = $element->getAttribute('id');
    if (!empty($id)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;
            $pattern = "/$element->tagName#" . $id . '+(?:,\s*#\w+)*\s*{([^}]+)}/';
            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                if (isset($matches[1])) {
                    $declarations = explode(';', $matches[1]);
                    foreach ($declarations as $declaration) {
                        list($property, $value) = explode(':', $declaration); #pour diviser une chaîne en plusieurs sous-chaînes delimitée par ; 
                        if(strlen(trim($property))!=0)
                            $styles['style'][trim($property)] = trim($value);
                    }
                }
            }             
            
        }
    }

    // 6. Récupérer les styles CSS par tag class : tagName.class,....{styles}
    $class = $element->getAttribute('class');
    if (!empty($class)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            //$pattern = '/(?<=^|\})(?:\.' . $class . '){([^}]*)}/';
            $pattern = "/$element->tagName." . $class . '+(?:,\s*.\w+)*\s*{([^}]+)}/';
            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                $declarations = explode(';', $matches[1]);
                foreach ($declarations as $declaration) {
                    list($property, $value) = explode(':', $declaration);
                    if(strlen(trim($property))!=0)
                        $styles['style'][trim($property)] = trim($value);
                }
            }
        }
    }

    // 7. Récupérer les styles CSS par tag et type : tagName[type="typeTag"],....{styles}
    $type = $element->getAttribute('type');
    if (!empty($type)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            //$pattern = '/(?<=^|\})(?:\.' . $class . '){([^}]*)}/';
            //$pattern = "/$element->tagName\[type=\"text\"\]" . '+(?:,\s*.\w+)*\s*{([^}]+)}/';
            $pattern = "/$element->tagName\[type\s*=\s*(\"|')$type(\"|')\]" . '+(?:,\s*.(\w+|(\w+\[\w+\s*=\s*(\"|\')\w+(\"|\')\])))*\s*{([^}]+)}/'; //|(\w+\[\w+\s*=\s*\w+\])

            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                $declarations = explode(';', $matches[sizeof($matches)-1]);
                foreach ($declarations as $declaration) {
                    list($property, $value) = explode(':', $declaration);
                    if(strlen(trim($property))!=0)
                        $styles['style'][trim($property)] = trim($value);
                }
            }
        }
    }
    // Réactiver les avertissements
    //error_reporting(E_ALL);

    return $styles;
}



/*
*fonction qui met à jour les styles des Components en fonction du style html reçu
*
*Params: $Styles       => Les styles d'un composant
*        $Component    => Composant à appliquer les styles
*/
function setStyle($styles,$Component){
    // Désactiver les avertissements Component
    error_reporting(E_ERROR | E_PARSE);

    $codeColors=listColors();

    if(isset($styles['background-color'])){
        $Component['$BackgroundColor']=colorToRGB($styles['background-color'],$codeColors);
    }
    if(isset($styles['color'])){
        $Component['$TextColor']=colorToRGB($styles['color'],$codeColors);
    }
    if(isset($styles['font-weight']) && $styles['font-weight']==="bold"){
        $Component['$FontBold']=true;
    }
    if(isset($styles['font-style']) && $styles['font-style']==="italic"){
        //$Component['$FontBold']=true;
    }
    if(isset($styles['text-align'])){
        //$Component['$FontBold']=true;
    }
    if(isset($styles['width'])){
        if(preg_match('/\d+%$/', $styles['width'],$matches)) $Component['$WidthPercent']=intval(trim($matches[sizeof($matches)-1],'%'));
        if(preg_match('/\d+px$/', $styles['width'],$matches)) $Component['$Width']=intval(trim($matches[sizeof($matches)-1],'px'));
    }   
    if(isset($styles['height'])){
        if(preg_match('/\d+%$/', $styles['height'],$matches)) $Component['$HeightPercent']=intval(trim($matches[sizeof($matches)-1],'%'));
        if(preg_match('/\d+px$/', $styles['height'],$matches)) $Component['$Height']=intval(trim($matches[sizeof($matches)-1],'px'));
    }

    //supprimer la clé style 
    unset($Component['style']);

    // Réactiver les avertissements
    //error_reporting(E_ALL);

    return $Component;
}