<?php
session_start();


//hypothèse 2 paramètres d'entrée, controle et action, avec l'url de index.php
// exmple : index.php?controle=c1&action=a12

if (isset($_GET['controle']) && isset($_GET['action'])) {
    $controle = $_GET['controle'];
    $action = $_GET['action'];
} else { //absence de paramètres : prévoir des valeurs par défaut
    //echo "absence"; die();
    $controle = "Voiture";
    $action = "renderCatalogueVoitures";
}

//inclure le fichier php de contrôle
//et lancer la fonction-action issue de ce fichier.
// c1 = nom du fichier
// a11 = nom de la fonction
if (!file_exists("./controle/$controle.php")) {
    echo "file not exist" . $controle;die();
    $controle = "Voiture";
    $action = "renderCatalogueVoitures";
}

require_once('./controle/' . $controle . '.php');
$controle = new $controle();
if (!method_exists($controle, $action)) {
    echo "method not exist" . $action; die();
    $controle = "Voiture";
    $action = "renderCatalogueVoitures";
    require_once('./controle/' . $controle . '.php');
    $controle = new $controle();
    $controle->$action();


}
else
    //echo $action;die();
    $controle->$action();


?>