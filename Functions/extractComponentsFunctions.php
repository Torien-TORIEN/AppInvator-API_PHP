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
    $tagNames = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li',"dt","dd","pre"];
    $parentTagNamesNotAccepted = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li','button','textarea',"dt","dd"];
    $childMidleTagNames=['a','b','span','em','i','strong'];
    $elementData = [];

    //&&(($link = $element->getElementsByTagName('a')->item(0))==null || (($link = $element->getElementsByTagName('a')->item(0))!==null && $link->getAttribute('class') !=="only-menu") )
    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $tagNames) || (in_array($element->tagName, $childMidleTagNames) && isFirstParentNotInList($element,$parentTagNamesNotAccepted))  ) {
        
        // Vérifier si c'est une balise <a> contenant une balise <img>
        if (($element->tagName === 'a' && $element->getElementsByTagName('img')->length > 0)) {
            //return null;
             // Appeler la fonction extractImage pour traiter l'image
             $Imagedata=extractImage($element->getElementsByTagName('img')->item(0), $html);
             $Imagedata['$Clickable']=true;
             $Imagedata['$Action'] = 'link:'.formatURL($element->getAttribute('href'));
             return $Imagedata;
        }else if(in_array($element->tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p']) && $element->getElementsByTagName('img')->length > 0){
            return extractImage($element->getElementsByTagName('img')->item(0), $html);
        }else if(formatText($element->textContent) ==""){
            return null;
        }

        $elementData = [
            '$Type' => "Label",
            '$Name' => $element->tagName,# $element->getAttribute('name'),
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
            if($link->getAttribute('class')==="only-menu"){
                $elementData['$Visible'] =false;
            }
        }

        if($element->tagName ==="a" ) {
            //$elementData['$Link'] = $element->getAttribute('href');
            $elementData['$Action'] = 'link:'.formatURL($element->getAttribute('href'));
        }

        // Rendre Invisible si c'est un menu
        if(containsNonMenuClass($element,"only-menu")){
            $elementData['$Visible']=false;
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

        
    //Recuperer la ligne hr sous forme de Label
    }elseif($element->tagName==="hr"){
        $elementData = [
            '$Type' => "Label",
            '$Name' => "hr",
            '$Text' => " ", // Utilise textContent pour extraire le texte
            //'$HeightPercent' => "",
            '$WidthPercent' => 100,
            '$Height' => 1,
            //'$Width' => "",
            '$FontSize' => $defaultFontSize, // Taille de police par défaut
            '$FontBold' => false,
            //'$TextColor' =>"",
            '$BackgroundColor' => "[0,0,0]",
            '$Visible' => true,
            //'style' => getStyle($element,$html)["style"],

        ];
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
    if ($element->tagName==="img") {
        $elementData = [
            '$Type' =>"Image",
            //'$Name' => $element->getAttribute('name'),
            '$AlternateText' => $element->getAttribute('alt'),
            '$Picture' => formatURL($element->getAttribute('src')),
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
        $defaultSelection = formatText($elements[0]);
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
            '$WidthPercent' => 100,
            //'$FontSize' => $defaultFontSize, // Taille de police par défaut
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






/* EXTRACT ALL COMPONENTS
*
* fonction qui extrait les composants en appélant les autres fonctions définies
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractElement($element,$html)
{   
    //echo "Balise $element->tagName\n";
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
    }elseif(($Select=extractSelectElements($element,$html))!=null){
        $elementData=$Select;
    }
    
    return $elementData;
}








