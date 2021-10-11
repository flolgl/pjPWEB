<?php

class Voiture{
    public function renderCatalogueVoitures(){
        require("./modele/VoitureDB.php");
        $voitures = VoitureDB::getVoituresDispo();
        require("./vue/accueil.html");
    }

    public function addToPanier(){
        if (!isset($_GET["voitureId"]))
            return;


        if (!isset($_SESSION["panier"])) {
                $_SESSION["panier"] = array();
        }
        $_SESSION["panier"][] = $_GET["voitureId"];
        $_SESSION["panier"] = array_unique($_SESSION["panier"]);
        $this->renderCatalogueVoitures();
    }

    public function vardPanier(){
        var_dump($_SESSION["panier"]); die();
    }
}