<?php

/* REMOVE NULL element , adding index
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


/* REMOVE NULL element without index
*
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

/* REMOVE Attribut vide des menus element without index
*
* Supprimer les attribut element dont la clé est une chaine vide ex :  "" => "lien// ", vide  dans un tableau sans ajouter l'index comme clé
*/
function removeNullAttributs($array) {
    $result = [];
    foreach ($array as $tab) {
        foreach ($tab as $key => $value) {
            if ($key !== "" && $key !== null && $value !== "") {
                //echo "Key : $key , value : $value \n";
                $result[] = $tab;
            }
        }
        
    }
    return $result;
}


/* TABLE Column in Database
*
* Fonction qui  retourne le nom de la colonne dans la BD de textBox soit {name sinon id , sinon placeholder , sinon "field" } 
*/
function getColumnName($tag){
    if($tag->getAttribute("name")!=""){
        return $tag->getAttribute("name");
    }else if($tag->getAttribute("id")!=""){
        return $tag->getAttribute("id");
    }else if($tag->getAttribute("placehoder")!=""){
        return $tag->getAttribute("placeholder");
    }else{
        return "field";
    }
}


/* FORMATING ST=tring
*
* Fonction qui formate les chaines de caractères à envoyer
*
*/
function formatText($chaine) {

    //echo "Text reçu :\n\t$chaine\n";

    // Décode la chaîne reçue
    $chaine = utf8_decode($chaine);

    // Supprimer les \n et \t
    $chaine = str_replace(array("\n", "\t"), '', $chaine);

    // Supprimer les espaces avant et après la chaîne
    $chaine = trim($chaine);

    // Remplacer plusieurs espaces par un seul espace
    $chaine = preg_replace('/\s+/', ' ', $chaine);

    // Remplacer un £ par le caractère "£"
    $chaine= str_replace('£', '#LivS', $chaine);

    // Remplacer un &nbsp par  un espace
    $chaine= str_replace('&nbsp', ' ', $chaine);

    // Remplacer un espace par le caractère "£"
    $chaine= str_replace(' ', '£', $chaine); //decommenter

    //Tilde
    $chaine= str_replace('~', '#tild#', $chaine); 

    //Remplacer " par ~ 
    $chaine= str_replace('"', '~', $chaine); //pour éviter un probleme car l'appli remplace 

    //Virgule 
    $chaine= str_replace(',', '#vir#', $chaine); 

    //Crochets
    $chaine= str_replace('[', '#croc', $chaine);
    $chaine= str_replace(']', 'croc#', $chaine); 

    //Accolades
    $chaine= str_replace('{', '#acol', $chaine);
    $chaine= str_replace('}', 'acol#', $chaine); 

    //echo "Text envoyé :\n\t$chaine\n\n\n";
    return $chaine;
}


/* FORMATING POUR LES MENUS ST=tring
*
* Fonction qui formate les chaines de caractères à envoyer et Eviter les accents
*
*/
function formatTextMenu($chaine) {

    //echo "Text reçu :\n\t$chaine\n";

    //Vérifie si la chaîne contient des caractères spéciaux accentués et les remplacer
    if (preg_match('/[ÉÀÈÊËÎÏÔŒÙÛÜÇéàèêëîïôœùûüç]/', $chaine)) {
        // Mise en majuscule et remplacement des caractères accentués
        $chaine = mb_strtoupper(str_replace(
            array('é', 'à', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'œ', 'ù', 'û', 'ü', 'ç', 'É', 'À', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Œ', 'Ù', 'Û', 'Ü', 'Ç'), 
            array('E', 'A', 'E', 'E', 'E', 'I', 'I', 'O', 'OE', 'U', 'U', 'U', 'C', 'E', 'A', 'E', 'E', 'E', 'I', 'I', 'O', 'OE', 'U', 'U', 'U', 'C'), 
            $chaine
        )); 
    }

    // Formatter les textes
    formatText($chaine);

    
    // ReVérifie si la chaîne contient des caractères spéciaux accentués et les remplacer
    if (preg_match('/[ÉÀÈÊËÎÏÔŒÙÛÜÇéàèêëîïôœùûüç]/', $chaine)) {
        // Mise en majuscule et remplacement des caractères accentués
        $chaine = mb_strtoupper(str_replace(
            array('é', 'à', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'œ', 'ù', 'û', 'ü', 'ç', 'É', 'À', 'È', 'Ê', 'Ë', 'Î', 'Ï', 'Ô', 'Œ', 'Ù', 'Û', 'Ü', 'Ç'), 
            array('E', 'A', 'E', 'E', 'E', 'I', 'I', 'O', 'OE', 'U', 'U', 'U', 'C', 'E', 'A', 'E', 'E', 'E', 'I', 'I', 'O', 'OE', 'U', 'U', 'U', 'C'), 
            $chaine
        ));
    }

    //echo "Text envoyé :\n\t$chaine\n\n\n";

    return $chaine;
}


/**  Formating URL  or COMPLETING URL
* 
*Fonction qui qui complète une url dans la balaise href
*Exemples:
*   href="/contact.html" => url=baseURL/contact.html
*   href="baseURL/contact.html" => url=baseURL/contact.html => déjà complet
*   href=" #id" => url=baseURL
*   href="/" => url=baseURL
*   href="baseURL/" => url=baseURL
*
* Functions:
*          string strtok(string $str ,string $token) :  divise une chaîne en sous-chaînes ("tokens")
*                                                       -pour extraire des parties d'une chaîne à l'aide d'un délimiteur spécifié.
*                                                       -@param : $str : chaine ,
*                                                       -@param : $token delimiteur
*
*          int strpos ( string $haystack , mixed $needle [, int $offset = 0 ] ) :  recherche la première occurrence d'une sous-chaîne dans une chaîne
*                                                       -@param : $haystack : La chaîne dans laquelle rechercher,
*                                                       -@param : $needle : La sous-chaîne à rechercher 
*                                                       -@param : $offset : La position de départ de la recherche dans la chaîne
*
*          string substr ( string $string , int $start [, int $length ] ) :  divise une chaîne en sous-chaînes ("tokens")
*                                                       -pour extraire des parties d'une chaîne d'une chaîne plus grande.
*                                                       -@param : $string : La chaîne d'origine
*                                                       -@param : $start : La position de départ de la sous-chaîne dans la chaîne d'origine 
*                                                       -@param : $length : La longueur de la sous-chaîne à extraire [par defaut : jusqu'à la fin].
*
* @param string $url L'URL à formater.
* @return string L'URL complétée.
*/
function formatURL($url){
    // Obtenir la base de l'URL (du site )
    $baseURL = getRequestBaseURL();

    // Enlever le dernier caractère de la $baseURL si c'est un '/'
    if (substr($baseURL, -1) === '/') {
        $baseURL = substr($baseURL, 0, -1);
    }

    // Vérifier si l'URL est déjà complète ou si elle pointe vers une ancre
    if (strpos($url, '/') === 0) {
        // L'URL commence par '/', il faut ajouter la base URL
        $url = $baseURL . strtok($url, '#'); // Ignorer l'ancre s'il y en a une
    } elseif (strpos($url, '#') === 0) {
        // L'URL commence par '#', il s'agit d'une ancre, on retourne juste la base URL
        $url = $baseURL;
    } 
    

    // Enlever le dernier caractère de la $url si c'est un '/' pour éviter sa se termine
    if (substr($url, -1) === '/') {
        $url = substr($url, 0, -1);
    }

    return strtok($url, '#'); // URL_complete#id =>URL_complete
}

