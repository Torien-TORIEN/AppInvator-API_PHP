<?php
$htmlContent='<!DOCTYPE html>
<html>
<head>
<style>
.container {background-color: powderblue;}
div{text-align:center}
h1{color: blue;}
.title{ text-align:center;}
p{font-weight: bold;}
#nom,#prenom{color:blue;}
#age{color:green;text-decoration: underline;}
button {
  display: inline-block;
  padding: 10px 20px;
  color: white;
  text-align: center;
  text-decoration: none;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
div.container {display: block;}
#submit{background-color: #4CAF50; }
input[type="text"]{
    width: 100%;
    border-radius: 5px;
    box-sizing: border-box;
}

</style>
</head>
<body>
<div class="container" style="width:100%">
  <h1 class="title" style="font-style: italic;width:10px">Informations </h1>
  <p>Nom : <span class="value" id="nom">Toto</span></p>
  <p>Prenom : <span class="value" id="prenom">Titi</span></p>
  <p>Age : <span class="value" id="age">20</span> ans</p>
  <label for="fname">First name:</label>
  <input type="text" id="fname" name="fname" value="TOTO" readonly><br><br>
  <button class="button" id="submit">Submit</button>
  <button class="button" id="cancel" style="background-color:red">Cancel</button> 
</div>

</body>
</html>';
