<?php

/**
 * Controleur gérant les voitures
 */
class Voiture{
    /**
     * Fonction permettant d'afficher le catalogue de voitures disponibles à la location
     */
    public function renderCatalogueVoitures(){
        require("./modele/VoitureDB.php");
        $voitures = VoitureDB::getVoituresDispo();
        require("./vue/accueil.html");
    }

    /**
     * Fonction permettant de set disponible une voiture
     */
    public function setDispo(){

        if (!isset($_GET["voitureId"]))
            return header("Location: ./index.php?controle=User&action=renderAllLocationsOfUser");

        require_once("./modele/UserDB.php");
        if (!(isset($_SESSION["login"]) && isset($_SESSION["uAuth"]) && UserDB::isAuthOk($_SESSION["login"], $_SESSION["uAuth"]))
            || !UserDB::getGroup($_SESSION["login"])){
            require_once("./controle/User.php");
            return User::redirectUserToLoginAndDisconnect();
        }

        require("./modele/VoitureDB.php");
        VoitureDB::setDispo($_GET["voitureId"]);
        header("Location: index.php?controle=User&action=renderAllLocationsOfUser");
    }

    /**
     * Permet d'afficher la page détails d'une voiture
     */
    public function renderCarDetails(){
        require("./modele/VoitureDB.php");

        $v = VoitureDB::getVoitureFromId($_GET["voitureId"])[0];
        $v["caract"] = json_decode($v["caract"], true);
        require("./vue/detailsCar.html");
    }

    /**
     * Permet d'ajouter au panier une voiture
     */
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

}