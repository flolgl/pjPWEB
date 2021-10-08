<?php

class Cart
{
    public function renderCart(){

        $articles = array();
        $prixTotal = 0;

        if (!isset($_SESSION["panier"])){
            require("./vue/cart.html");
            return;
        }

        require("./modele/VoitureDB.php");
        for ($i = 0; $i<count($_SESSION["panier"]); $i++){
            if (!isset($_SESSION["panier"][$i]))
                continue;
            $car = VoitureDB::getVoitureFromId($_SESSION["panier"][$i]);
            $articles[$i]["model"] = $car[0]["type"];
            $articles[$i]["id"] = $car[0]["id"];
            $articles[$i]["prix"] = $car[0]["prix"];
            $articles[$i]["photo"] = $car[0]["photo"];
            $articles[$i]["caract"] = json_decode($car[0]["caract"], true);
            $prixTotal = $car[0]["prix"];
        }


        require("./vue/cart.html");

    }

    public function removeCarFromPanier(){

        for ($i = 0; $i<count($_SESSION["panier"]); $i++){
            if ($_GET["voitureId"] == $_SESSION["panier"][$i]) {
                $_SESSION["panier"][$i] = null;
                break;
            }

        }
        header("Location: ./index.php?controle=Cart&action=renderCart");

    }
}