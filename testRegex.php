<?php

// Chaîne de caractères à vérifier
$string1 = "#age{color:green;text-decoration: underline;}";
$string2 = "#age { color:green;text-decoration: underline; }";
$string3 = "#age { color:green; text-decoration: underline; }";
$string4 = "#test { font-weight: bold; 
}";

$id="test";
// Expression régulière
$pattern = '/#'.$id.'+\s*{[^}]+}/';

// Vérifier les correspondances
if (preg_match($pattern, $string1)) {
    echo "Correspondance trouvée dans la chaîne 1.<br>\n";
}

if (preg_match($pattern, $string2)) {
    echo "Correspondance trouvée dans la chaîne 2.<br>\n";
}

if (preg_match($pattern, $string3)) {
    echo "Correspondance trouvée dans la chaîne 3.<br>\n";
}

if (preg_match($pattern, $string4)) {
    echo "Correspondance trouvée dans la chaîne 4.<br>\n";
} else {
    echo "Aucune correspondance trouvée dans la chaîne 4.\n";
}

?>
