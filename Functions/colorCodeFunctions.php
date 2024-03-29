<?php
function listColors(){
    $ColorCodes = array(
        "aliceblue" => array(240, 248, 255),
        "antiquewhite" => array(250, 235, 215),
        "aqua" => array(0, 255, 255),
        "aquamarine" => array(127, 255, 212),
        "azure" => array(240, 255, 255),
        "beige" => array(245, 245, 220),
        "bisque" => array(255, 228, 196),
        "black" => array(0, 0, 0),
        "blanchedalmond" => array(255, 235, 205),
        "blue" => array(0, 0, 255),
        "blueviolet" => array(138, 43, 226),
        "brown" => array(165, 42, 42),
        "burlywood" => array(222, 184, 135),
        "cadetblue" => array(95, 158, 160),
        "chartreuse" => array(127, 255, 0),
        "chocolate" => array(210, 105, 30),
        "coral" => array(255, 127, 80),
        "cornflowerblue" => array(100, 149, 237),
        "cornsilk" => array(255, 248, 220),
        "crimson" => array(220, 20, 60),
        "cyan" => array(0, 255, 255),
        "darkblue" => array(0, 0, 139),
        "darkcyan" => array(0, 139, 139),
        "darkgoldenrod" => array(184, 134, 11),
        "darkgray" => array(169, 169, 169),
        "darkgreen" => array(0, 100, 0),
        "darkgrey" => array(169, 169, 169),
        "darkkhaki" => array(189, 183, 107),
        "darkmagenta" => array(139, 0, 139),
        "darkolivegreen" => array(85, 107, 47),
        "darkorange" => array(255, 140, 0),
        "darkorchid" => array(153, 50, 204),
        "darkred" => array(139, 0, 0),
        "darksalmon" => array(233, 150, 122),
        "darkseagreen" => array(143, 188, 143),
        "darkslateblue" => array(72, 61, 139),
        "darkslategray" => array(47, 79, 79),
        "darkslategrey" => array(47, 79, 79),
        "darkturquoise" => array(0, 206, 209),
        "darkviolet" => array(148, 0, 211),
        "deeppink" => array(255, 20, 147),
        "deepskyblue" => array(0, 191, 255),
        "dimgray" => array(105, 105, 105),
        "dimgrey" => array(105, 105, 105),
        "dodgerblue" => array(30, 144, 255),
        "firebrick" => array(178, 34, 34),
        "floralwhite" => array(255, 250, 240),
        "forestgreen" => array(34, 139, 34),
        "fuchsia" => array(255, 0, 255),
        "gainsboro" => array(220, 220, 220),
        "ghostwhite" => array(248, 248, 255),
        "gold" => array(255, 215, 0),
        "goldenrod" => array(218, 165, 32),
        "gray" => array(128, 128, 128),
        "green" => array(0, 128, 0),
        "greenyellow" => array(173, 255, 47),
        "grey" => array(128, 128, 128),
        "honeydew" => array(240, 255, 240),
        "hotpink" => array(255, 105, 180),
        "indianred" => array(205, 92, 92),
        "indigo" => array(75, 0, 130),
        "ivory" => array(255, 255, 240),
        "khaki" => array(240, 230, 140),
        "lavender" => array(230, 230, 250),
        "lavenderblush" => array(255, 240, 245),
        "lawngreen" => array(124, 252, 0),
        "lemonchiffon" => array(255, 250, 205),
        "lightblue" => array(173, 216, 230),
        "lightcoral" => array(240, 128, 128),
        "lightcyan" => array(224, 255, 255),
        "lightgoldenrodyellow" => array(250, 250, 210),
        "lightgray" => array(211, 211, 211),
        "lightgreen" => array(144, 238, 144),
        "lightgrey" => array(211, 211, 211),
        "lightpink" => array(255, 182, 193),
        "lightsalmon" => array(255, 160, 122),
        "lightseagreen" => array(32, 178, 170),
        "lightskyblue" => array(135, 206, 250),
        "lightslategray" => array(119, 136, 153),
        "lightslategrey" => array(119, 136, 153),
        "lightsteelblue" => array(176, 196, 222),
        "lightyellow" => array(255, 255, 224),
        "lime" => array(0, 255, 0),
        "limegreen" => array(50, 205, 50),
        "linen" => array(250, 240, 230),
        "magenta" => array(255, 0, 255),
        "maroon" => array(128, 0, 0),
        "mediumaquamarine" => array(102, 205, 170),
        "mediumblue" => array(0, 0, 205),
        "mediumorchid" => array(186, 85, 211),
        "mediumpurple" => array(147, 112, 219),
        "mediumseagreen" => array(60, 179, 113),
        "mediumslateblue" => array(123, 104, 238),
        "mediumspringgreen" => array(0, 250, 154),
        "mediumturquoise" => array(72, 209, 204),
        "mediumvioletred" => array(199, 21, 133),
        "midnightblue" => array(25, 25, 112),
        "mintcream" => array(245, 255, 250),
        "mistyrose" => array(255, 228, 225),
        "moccasin" => array(255, 228, 181),
        "navajowhite" => array(255, 222, 173),
        "navy" => array(0, 0, 128),
        "oldlace" => array(253, 245, 230),
        "olive" => array(128, 128, 0),
        "olivedrab" => array(107, 142, 35),
        "orange" => array(255, 165, 0),
        "orangered" => array(255, 69, 0),
        "orchid" => array(218, 112, 214),
        "palegoldenrod" => array(238, 232, 170),
        "palegreen" => array(152, 251, 152),
        "paleturquoise" => array(175, 238, 238),
        "palevioletred" => array(219, 112, 147),
        "papayawhip" => array(255, 239, 213),
        "peachpuff" => array(255, 218, 185),
        "peru" => array(205, 133, 63),
        "pink" => array(255, 192, 203),
        "plum" => array(221, 160, 221),
        "powderblue" => array(176, 224, 230),
        "purple" => array(128, 0, 128),
        "rebeccapurple" => array(102, 51, 153),
        "red" => array(255, 0, 0),
        "rosybrown" => array(188, 143, 143),
        "royalblue" => array(65, 105, 225),
        "saddlebrown" => array(139, 69, 19),
        "salmon" => array(250, 128, 114),
        "sandybrown" => array(244, 164, 96),
        "seagreen" => array(46, 139, 87),
        "seashell" => array(255, 245, 238),
        "sienna" => array(160, 82, 45),
        "silver" => array(192, 192, 192),
        "skyblue" => array(135, 206, 235),
        "slateblue" => array(106, 90, 205),
        "slategray" => array(112, 128, 144),
        "slategrey" => array(112, 128, 144),
        "snow" => array(255, 250, 250),
        "springgreen" => array(0, 255, 127),
        "steelblue" => array(70, 130, 180),
        "tan" => array(210, 180, 140),
        "teal" => array(0, 128, 128),
        "thistle" => array(216, 191, 216),
        "tomato" => array(255, 99, 71),
        "turquoise" => array(64, 224, 208),
        "violet" => array(238, 130, 238),
        "wheat" => array(245, 222, 179),
        "white" => array(255, 255, 255),
        "whitesmoke" => array(245, 245, 245),
        "yellow" => array(255, 255, 0),
        "yellowgreen" => array(154, 205, 50)
    );
    return $ColorCodes;
}


/*
* Convertir la couleur en  RGB sous forme de chaine du tableau : "[R,G,B]"
*/
function colorToRGB($couleur,$ColorCodes) {
    // Si la couleur commence par un "#" suivi de 3 ou 6 caractères hexadécimaux
    if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $couleur)) {
        // Supprimer le "#" de la chaîne de couleur
        $couleur = ltrim($couleur, '#');
        
        // Convertir la couleur hexadécimale en valeurs décimales
        if(strlen($couleur)===6){
            $r = hexdec(substr($couleur, 0, 2));
            $g = hexdec(substr($couleur, 2, 2));
            $b = hexdec(substr($couleur, 4, 2));
        }elseif(strlen($couleur)===3){
            $r = hexdec(substr($couleur, 0, 1).substr($couleur, 0, 1));
            $g = hexdec(substr($couleur, 1, 1).substr($couleur, 1, 1));
            $b = hexdec(substr($couleur, 2, 1).substr($couleur, 2, 1));
        }
        
        // Retourner les valeurs RGB dans un tableau
        $array= array($r, $g, $b);
        return json_encode($array);
    } elseif (preg_match('/^rgb\((\d{1,3}), (\d{1,3}), (\d{1,3})\)$/', $couleur, $matches)) {
        // Si la couleur est déjà en format RGB
        $array= array((int)$matches[1], (int)$matches[2], (int)$matches[3]);
        return json_encode($array);
    } elseif(array_key_exists($couleur, $ColorCodes)) {
        // Si la couleur est dans le tableau associatif $couleurs
        $array= $ColorCodes[$couleur];
        return json_encode($array);
    }else{//couleur invalide
        return "";
    }
}

// Exemples d'utilisation :
/*
$ColorCodes=listColors();
$couleur1 = "powderblue";
$couleur2 = "blue";
$couleur3 = "#4CAF50";
$couleur4 = "#4caf50";
$couleur5 ="#333";

print_r(colorToRGB($couleur1,$ColorCodes)); // Output: Array ( [0] => 255 [1] => 0 [2] => 0 )
print_r(colorToRGB($couleur2,$ColorCodes)); // Output: Array ( [0] => 0 [1] => 0 [2] => 255 )
print_r(colorToRGB($couleur3,$ColorCodes)); // Output: Array ( [0] => 76 [1] => 175 [2] => 80 )
print_r(colorToRGB($couleur4,$ColorCodes)); // Output: Array ( [0] => 76 [1] => 175 [2] => 80 )
print_r(colorToRGB($couleur5,$ColorCodes));
*/