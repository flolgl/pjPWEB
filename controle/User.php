<?php

/**
 * Controleur gérant un User
 */
class User{

    /**
     * @var string Raison d'un retour négatif du form
     */
    private $res = "";

    /**
     * Fonction permettant de process le form de login
     */
    public function processLogin(){
        require_once("./controle/utils/FormValidation.php");
        require_once("./modele/UserDB.php");
        $profil = $this->getLoginPostInfo();

        if (!FormValidation::areInputFilled($profil) || !FormValidation::isEmailRight($profil["email"]))
            $this->res = "Il est indispensable de remplir correctement tous les champs";
        else {
            $user = new UserDB($profil["email"], $profil["pw"]);

            $cookie = $user->connectUser();
            if($cookie != null){
                $_SESSION["uAuth"] = $cookie;
                $_SESSION["login"] = $profil["email"];
                $_SESSION["group"] = UserDB::getGroup($profil["email"]);

                header("Location: ./index.php");
            }
            else
                $this->res="Mail ou mot de passe incorrect";

        }
        unset($profil["pw"]);
        $this->renderLogin();
    }

    /**
     * Fonction permettant d'afficher le catalogue de voitures du site
     */
    private function renderAllCatalogueOfLoueur(){
        require("./modele/VoitureDB.php");
        $stockCar = isset($_GET["stockCar"]) ? $_GET["stockCar"] === "true" : true;
        $rentedCar = isset($_GET["rentedCar"]) ? $_GET["rentedCar"] === "true" : true;

        require("./modele/LocationDB.php");
        //var_dump($stockCar, $rentedCar);
        $choiceEntreprise = "";
        $entreprises = LocationDB::getAllClientsOfLoueur(UserDB::getUserId($_SESSION["login"]));

        if (!(isset($_GET["idE"]) && is_numeric($_GET["idE"])) || !UserDB::doesUserExistsWithId($_GET["idE"]))
            $cars = VoitureDB::getCatalogueOfLoueur($_SESSION["login"], $stockCar, $rentedCar);
        else
            $cars = LocationDB::getAllLocationsOfUser(UserDB::getUserMail($_GET["idE"]));

        $mois = LocationDB::getAllMonth();

        $choiceEntreprise = $this->getEntrepriseName(isset($_GET["idE"]) ? $_GET["idE"] : -1, $entreprises);
        $prixTotal = 0;


        foreach($cars as $key => $value){
            $cars[$key]["caract"] = json_decode($value["caract"], true);
            if ($rentedCar)
                $facturation = LocationDB::getClientAndLocationInfoFromCarId($value["id"]);
                if (!empty($facturation)) {
                    $cars[$key]["facturation"] = $facturation[0];
                    $prixTotal += $facturation[0]["prixJour"] * $facturation[0]["duree"];
            }
        }
        require("./vue/loueurCatalogue.html");
    }

    /**
     * Fonction permettant d'afficher le nom d'un loueur
     * @param $idE string id du loueur
     * @param $tab array tab comportant le nom des entreprises
     * @return string le nom de l'entreprise ou "choisir une entreprise" si nom pas trouvé
     */
    private function getEntrepriseName($idE, $tab){
        foreach($tab as $v)
            if ($v["id"] === $idE)
                return "Entreprise choisie : ".$v["nom"];
        return "Choisir une entreprise";
    }

    /**
     * Permet d'afficher les locations d'un client
     */
    private function renderAllLocationsOfClient(){
        require("./modele/LocationDB.php");
        $cars = LocationDB::getAllLocationsOfUser($_SESSION["login"]);

        foreach($cars as $key => $value)
            $cars[$key]["caract"] = json_decode($value["caract"], true);

        require("./vue/carSelection.html");
    }

    /**
     * Permet de déterminer s'il faut afficher le catalogue d'un client ou d'un loueur
     */
    public function renderAllLocationsOfUser(){
        if (!User::isUserLoggedIn())
            return self::redirectUserToLoginAndDisconnect();

        if (UserDB::getGroup($_SESSION['login']) == 1)
            $this->renderAllCatalogueOfLoueur();
        else
            $this->renderAllLocationsOfClient();


    }

    /**
     * Fonction permettant d'arrêter la location d'une voiture
     */
    public function arreterLocation(){
        if (!User::isUserLoggedIn())
            return self::redirectUserToLoginAndDisconnect();

        if (!isset($_GET["locationId"]) || !is_numeric($_GET["locationId"]))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");

        require("./modele/LocationDB.php");
        LocationDB::removeCarFromLocation($_GET["locationId"]);
        header("Location: ./index.php?controle=User&action=renderAllLocationsOfUser");
    }

    /**
     * Fonction permettant de déterminer si visiteur est logged in
     * @return bool true si user est connecté, false dans le cas contraire
     */
    public static function isUserLoggedIn(){
        require("./modele/UserDB.php");
        return isset($_SESSION["login"]) && isset($_SESSION["uAuth"]) && UserDB::isAuthOk($_SESSION["login"], $_SESSION["uAuth"]);
    }

    /**
     * Fonction permettant de déconnecter un user
     */
    public static function redirectUserToLoginAndDisconnect(){
        $_SESSION["login"] = null; $_SESSION["uAuth"] = null;
        header("Location: index.php?controle=User&action=renderProfile");
    }

    /**
     * Permet de récupérer les infos dans POST du form de login
     * @return array infos dans POST du form de login
     */
    private function getLoginPostInfo(){
        $profil = array();
        $profil["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
        $profil["pw"] = isset($_POST["pw"]) ? $_POST["pw"] : "";
        return $profil;
    }

    /**
     * Permet de récupérer les infos dans POST du form de register
     * @return array infos dans POST du form de register
     */
    private function getRegisterPostInfo(){
        $profil = array();
        $profil["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
        $profil["nom"] = isset($_POST["nom"]) ? $_POST["nom"] : "";
        $profil["prenom"] = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
        $profil["pw"] = isset($_POST["pw"]) ? $_POST["pw"] : "";
        $profil["pwConfirm"] = isset($_POST["pwConfirm"]) ? $_POST["pwConfirm"] : "";
        $profil["nomEntreprise"] = isset($_POST["nomEntreprise"]) ? $_POST["nomEntreprise"] : "";
        $profil["codePostal"] = isset($_POST["codePostal"]) ? $_POST["codePostal"] : "";

        return $profil;
    }

    /**
     * Fonction permettant de process l'enregistrement d'un user
     */
    public function processRegister(){
        include "./controle/utils/FormValidation.php";
        require_once("./modele/UserDB.php");

        $profil = $this->getRegisterPostInfo();
        //var_dump($profil);

        if (!FormValidation::areInputFilled($profil) || !FormValidation::isEmailRight($profil["email"]))
            $this->res = "Il est indispensable de remplir correctement tous les champs";
        else if ($profil["pw"] != $profil["pwConfirm"])
            $this->res = "Les mots de passe ne sont pas identiques";
        else if (UserDB::doesUserExists($profil["email"]))
            $this->res = "Utilisateur déjà existant";
        else {
            if (UserDB::insertUserIntoBdd($profil)){
                $user = new UserDB($profil["email"], $profil["pw"]);
                $cookie = $user->connectUser();
                if($cookie != null) {
                    $_SESSION["uAuth"] = $cookie;
                    $_SESSION["login"] = $profil["email"];
                    $_SESSION["group"] = 0;
                    header("Location: ./index.php");
                }
            }
            else
                $this->res = "Erreur lors de l'enregistrement";
        }
        $this->renderRegister();

    }

    /**
     * Fonction permettant d'afficher le form de login
     */
    public function renderLogin(){
        $profil = $this->getLoginPostInfo();
        $res = $this->res;
        require("./vue/login.html");
    }

    /**
     * Fonction permettant d'afficher le form de register
     */
    public function renderRegister(){
        $profil = $this->getRegisterPostInfo();
        $res = $this->res;
        require("./vue/register.html");
    }

    /**
     * Fonction permettant d'afficher le form de login ou l'accueil en fonction du statut de connexion du visiteur
     */
    public function renderProfile(){
        require("./modele/UserDB.php");
        if( !empty($_SESSION["login"]) && !empty($_SESSION["uAuth"]) &&
            UserDB::doesUserExists($_SESSION["login"]) && UserDB::isAuthOk($_SESSION["login"], $_SESSION["uAuth"]))
            // TODO : rediriger vers profil
            header("Location: ./index.php");
        else
            $this->renderLogin();
    }

    /**
     * Fonction permettant de déconnecter un user
     */
    public function disconnect(){
        $_SESSION["login"] = $_SESSION["uAuth"]  = null;
        header("Location: ./index.php");

    }

    /**
     * Fonction permettant d'afficher les factures d'un client
     */
    public function renderFacturation(){
        if (!User::isUserLoggedIn())
            return self::redirectUserToLoginAndDisconnect();

        if (!isset($_GET["locationId"]) || !is_numeric($_GET["locationId"]))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");

        require("./modele/LocationDB.php");
        $locationInfo = LocationDB::getFacturation($_GET["locationId"]);

        if (empty($locationInfo))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");

        $total = 0;
        $loueur = false;
        foreach ($locationInfo as $item)
            $total += $item["total"];

        require("./vue/factureLocation.html");
    }

    /**
     * Fonction permettant d'afficher les factures d'un loueur

     */
    public function getAllFacturesOfLoueur(){
        if (!User::isUserLoggedIn())
            return self::redirectUserToLoginAndDisconnect();

        require_once("./modele/LocationDB.php");
        require_once("./modele/UserDB.php");
        $locationInfo = LocationDB::getAllFactureOfLoueurDB(UserDB::getUserId($_SESSION["login"]));

        if (empty($locationInfo))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");


        $total = 0;
        foreach ($locationInfo as $item)
            $total += $item["total"];
        $loueur = true;

        require("./vue/factureLocation.html");
    }

    /**
     * Fonction permettant d'afficher les factures d'un mois d'un loueur
     */
    public function getFactureOfMonth(){
        if (!User::isUserLoggedIn())
            return self::redirectUserToLoginAndDisconnect();
        if (!isset($_GET["month"]) || !isset($_GET["year"]) || !is_numeric($_GET["month"]) || !is_numeric($_GET["year"]))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");

        require_once("./modele/LocationDB.php");
        require_once("./modele/UserDB.php");
        $locationInfo = LocationDB::getAllFactureOfLoueurByMonth(UserDB::getUserId($_SESSION["login"]), $_GET["month"], $_GET["year"]);

        if (empty($locationInfo))
            return header("Location: index.php?controle=User&action=renderAllLocationsOfUser");
        $total = 0;
        foreach ($locationInfo as $item)
            $total += $item["total"];
        $loueur = true;

        require("./vue/factureLocation.html");
    }



}