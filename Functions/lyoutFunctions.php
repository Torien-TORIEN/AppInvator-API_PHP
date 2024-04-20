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
    @$dom->loadHTML($elementHTML,LIBXML_HTML_NOIMPLIED); // l'option LIBXML_HTML_NOIMPLIED pour éviter l'ajout implicite de balises <html> et <body>.

    //tags inlines
    $tagsInlines=['span','strong','em','img','input','button','label','abbr','cite',"a"];
    $parentTagNamesNotAccepted = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p','li','button','textarea'];

    // Tableau pour stocker les balises alignées
    $resultTags = [];


    /********************************************** PROBLEME ***************************************************************** */
    // Récupérer toutes les balises du document HTML
    $root = $dom->documentElement;
    //$elements = $dom->getElementsByTagName('*'); //à eviter car il prends tous les Tags : Répétiotion des champs 
    //$elements = $root->childNodes; // Prends seulement les premiers enfants : contient noeud text entre chaque balise donc toujours VerticalArragement pas HorizontalArragement

    /***************************************************** SOLUTIONS *************************************************** */
    // Tableau pour stocker les balises HTML uniquement : ne pas conserver les noeuds textes, commentaires
    $elements = [];

    // Parcourir les enfants du nœud racine
    foreach ($root->childNodes as $child) {
        // Vérifier si le nœud est une balise HTML
        if ($child->nodeType === XML_ELEMENT_NODE) {
            // Ajouter la balise à notre tableau
            $elements[] = $child;
        }
    }
    /*********************************************************************************************************************** */


    // Variables pour suivre le groupe actuel d'alignement et l'index actuel à l'intérieur de ce groupe
    $currentInlineIndex = 0;

    // Parcourir toutes les balises du document HTML
    $previousTagName=null;
    $previousTag=null;
    foreach ($elements as $element) {
        //echo "tagNAME :$element->tagName\n"; // pourquoi ici j'ai des 

        // Récupérer le nom de la balise
        $currentTag= $element;
        $currentTagName = $element->tagName;

        // Vérifier si la balise fait partie des balises spécifiées
        if ($element instanceof DOMElement && (in_array($currentTagName, $tagsListAccepted) || ($currentTagName ==="a" && !in_array($currentTag->parentNode->tagName,$parentTagNamesNotAccepted))) ) {
          $currentStyle=getStyle($currentTag,$html)['style'];
          //echo "TagName : $currentTagName\n";

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


        // Si on  met  à jour previousTag si le parent de currentTag n'est pas dans cette liste 
        //On met à jour si le parent direct n'est pas dans la liste $parentTagNamesNotAccepted (Cas de la balise <a> )
        if(isFirstParentNotInList($currentTag,$parentTagNamesNotAccepted)){
          $previousTag=$currentTag;
          $previousTagName=$currentTagName;
        }
    }

    // Retourner le tableau des balises alignées
    return $resultTags;
}


/* Lyout : HorinzontalArragement
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
    '$Type' => "HorizontalArrangement",
    //'$Name' => "",//$element->getAttribute('name'),
    '$AlignHorizontal' => $PositionHorizontal["Left"],
    '$AlignVertical' => $PositionVertical["Top"],
    //'$Image' => '', // Lien de l'image de fond (à compléter si nécessaire)
    //'$HeightPercent' => "",
    '$WidthPercent' => 100,
    //'$Height' => "",
    //'$Width' => "",
    //'$BackgroundColor' => "",
    '$Visible' => true,
    '$Components' => []
  ];
  //data à retourner
  $returnData=[];
  //Tableau
  $tabs=findInlineTags($elementHTML, $tagsListAccepted,$htmlPage);

  //print_r($tabs);

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


/* Lyout : VerticalArragement
* Recuperer les lyouts [div, section , article, ...] si parmi les enfants de niveau 1 de ce div il y a ces listes suivantes
*[h1-6,label,p,span,a,button,img,table,]
*Si l'enfant de ce tag est seulement parmis [div,section,form,article,...] on le recupère pas
*
*Params: $element => Element DOM
*        $html    => Page html complet reçu : pour permettre d'analyser le style
*/
function extractLyout($element,$html)
{
    $ContainerTagNames=['div', 'section', 'article','nav','form','header','footer','aside','body','ul','ol','dl', 'figure'];
    $PositionHorizontal = ['Left' => 1, 'Right' => 2, 'Center' => 3];
    $PositionVertical = ['Top' => 1, 'Center' => 2, 'Bottom' => 3];
    
    $elementData = [];

    // Vérifie si le tag de l'élément est dans la liste des balises autorisées
    if (in_array($element->tagName, $ContainerTagNames)) {
        // Initialise le contenu
        $contents = [];
        //echo "Lyout VA :+$element->tagName =>";
        $isVerticalArragement=false;

        // Parcours les enfants de niveau 1 : Si un de ses enfants sont dans la liste donc VerticalArragement 
        foreach ($element->childNodes as $child) {
            // Vérifie si l'un des enfants est une balise autorisée (balises de textes ou images)
			    if ($child instanceof DOMElement && in_array($child->tagName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'label', 'p', 'span','strong', 'a', 'button', 'img', 'input','table','li','dt','dd'])) {
              // Ajoute les données de l'enfant dans le contenu
              //$contents[] =extractElement($child);//[$child->tagName]; //extractElement($child);
              


              $isVerticalArragement=true;//Si true , On prend la balise  comme  VerticalArragement

          }
        }
        //echo "$isVerticalArragement \n";
        if($isVerticalArragement){
            $elementHTML = $element->ownerDocument->saveHTML($element);
            //Balises de textes ou image
            $tags = ['h1','h2','h3','h4', 'h5','h6','button','textarea','img','p', 'label', 'input','select','hr','dd','dt','li','pre']; //ne pas ajouter la balise a
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
                //'$Components' => $contents
            ];

            //Mise à jour des Styles
            $elementData=setStyle($elementData["style"],$elementData);

            //Mettre l'attribut $Components à la fin (après avoir mis à jour le style)
            $elementData['$Components']=$contents;


        }
    }
    return $elementData;
}



//$tags = ['h1','h2','h3','h4', 'h5','h6','button','textarea','img','p', 'label', 'input','a'];

//$result = findInlineTags($html, $tags);

//$result = extractHorizontalLyout($html, $tags,$html);

/*header('Content-Type: application/json');
echo json_encode($result);*/

