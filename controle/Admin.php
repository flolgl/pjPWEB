<?php

/**
 * Controleur gérant l'aspect Admin du site
 */
class Admin{

    /**
     * @var string Raison d'un retour négatif du form
     */
    private $res = "";

    /**
     * Fonction appellant la vue
     */
    public function renderAdmin(){
        $profil = $this->getPostInfo();
        $res = $this->res;
        require("./vue/adminAddLoueur.html");
    }

    /**
     * Fonction permettant de process le form d'ajout d'un loueur
     */
    public function processAddLoueur(){
        $profil = $this->getPostInfo();
        require("./controle/utils/FormValidation.php");
        if (FormValidation::areInputFilled($profil))
            $this->res = "Il est indispensable de tout remplir";
        else if (FormValidation::isEmailRight($profil["email"]) || FormValidation::isEmailRight($profil["mailEntreprise"]))
            $this->res = "Il est indispensable de remplir correctement les champs";

        require("./modele/UserDB.php");
        if(!self::adminCanConnect($profil["email"], $profil["pw"]))
            $this->res= "Mail administrateur ou mot de passe administrateur incorrect";
        else if(UserDB::doesUserExists($profil["mailEntreprise"]))
            $this->res = "Utilisateur déjà existant";
        else {
            UserDB::insertUserIntoBdd($profil, 1);
            return header("Location: ./index.php");
        }
        $this->renderAdmin();
    }

    /**
     * Fonction de déterminer si un admin peut se connecter
     * @param $login string le login de l'admin
     * @param $pw string le password de l'admin
     * @return bool true si l'admin peut se connecter, false dans le cas contraire
     */
    private static function adminCanConnect($login, $pw){
        require("./modele/AdminDB.php");

        return !empty($login) && !empty($pw) && AdminDB::adminCanConnect($login, $pw);
    }

    /**
     * Fonction permettant de récuper l'ensemble des infos nécessaires pour afficher et traiter le form d'ajout d'un loueur
     * @return array le tableau des inputs dans POST
     */
    private function getPostInfo(){
        $tab = array();

        $tab["email"] = isset($_POST["email"]) ? $_POST["email"] : "";
        $tab["pw"] = isset($_POST["pw"]) ? $_POST["pw"] : "";
        $tab["nom"] = isset($_POST["nom"]) ? $_POST["nom"] : "";
        $tab["prenom"] = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
        $tab["nomEntreprise"] = isset($_POST["nomEntreprise"]) ? $_POST["nomEntreprise"] : "";
        $tab["mailEntreprise"] = isset($_POST["mailEntreprise"]) ? $_POST["mailEntreprise"] : "";
        $tab["passwordEntreprise"] = isset($_POST["passwordEntreprise"]) ? $_POST["passwordEntreprise"] : "";
        $tab["codePostal"] = isset($_POST["codePostal"]) ? $_POST["codePostal"] : "";
        return $tab;
    }

}