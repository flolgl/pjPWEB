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
        require("./modele/LocationDB.php");
        $i = 0;
        foreach($_SESSION["panier"] as $key => $item){
            if (!isset($item))
                continue;
            if (LocationDB::isVoitureLouee($item) || !VoitureDB::getDispoState($item)[0] === "disponible") {
                $_SESSION["panier"][$key] = null;
                continue;
            }

            $car = VoitureDB::getVoitureFromId($item);
            $articles[$i]["type"] = $car[0]["type"];
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

    public function louerVoitures(){
        if (empty($_POST["dateDebut"]) || empty($_POST["dateFin"]))
            return header("Location: ./index.php?controle=Cart&action=renderCart");
        if (empty($_SESSION["panier"]))
            return header("Location: ./index.php?controle=Cart&action=renderCart");
        require_once("./controle/User.php");
        if (!User::isUserLoggedIn())
            return User::redirectUserToLoginAndDisconnect();
        require_once("./modele/UserDB.php");
        if (UserDB::getGroup($_SESSION["login"]) != 0)
            return header("Location: ./index.php");

        require_once("./modele/LocationDB.php");
        $login =UserDB::getUserId($_SESSION["login"]);

        foreach($_SESSION["panier"] as $key => $value)
            LocationDB::louerVoiture($value, $login, $_POST["dateDebut"], $_POST["dateFin"], date('d-m-Y'));

        header("Location: ./index.php");
    }

}