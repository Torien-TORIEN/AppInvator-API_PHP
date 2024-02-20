<?php

require_once "extractComponentsFunctions.php";
require_once "checkFunctions.php";
require_once "styleFunctions.php";
require_once "colorCodeFunctions.php";

// Désactiver les avertissements
error_reporting(E_ERROR | E_PARSE);

/*
* Fonction qui extrait dans un meme  tableau des composants horizontal
*
*Params: $elementHTML         => Element DOM
*        $tagsListAccepted    => Listes de tagnames acceptés
*        $html                => Page html complet reçu : pour permettre d'analyser le style
*/
function findInlineTags($elementHTML, $tagsListAccepted,$html) {
    // Créer un nouveau document DOM
    $dom = new DOMDocument();
    // Charger le HTML dans le document DOM (la suppression du '@' devant permet de masquer les avertissements)
    @$dom->loadHTML($elementHTML);

    //tags inlines
    $tagsInlines=['span','a','strong','em','img','input','button','label','abbr','cite'];

    // Tableau pour stocker les balises alignées
    $resultTags = [];

    
    // Récupérer toutes les balises du document HTML
    $elements = $dom->getElementsByTagName('*');

    // Variables pour suivre le groupe actuel d'alignement et l'index actuel à l'intérieur de ce groupe
    $currentInlineIndex = 0;

    // Parcourir toutes les balises du document HTML
    $previousTagName=null;
    $previousTag=null;
    foreach ($elements as $element) {
        // Récupérer le nom de la balise
        $currentTag= $element;
        $currentTagName = $element->tagName;

        // Vérifier si la balise fait partie des balises spécifiées
        if (in_array($currentTagName, $tagsListAccepted)) {
          $currentStyle=getStyle($currentTag,$html)['style'];
          
          //Tag sans précedant ou n'est pas dans le meme contenaire
          if($previousTagName===null || ($previousTag->parentNode!==$currentTag->parentNode) ){
            array_push($resultTags, [extractElement($element,$html)]);
            $currentInlineIndex+=1;

            //les 2 Tags dans le meme contenaire 
          }else{
            
            $previousStyle=getStyle($previousTag,$html)['style'];

            //currentTag n'est pas inline par défaut
            if(!in_array($currentTagName,$tagsInlines)){
              
              //Tag precedent est inline par defaut
              if(in_array($previousTagName,$tagsInlines)){

                //display:inline
                if( ((isset($currentStyle['display']) && preg_match('/^inline(-\w)*$/',$currentStyle['display'])) && (!isset($previousStyle['display']) || $previousStyle['display']!=="block") ) || $currentInlineIndex===0){
                  
                  array_push($resultTags[$currentInlineIndex -1], extractElement($element,$html));
                }else{
                  
                  array_push($resultTags, [extractElement($element,$html)]);
                  $currentInlineIndex+=1;
                }

              }else{//Tag precedent n'est pas display:inline par defaut mais display:block
                
                //inline
                if((isset($previousStyle['display']) && preg_match('/^inline(-\w)*$/',$previousStyle['display'])) && ( isset($currentStyle['display']) && !preg_match('/^inline(-\w)*$/',$currentStyle['display'])) && $currentInlineIndex!==0){
                  array_push($resultTags[$currentInlineIndex-1], extractElement($element,$html));
                }else{//previous : display !=inline
                  array_push($resultTags, [extractElement($element,$html)]);
                  $currentInlineIndex+=1;
                }
              }

            }else{//currentTag est inline par defaut

              //Tag precedent est inline par defaut
              if(in_array($previousTagName,$tagsInlines)){

                //display:block
                if((isset($currentStyle['display']) && $currentStyle['display']==="block") || (isset($previousStyle['display']) && $previousStyle['display']==="block") || $currentInlineIndex===0){
                  array_push($resultTags, [extractElement($element,$html)]);
                  $currentInlineIndex+=1;
                }else{
                  
                  //echo "current id : $currentInlineIndex , lenght: ".sizeof($resultTags).", tagName : $currentTagName <br>\n";
                  array_push($resultTags[$currentInlineIndex -1], extractElement($element,$html));
                }

              }else{//Tag precedent n'est pas display:inline par defaut mais display:block
                
                //pevious :display="inline" et current :display!=block
                if((isset($previousStyle['display']) && preg_match('/^inline(-\w)*$/',$previousStyle['display']) ) && ( !isset($currentStyle['display']) || $currentStyle['display']!=="block") && $currentInlineIndex!==0){
                  array_push($resultTags[$currentInlineIndex-1], extractElement($element,$html));
                }else{//previous : display !=inline
                  array_push($resultTags, [extractElement($element,$html)]);
                  $currentInlineIndex+=1;
                }
              }
            }
          }

        }
        
        $previousTag=$currentTag;
        $previousTagName=$currentTagName;
    }

    // Retourner le tableau des balises alignées
    return $resultTags;
}


/*
* fonction qui structure les components en HorizontalArragement
*
*Params: $elementHTML         => Element DOM
*        $tagsListAccepted    => Listes de tagnames acceptés
*        $html                => Page html complet reçu : pour permettre d'analyser le style
*/
function extractHorizontalLyout ($elementHTML,$tagsListAccepted,$htmlPage){

  //Data
  $PositionHorizontal = ['Left' => 1, 'Right' => 2, 'Center' => 3];
  $PositionVertical = ['Top' => 1, 'Center' => 2, 'Bottom' => 3];
  $HorizontalArragement = [
    '$Type' => "HoizontalArragement",
    //'$Name' => "",//$element->getAttribute('name'),
    '$AlignHorizontal' => $PositionHorizontal["Center"],
    '$AlignVertical' => $PositionVertical["Top"],
    //'$Image' => '', // Lien de l'image de fond (à compléter si nécessaire)
    //'$HeightPercent' => "",
    '$WidthPercent' => 100,
    //'$Height' => "",
    //'$Width' => "",
    '$BackgroundColor' => "",
    '$Visible' => true,
    '$Components' => []
  ];
  //data à retourner
  $returnData=[];
  //Tableau
  $tabs=findInlineTags($elementHTML, $tagsListAccepted,$htmlPage);

  foreach($tabs as $horizontalTab){
    //echo "dans le premier boucle\n";
    if($horizontalTab[0]!==null){
      //echo "=>Non NULL\n";
      if(sizeof($horizontalTab)>1){//contien au moins deux composants :
        //echo "==> Taille sup à 1\n\n";
        array_push($returnData,$HorizontalArragement);
        foreach($horizontalTab as $component){
          array_push($returnData[sizeof($returnData)-1]['$Components'],$component);
        }

      }else{//contien qu'un composant => donc ça ne sert à rien de le mettre dans un LyoutHorizontal
        array_push($returnData,$horizontalTab[0]);
        //echo "==> Taille egale à 1\n\n";
      }

    }
  }

  return $returnData;
  
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


$html='<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .button {
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        .button1 {
            background-color: #04AA6D;
        }

        .button2 {
            background-color: #008CBA;
        }

        h1 {
            color: #333;
        }

        h3 {
            color: #666;
        }

        p {
            color: #888;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px auto;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #04AA6D;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <div>
        <a href="https://www.w3schools.com">Lien W3school</a>
        <h1>The button element - Styled with CSS</h1>
        <h3>Aller à W3Schools, cliquer <a href="https://www.w3schools.com">Ici</a></h3>
        <p>Change the background color of a button with the background-color property:</p>
    </div>

    <div>
        <img src="https://i.ytimg.com/vi/JJt9tVcrXRw/maxresdefault.jpg" alt="LOGO">
        <button class="button button1">Green</button>
        <input type="button" class="button button2" value="Blue">
    </div>

    <div>
        <form action="">
            <label for="fname">First name:</label>
            <input type="text" id="fname" name="fname" value="TOTO" readonly><br><br>
            <label for="lname">Last name:</label>
            <input type="text" id="lname" name="lname" placeholder="Your name"><br><br>
            <label for="pwd">Password:</label>
            <input type="password" id="pwd" name="pwd"><br><br>
            <label for="w3review">Review of W3Schools:</label>
            <textarea id="w3review" name="w3review" rows="4" cols="50">At w3schools.com you will learn how to make a website. They offer free tutorials in all web development technologies.</textarea>
            <input type="submit" value="Submit">
            <button class="button button1"><a href="https://www.w3schools.com" style="text-decoration: none; color: white;">Visit W3Schools.com!</a></button>
        </form>
    </div>
</div>

</body>
</html>';
$tags = ['h1','h2','h3','h4', 'h5','h6','button','textarea','img','p', 'label', 'input','a'];

//$result = findInlineTags($html, $tags);

//$result = extractHorizontalLyout($html, $tags,$html);

/*header('Content-Type: application/json');
echo json_encode($result);*/
