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
        $i = 0;
        foreach($_SESSION["panier"] as $item){
            if (!isset($item))
                continue;
            $car = VoitureDB::getVoitureFromId($item);
            $articles[$i]["model"] = $car[0]["type"];
            $articles[$i]["id"] = $car[0]["id"];
            $articles[$i]["prix"] = $car[0]["prix"];
            $articles[$i]["photo"] = $car[0]["photo"];
            $articles[$i]["caract"] = json_decode($car[0]["caract"], true);
            $prixTotal += $car[0]["prix"];
            $i++;
        }


        require("./vue/cart.html");
    }

    public function removeCarFromPanier(){

        foreach($_SESSION["panier"] as $k => $v){
            if ($_GET["voitureId"] == $v) {
                unset($_SESSION["panier"][$k]);
                break;
            }

        }
        header("Location: ./index.php?controle=Cart&action=renderCart");

    }
}