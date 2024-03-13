<?php
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
* Fonction pour tester si une balise n'a pas un parent dans la liste fourni
*/
function isFirstParentNotInList($element, $list) { 
    // Récupérer le premier parent de l'élément 
    $parent = $element->parentNode;

    //Verifier que le parent n'est pas dans la liste
    return !in_array($parent->tagName, $list); 
} 
    

/* Button
* Recuperer les  buttons 
*/
function extractButton($element)
{
    $defaultFontSize=14.0;
    if(isButton($element)){
        $buttonData = [
            '$Type' => "Button",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => $element->textContent != "" ? $element->textContent : $element->getAttribute("value"),//$element->nodeValue, // Utilise textContent pour extraire le texte
            //'$HeightPercent' => 15,
            //'$WidthPercent' => 50,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$Visible' => true,
            '$Action'=>'',
            'style' => $element->getAttribute('style'),
        ];

        // Vérifie s'il y a un lien à l'intérieur du bouton
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            $buttonData['$Action'] = 'redirect';
            $buttonData['$Link'] = $link->getAttribute('href');
        } else {
            // Si aucun lien, l'action est définie sur 'submit'
            $buttonData['$Action'] = 'submit';
        }

        return $buttonData;
    }else{
        return null;
    }
}

/* Label
* Recuperer les  textes de  [h1-6,label,p,span,a]
*/
function extractElementText($element)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $tagNames = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li'];
    $tagNamesMidle=['a','b','span','em','i','strong'];
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames) || (in_array($element->tagName, $tagNamesMidle) && isFirstParentNotInList($element,$tagNames))) {
        $elementData = [
            '$Type' => "Label",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => $element->textContent, // Utilise textContent pour extraire le texte
            //'$HeightPercent' => 20,
            //'$WidthPercent' => 100,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => [255,0,0],
            //'$BackgroundColor' => "Blue",
            '$Visible' => true,
            //'style' => $element->getAttribute('style'),
        ];

        // Vérifie s'il y a un lien à l'intérieur de l'élément
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            $elementData['$Link'] = $link->getAttribute('href');
        }

        if($element->tagName ==="a") $elementData['$Link'] = $element->getAttribute('href');

        // Si l'élément est une balise de titre, détermine le niveau et ajuste la taille de police
        if (preg_match('/h([1-6])/', $element->tagName, $matches)) {
            $elementData['$FontBold'] = true;
            // Ajuster la taille de police selon le niveau de la balise de titre
            $headingSizeMap = [1 => 24.0, 2 => 22.0, 3 => 20.0, 4 => 18.0, 5 => 16.0, 6 => 14.0];
            $headingLevel = intval($matches[1]);
            if (isset($headingSizeMap[$headingLevel])) {
                $elementData['$FontSize'] = $headingSizeMap[$headingLevel];
            }
        }

    }

    return $elementData;
}


/* TextBox
* Recuperer les champs des textes [input, textarea]
*/
function extractTextBoxElements($element)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $tagNames = ["input","textarea"];
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames) && $element->getAttribute('type') != 'password' && $element->getAttribute('type') != 'submit' && $element->getAttribute('type') != 'button') {
        $isTextArea = $element->tagName === 'textarea';
        $elementData = [
            '$Type' =>"TextBox",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => $element->getAttribute('value')!=""?$element->getAttribute('value'):$element->textContent,
            '$Hint' => $element->getAttribute('placeholder'),
            '$HeightPercent' => 20,
            '$WidthPercent' => 50,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$ReadOnly' => $element->hasAttribute('readonly') ? true : false,
            '$MultiLine' => $isTextArea ? true : false,
            '$NumbersOnly' => false,
            '$Visible' => true,
            //'style' => $element->getAttribute('style'),
        ];
    }
    

    return $elementData;
}


/* TextBox
* Recuperer les champs des textes [input, textarea]
*/
function extractImage($element)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if ($element->tagName==="img" ) {
        $isTextArea = $element->tagName === 'textarea';
        $elementData = [
            '$Type' =>"Image",
            //'$Name' => $element->getAttribute('name'),
            '$AlternateText' => $element->getAttribute('alt'),
            '$Picture' => $element->getAttribute('src'),
            //'$HeightPercent' => 30,
            //'$WidthPercent' =>100,
            '$ScalePictureToFit' =>false, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$Clickable' => false,
            '$Visible' => true,
            //'style' => $element->getAttribute('style'),
        ];
    }
    

    return $elementData;
}


/* PasswordTextBox
* Recuperer les champs de mot de passe  [input (type=password)]
*/
function extractPasswordTextBoxElements($element)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if ($element->tagName=="input" && $element->getAttribute('type') == 'password') {
        $isTextArea = $element->tagName === 'textarea';
        $elementData = [
            '$Type' =>"PasswordTextBox",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => $element->getAttribute('value'),
            '$Hint' => $element->getAttribute('placeholder'),
            '$HeightPercent' => 10,
            '$WidthPercent' => 100,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$ReadOnly' => $element->hasAttribute('readonly') ? true : false,
            '$NumbersOnly' => false,
            '$Visible' => true,
           // 'style' => $element->getAttribute('style'),
        ];
    }
    

    return $elementData;
}



/* 
* Supprimer les null dans un tableau et ajoute l'index comme clé
*/
function removeNullElementsIndex($array) {
    foreach ($array as $key => $value) {
        if (is_null($value)) {
            unset($array[$key]);
        }
    }
    return $array;
}


/* 
* Supprimer les null dans un tableau sans ajouter l'index comme clé
*/
function removeNullElements($array) {
    $result = [];
    foreach ($array as $value) {
        if (!is_null($value)) {
            $result[] = $value;
        }
    }
    return $result;
}

/* Lyout
* Recuperer les lyouts [div, section , article, ...] si parmi les enfants de niveau 1 de ce div il y a ces listes suivantes
*[h1-6,label,p,span,a,button,img,table,]
*Si l'enfant de ce tag est seulement parmis [div,section,form,article,...] on le recupère pas
*/
function extractLyout($element)
{
    $ContainerTagNames=['div', 'section', 'article','nav','form','header','footer','aside'];
    $PositionHorizontal = ['Left' => 1, 'Right' => 2, 'Center' => 3];
    $PositionVertical = ['Top' => 1, 'Center' => 2, 'Bottom' => 3];
    
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $ContainerTagNames)) {
        // Initialise le contenu
        $contents = [];

        // Parcours les enfants de niveau 1
        foreach ($element->childNodes as $child) {
            // Vérifie si l'enfant est une balise autorisée
			if ($child instanceof DOMElement && in_array($child->tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p', 'span', 'a', 'button', 'img', 'input','table','ul','li','ol','dt','dd'])) {
                // Ajoute les données de l'enfant dans le contenu
                $contents[] =extractElement($child);//[$child->tagName]; //extractElement($child);
            }
        }

        // Vérifie si le contenu n'est pas vide
        if (!empty($contents)) {
            // Détermine le type de disposition en fonction du tag de l'élément parent
            $layoutType = in_array($element->tagName, $ContainerTagNames) ? "VerticalArrangement":"HorizontalArrangement" ;

            $elementData = [
                '$Type' => $layoutType,
                '$Name' => $element->tagName,//$element->getAttribute('name'),
                '$AlignHorizontal' => $PositionHorizontal["Center"],
                '$AlignVertical' => $PositionVertical["Top"],
                '$Image' => '', // Lien de l'image de fond (à compléter si nécessaire)
                '$Height' => "",
                '$Width' => "",
                '$BackgroundColor' => "",
                '$Visible' => true,
                '$Components' => $contents
            ];
        }
    }

    return $elementData;
}



/* 
* Extract all
*/
function extractElement($element)
{
    $elementData=null;
    if(($button=extractButton($element))!=null){
        $elementData=$button;
    }elseif(($Label=extractElementText($element))!=null){
        $elementData=$Label;
    }elseif(($TextBox=extractTextBoxElements($element))!=null){
        $elementData=$TextBox;
    }elseif(($PasswordTextBox=extractPasswordTextBoxElements($element))!=null){
        $elementData=$PasswordTextBox;
    }elseif(($Image=extractImage($element))!=null){
        $elementData=$Image;
    }
    
    return $elementData;
}


/*
* Verifier que le parent de a n'est pas dans la liste
*/
function verifyParent($element) {
    $disallowedParents = ['span', 'p', 'h1', 'h2'];

    // Vérifie si l'élément a un parent
    if ($element->parentNode !== null) {
        $parentTagName = $element->parentNode->tagName;
        // Vérifie si le nom de balise du parent est dans la liste des parents non autorisés
        if (in_array($parentTagName, $disallowedParents)) {
            return false; // Le parent n'est pas autorisé
        }
    }
    return true; // Le parent est autorisé ou l'élément n'a pas de parent
}


/*
*get styles 
*/
function getStylee($element) {
    $styles = [
        'tagName'=>$element->tagName,
        'class'=>$element->getAttribute('class'),
        'name'=>$element->getAttribute('name'),
        'id'=>$element->getAttribute('id'),
    ];

    // Récupérer le style CSS inline de l'élément
    $inlineStyle = $element->getAttribute('style');
    if ($inlineStyle !== '') {
        $styles['inline'] = $inlineStyle;
    }

    // Récupérer les styles CSS définis dans les feuilles de style
    $tagName = $element->tagName;
    $id = $element->getAttribute('id');
    $class = $element->getAttribute('class');
    $name = $element->getAttribute('name');

    // Recherche des règles de style dans les balises <style>
    $styleTags = $element->ownerDocument->getElementsByTagName('style');

    foreach ($styleTags as $styleTag) {
        $css = $styleTag->textContent;
        // Utilisez une expression régulière pour extraire les règles de style spécifiques à cet élément
        $pattern = '/(?<=\b' . $tagName . '\b|\b#' . $id . '\b|\b\.' . $class . '\b|\b' . $name . '\b)\s*{([^}]*)}/i';
        preg_match($pattern, $css, $matches);
        if (!empty($matches)) {
            $styles['from_style_tag'] = trim($matches[0]);
            break;
        }
    }

    return $styles;
}

/*
*
*/
/*
*get styles 
*/
function getStylle($element) {
    $styles = [
        'tagName' => $element->tagName,
        'class' => $element->getAttribute('class'),
        'name' => $element->getAttribute('name'),
        'id' => $element->getAttribute('id'),
    ];

    // Récupérer le style CSS inline de l'élément
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

    // Récupérer les styles CSS définis dans les balises <style>
    $styleTags = $element->ownerDocument->getElementsByTagName('style');
    foreach ($styleTags as $styleTag) {
        $css = $styleTag->textContent;
        $pattern = '/(?<=' . $element->tagName . '|' . $element->getAttribute('id') . '|\\.' . $element->getAttribute('class') . '|' . $element->getAttribute('name') . ')\s*{([^}]*)}/i';
        preg_match($pattern, $css, $matches);
        if (!empty($matches)) {
            $style = [];
            $declarations = explode(';', $matches[1]);
            foreach ($declarations as $declaration) {
                $declarationParts = explode(':', $declaration);
                if (count($declarationParts) === 2) {
                    $property = trim($declarationParts[0]);
                    $value = trim($declarationParts[1]);
                    $style[$property] = $value;
                }
            }
            $styles['style'] = isset($styles['style']) ? array_merge($styles['style'], $style) : $style;
        }
    }

    return $styles;
}


function getStyle3($element) {
    $styles = [
        'tagName' => $element->tagName,
        'class' => $element->getAttribute('class'),
        'name' => $element->getAttribute('name'),
        'id' => $element->getAttribute('id'),
        'style' => [], // initialisation du tableau de styles
    ];

    // Récupérer le style CSS inline de l'élément
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

    // Récupérer les styles CSS définis dans les balises <style>
    $styleTags = $element->ownerDocument->getElementsByTagName('style');
    foreach ($styleTags as $styleTag) {
        $css = $styleTag->textContent;
        $pattern = '/(?<=^|\})(?:[^{]+){([^}]+)}/';
        preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $declaration = $match[1];
            if (strpos($declaration, $element->tagName) !== false ||
                strpos($declaration, '#' . $element->getAttribute('id')) !== false ||
                strpos($declaration, '.' . $element->getAttribute('class')) !== false ||
                strpos($declaration, $element->getAttribute('name')) !== false) {
                // Si le match correspond à l'élément, ajouter les styles
                $declarations = explode(';', $declaration);
                foreach ($declarations as $declaration) {
                    $declarationParts = explode(':', $declaration);
                    if (count($declarationParts) === 2) {
                        $property = trim($declarationParts[0]);
                        $value = trim($declarationParts[1]);
                        $styles['style'][$property] = $value;
                    }
                }
            }
        }
    }

    return $styles;
}

function getStyle4($element) {
    $styles = [
        'tagName' => $element->tagName,
        'class' => $element->getAttribute('class'),
        'name' => $element->getAttribute('name'),
        'id' => $element->getAttribute('id'),
        'style' => [], // initialisation du tableau de styles
    ];

    // Récupérer le style CSS inline de l'élément
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

    // Récupérer les styles CSS définis dans les balises <style>
    $styleTags = $element->ownerDocument->getElementsByTagName('style');
    foreach ($styleTags as $styleTag) {
        $css = $styleTag->textContent;

        // Recherche de styles par tagName
        $pattern = '/(?<=^|\})(?:' . $element->tagName . '){([^}]*)}/';
        preg_match($pattern, $css, $matches);
        if (!empty($matches)) {
            $declarations = explode(';', $matches[1]);
            foreach ($declarations as $declaration) {
                list($property, $value) = explode(':', $declaration);
                $styles['style'][trim($property)] = trim($value);
            }
        }

        // Recherche de styles par class
        $class = $element->getAttribute('class');
        if (!empty($class)) {
            $pattern = '/(?<=^|\})(?:\.' . $class . '){([^}]*)}/';
            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                $declarations = explode(';', $matches[1]);
                foreach ($declarations as $declaration) {
                    list($property, $value) = explode(':', $declaration);
                    $styles['style'][trim($property)] = trim($value);
                }
            }
        }

        // Recherche de styles par ID
        $id = $element->getAttribute('id');
        if (!empty($id)) {
            $pattern = '/(?<=^|\})(?:#' . $id . '){([^}]*)}/';
            preg_match($pattern, $css, $matches);
            if (!empty($matches)) {
                $declarations = explode(';', $matches[1]);
                foreach ($declarations as $declaration) {
                    list($property, $value) = explode(':', $declaration);
                    $styles['style'][trim($property)] = trim($value);
                }
            }
        }
    }

    return $styles;
}

function getStyle($element) {
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

    // 2. Récupérer les styles CSS définis dans les balises <style>
    $styleTags = $element->ownerDocument->getElementsByTagName('style');
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
                $styles['style'][trim($property)] = trim($value);
            }
        }
    }

    // 3. Récupérer les styles CSS par class
    $class = $element->getAttribute('class');
    if (!empty($class)) {
        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            //$pattern = '/(?<=^|\})(?:\.' . $class . '){([^}]*)}/';
            $pattern = '/.' . $class . '+(?:,\s*.\w+)*\s*{([^}]+)}/';
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

    return $styles;
}




