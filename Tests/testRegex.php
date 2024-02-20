<?php

// Chaîne de caractères à vérifier
$string1 = "#age{color:green;text-decoration: underline;}";
$string2 = "#age { color:green;text-decoration: underline; }";
$string3 = "#age { color:green; text-decoration: underline; }";
$string4 = "#test { font-weight: bold; }";
$string5='input[type=\'text\']{width: 100%;border-radius: 5px;box-sizing: border-box;}';
$string6='input[type = "text"],
input[type="password"],
textarea {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}';

$id="test";
$type="text";
// Expression régulière
$pattern = '/#'.$id.'+\s*{[^}]+}/';
$pattern = "/input\[type\s*=\s*(\"|')$type(\"|')\]" . '+(?:,\s*.(\w+|(\w+\[\w+\s*=\s*(\"|\')\w+(\"|\')\])))*\s*{([^}]+)}/'; //|(\w+\[\w+\s*=\s*\w+\])


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
}
if(preg_match('/\d+%$/', "10%" , $matches)){
    $nombre=intval(trim($matches[sizeof($matches)-1],'%'));
    echo "Correspondance trouvée dans la chaîne 10% :$nombre .<br>\n";
}
if (preg_match($pattern, $string6)) {
    echo "Correspondance trouvée dans la chaîne 5.<br>\n";
}else {
    echo "Aucune correspondance trouvée dans la chaîne 5.\n";
}

?>
