<?php

class User{
    private $res = "";


    public function __construct(){

    }

    public function processLogin(){
        include "./controle/FormValidation.php";
        require ("./modele/LoginClass.php");
        $profil = $this->getLoginPostInfo();

        if (!FormValidation::areInputFilled($profil) || !FormValidation::isEmailRight($profil["email"]))
            $this->res = "Il est indispensable de remplir correctement tous les champs";
        else {
            $user = new LoginClass($profil["email"], $profil["pw"]);
            $cookie = $user->connectUser();
            if($cookie != null){
                $_SESSION["uAuth"] = $cookie;
                $_SESSION["login"] = $profil["email"];
                header("Location: ./index.php");
            }
            else
                $this->res="Mot de passe incorrect";

        }
        unset($profil["pw"]);
        $this->renderLogin();
    }

    private function getLoginPostInfo(){
        $profil = array();
        $profil["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
        $profil["pw"] = isset($_POST["pw"]) ? $_POST["pw"] : "";
        return $profil;
    }

    private function getRegisterPostInfo(){
        $profil = array();
        $profil["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
        $profil["nom"] = isset($_POST["nom"]) ? $_POST["nom"] : "";
        $profil["prenom"] = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
        $profil["pw"] = isset($_POST["pw"]) ? $_POST["pw"] : "";
        $profil["pwConfirm"] = isset($_POST["pwConfirm"]) ? $_POST["pwConfirm"] : "";
        return $profil;
    }

    public function processRegister(){
        include "./controle/FormValidation.php";
        require("./modele/UserDB.php");
        require ("./modele/LoginClass.php");

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
                $user = new LoginClass($profil["email"], $profil["pw"]);
                $cookie = $user->connectUser();
                if($cookie != null) {
                    $_SESSION["uAuth"] = $cookie;
                    $_SESSION["login"] = $profil["email"];
                    header("Location: ./index.php");
                }
            }
            else
                $this->res = "Erreur lors de l'enregistrement";
        }
        $this->renderRegister();

    }

    public function renderLogin(){
        $profil = $this->getLoginPostInfo();
        $res = $this->res;
        require("./vue/login.html");
    }


    public function renderRegister(){
        $profil = $this->getRegisterPostInfo();
        $res = $this->res;
        require("./vue/register.html");
    }

    public function renderProfile(){
        //1633537030
        require("./modele/UserDB.php");
        if( !empty($_SESSION["login"]) && !empty($_SESSION["uAuth"]) &&
            UserDB::doesUserExists($_SESSION["login"]) && UserDB::isAuthOk($_SESSION["login"], $_SESSION["uAuth"]))
            // rediriger vers profil
            header("Location: ./index.php");
        else
            $this->renderLogin();
    }

    public function disconnect(){
        $_SESSION["login"] = $_SESSION["uAuth"]  = null;
        header("Location: ./index.php");

    }


}