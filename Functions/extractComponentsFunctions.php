<?php

// Désactiver les avertissements
error_reporting(E_ERROR | E_PARSE);

/* Button
* Recuperer les  buttons 
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractButton($element,$html)
{
    $defaultFontSize=14.0;
    if(isButton($element)){
        $buttonData = [
            '$Type' => "Button",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => formatText($element->textContent != "" ? $element->textContent : $element->getAttribute("value")),//$element->nodeValue, // Utilise textContent pour extraire le texte
            //'$HeightPercent' => 15,
            //'$WidthPercent' => 50,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$Visible' => true,
            '$Action'=>'',
            'style' => getStyle($element,$html)["style"],
        ];

        // Vérifie s'il y a un lien à l'intérieur du bouton
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            $buttonData['$Action'] = 'link:'.formatURL($link->getAttribute('href'));
            //$buttonData['$Action'] = 'redirect';
            //$buttonData['$Link'] = $link->getAttribute('href');
        } else {
            // Si aucun lien, l'action est définie sur 'submit'
            $apiURL=getAPI_URL_BASE();
            $buttonData['$Action'] = "form:$apiURL"."data.php";//"form:URL_API", $Hint=pour texte box doit etre le nom de la colonne de BD 
        }

        //Mise à jour des Styles
        $buttonData=setStyle($buttonData["style"],$buttonData);

        return $buttonData;
    }else{
        return null;
    }
}

/* Label
* Recuperer les  textes de  [h1-6,label,p,span,a]
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractElementText($element,$html)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $tagNames = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li'];
    $parentTagNamesNotAccepted = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li','button','textarea'];
    $childMidleTagNames=['a','b','span','em','i','strong'];
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames) || (in_array($element->tagName, $childMidleTagNames) && isFirstParentNotInList($element,$parentTagNamesNotAccepted))) {
        $elementData = [
            '$Type' => "Label",
            '$Name' => $element->getAttribute('name'),
            '$Text' => formatText($element->textContent), // Utilise textContent pour extraire le texte
            //'$HeightPercent' => "",
            //'$WidthPercent' => "",
            //'$Height' => "",
            //'$Width' => "",
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' =>"",
            //'$BackgroundColor' => "",
            '$Visible' => true,
            'style' => getStyle($element,$html)["style"],

        ];

        // Vérifie s'il y a un lien à l'intérieur de l'élément
        $link = $element->getElementsByTagName('a')->item(0);
        if ($link !== null) {
            //$elementData['$Link'] = $link->getAttribute('href');
            $elementData['$Action'] = 'link:'.formatURL($link->getAttribute('href'));
        }

        if($element->tagName ==="a") {
            //$elementData['$Link'] = $element->getAttribute('href');
            $elementData['$Action'] = 'link:'.formatURL($element->getAttribute('href'));
        }

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

        //Met à jour les styles
        $elementData=setStyle($elementData["style"],$elementData);

        

    }

    return $elementData;
}


/* TextBox
* Recuperer les champs des textes [input, textarea]
* $Hint : le nom de la colonne => donc l'attribut name
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractTextBoxElements($element,$html)
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
            '$Text' => formatText($element->getAttribute('value')!=""?$element->getAttribute('value'):$element->textContent),
            '$Hint' => formatText(getColumnName($element)),
            //'$HeightPercent' => 20,
            //'$WidthPercent' => 50,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$ReadOnly' => $element->hasAttribute('readonly') ? true : false,
            '$MultiLine' => $isTextArea ? true : false,
            '$NumbersOnly' => false,
            '$Visible' => true,
            'style' => getStyle($element,$html)["style"],
        ];
    }
    
    //Mise à jour des Styles
    $elementData=setStyle($elementData["style"],$elementData);

    return $elementData;
}


/* Image
* Recuperer les images
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractImage($element,$html)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if ($element->tagName==="img" ) {
        $elementData = [
            '$Type' =>"Image",
            //'$Name' => $element->getAttribute('name'),
            '$AlternateText' => $element->getAttribute('alt'),
            '$Picture' => $element->getAttribute('src'),
            '$HeightPercent' => preg_match('/\d+%$/', $element->getAttribute("height"),$matches)?intval(trim($matches[sizeof($matches)-1],'%')):"",
            '$WidthPercent' =>preg_match('/\d+%$/', $element->getAttribute("width"),$matches)?intval(trim($matches[sizeof($matches)-1],'%')):100,
            '$Height' => preg_match('/\d+px$/', $element->getAttribute("height"),$matches)?intval(trim($matches[sizeof($matches)-1],'px')):"",
            '$Width' =>preg_match('/\d+px$/', $element->getAttribute("width"),$matches)?intval(trim($matches[sizeof($matches)-1],'px')):"",
            '$ScalePictureToFit' =>false, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$Clickable' => false,
            '$Visible' => true,
            'style' => getStyle($element,$html)["style"],
        ];
    }

    //Mise à jour des Styles
    $elementData=setStyle($elementData["style"],$elementData);

    return $elementData;
}


/* PasswordTextBox
* Recuperer les champs de mot de passe  [input (type=password)]
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractPasswordTextBoxElements($element,$html)
{
    $defaultFontSize = 14.0; // Taille de police par défaut en pixels
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if ($element->tagName=="input" && $element->getAttribute('type') == 'password') {
        $isTextArea = $element->tagName === 'textarea';
        $elementData = [
            '$Type' =>"PasswordTextBox",
            //'$Name' => $element->getAttribute('name'),
            '$Text' => formatText($element->getAttribute('value')),
            '$Hint' => formatText(getColumnName($element)),
            //'$HeightPercent' => 10,
            //'$WidthPercent' => 100,
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' => "",
            //'$BackgroundColor' => "",
            '$ReadOnly' => $element->hasAttribute('readonly') ? true : false,
            '$NumbersOnly' => false,
            '$Visible' => true,
            //'style' => getStyle($element,$html)["style"],
        ];
    }
    
    //Mise à jour des Styles
    $elementData=setStyle($elementData["style"],$elementData);

    return $elementData;
}



/* Lyout
* Recuperer les lyouts [div, section , article, ...] si parmi les enfants de niveau 1 de ce div il y a ces listes suivantes
*[h1-6,label,p,span,a,button,img,table,]
*Si l'enfant de ce tag est seulement parmis [div,section,form,article,...] on le recupère pas
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractLyout($element,$html)
{
    $ContainerTagNames=['div', 'section', 'article','nav','form','header','footer','aside','body'];
    $PositionHorizontal = ['Left' => 1, 'Right' => 2, 'Center' => 3];
    $PositionVertical = ['Top' => 1, 'Center' => 2, 'Bottom' => 3];
    
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $ContainerTagNames)) {
        // Initialise le contenu
        $contents = [];

        $isVerticalArragement=false;

        // Parcours les enfants de niveau 1 : Si un de ses enfants sont dans la liste donc VerticalArragement 
        foreach ($element->childNodes as $child) {
            // Vérifie si l'un des enfants est une balise autorisée
			if ($child instanceof DOMElement && in_array($child->tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p', 'span', 'a', 'button', 'img', 'input','table','ul','li','ol','dt','dd'])) {
                // Ajoute les données de l'enfant dans le contenu
                //$contents[] =extractElement($child);//[$child->tagName]; //extractElement($child);
                

                $isVerticalArragement=true;

            }
        }

        if($isVerticalArragement){
            $elementHTML = $element->ownerDocument->saveHTML($element);
            $tags = ['h1','h2','h3','h4', 'h5','h6','button','textarea','img','p', 'label', 'input']; //ne pas ajouter la balise a
            $contents[]=extractHorizontalLyout($elementHTML, $tags,$html);
        }

        // Vérifie si le contenu n'est pas vide
        if (!empty($contents)) {
            // Détermine le type de disposition en fonction du tag de l'élément parent
            $layoutType = "VerticalArrangement";

            $elementData = [
                '$Type' => $layoutType,
                '$Name' => $element->tagName,//$element->getAttribute('name'),
                '$AlignHorizontal' => $PositionHorizontal["Left"],
                '$AlignVertical' => $PositionVertical["Top"],
                //'$Image' => '', // Lien de l'image de fond (à compléter si nécessaire)
                //'$Height' => "",
                //'$Width' => "",
                '$BackgroundColor' => "[255,255,255]",
                'style'=>getStyle($element,$html)['style'],
                '$Visible' => true,
                '$Components' => $contents
            ];

            //Mise à jour des Styles
            $elementData=setStyle($elementData["style"],$elementData);


        }
    }

    return $elementData;
}


/* EXTRACT ALL COMPONENTS
*
* fonction qui extrait les composants en appélant les autres fonctions définies
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractElement($element,$html)
{
    $elementData=null;
    if(($button=extractButton($element,$html))!=null){
        $elementData=$button;
    }elseif(($Label=extractElementText($element,$html))!=null){
        $elementData=$Label;
    }elseif(($TextBox=extractTextBoxElements($element,$html))!=null){
        $elementData=$TextBox;
    }elseif(($PasswordTextBox=extractPasswordTextBoxElements($element,$html))!=null){
        $elementData=$PasswordTextBox;
    }elseif(($Image=extractImage($element,$html))!=null){
        $elementData=$Image;
    }
    
    return $elementData;
}








