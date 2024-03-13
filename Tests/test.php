<?php

//encoding 
$caractere_a = "Aller à l'école "; // le caractère à
$json_encode = json_encode($caractere_a);
//$json_encode = json_encode($caractere_a,JSON_UNESCAPED_UNICODE);
echo "$json_encode <br> \n"; // Affiche "\u00e0"







$texte_iso = "\u00c3\u00a0"; // la représentation Unicode incorrecte
$texte_html = "Aller \u00c3\u00a0 W3Schools, cliquer Ici"; // la représentation Unicode correcte
//$texte_utf8 = html_entity_decode($texte_html, ENT_QUOTES, 'UTF-8'); // Convertir les entités HTML en UTF-8
$texte_utf8 = json_decode('"'.$texte_html.'"');
echo "$texte_html = $texte_utf8"; // Affiche le caractère "à
