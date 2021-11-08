<?php

class Loueur
{

    private $res = "";

    /**
     * Permet l'affichage de la vue du form d'ajout d'une voiture
     */
    public function renderAddCar(){
        require("./controle/User.php");
        if (!Loueur::isLoueurAndLoggedIn())
            return User::redirectUserToLoginAndDisconnect();
        $res = $this->res;
        $car = $this->getPostInfo();
        $carNames = $this->getVoituresNamesAndPhotoNamesMap();
        require("./vue/ajoutVoitures.html");
    }

    /**
     * @return bool true si user est loueur et connecté, false dans le cas contraire
     */
    private function isLoueurAndLoggedIn(){
        require_once("./modele/UserDB.php");
        return isset($_SESSION["login"]) && isset($_SESSION["uAuth"]) && UserDB::isAuthOk($_SESSION["login"], $_SESSION["uAuth"])
            && UserDB::getGroup($_SESSION["login"]) == 1;
    }

    /**
     * @return array Tab composé de clés => nom de la voiture et de valeurs => nom de la photo. Ajouter .webp !
     */
    private function getVoituresNamesAndPhotoNamesMap(){
        require_once("./modele/VoitureDB.php");
        $carNames = VoitureDB::getAllVoituresNamesAndPhotoName();
        $map = array();
        foreach ($carNames as $car)
            $map[$car["type"]] = $car["photo"];

        return $map;

    }

    /**
     * Fonction permettant d'ajouter une voiture en DB
     */
    public function addVoiture(){
        if (!Loueur::isLoueurAndLoggedIn()){
            require("./controle/User.php");
            return User::redirectUserToLoginAndDisconnect();
        }

        require_once("./modele/VoitureDB.php");
        $car = $this->getPostInfo();
        if (!$this->isCarInfoFilled($car)) {
            $this->res = "Il est indispensable de remplir tous les champs";
        } else if (!is_numeric($car["carPrice"]) || !is_numeric($car["carPlaces"]) || !is_numeric($car["carPortes"])) {
            $this->res = "Les champs \"Prix à la journée\", \"Nombre de places\" et \"Nombre de portes\" ne peuvent être composées de nombres seulement";
        } else if (!$this->isPlaquePattern($car["carPlate"])) {
            $this->res = "Le format de la plaque est mauvais";
        } else if (VoitureDB::doesVoitureExists($car["carPlate"])) {
            $this->res = "Voiture déjà existante en base de données";
        } else if (!$this->isImageValid($car["carImage"], $car["carImageUpload"])){
            $this->res = "L'image de la voiture n'est pas au bon format";
        } else {
            $tab = $this->getJsonTableFromCar($car);
            require_once("./modele/UserDB.php");
            if (VoitureDB::insertNewVoiture($car, $tab, UserDB::getUserId($_SESSION["login"]))) {
                $this->moveImageIfNecessary($car["carImage"], $car["carImageUpload"]);
                header("Location: ./index.php");
                return;
            } else {
                $this->res = "Erreur lors de l'enregistrement";
            }
        }
        $this->renderAddCar();

    }

    /**
     * Détermine si nécessité d'upload l'image
     * @param $image string l'input du menu déroulant
     * @param $imageUpload array l'image de l'input file
     */
    private function moveImageIfNecessary($image, $imageUpload){
        if($image !== "noImage" && !empty($image))
            return;
        else
            move_uploaded_file($imageUpload["tmp_name"], "./img/".$imageUpload['name']);
    }

    /**
     * @param $image string l'input du menu déroulant
     * @param $imageUpload array l'image de l'input file
     * @return bool true si l'image est valide
     */
    private function isImageValid($image, $imageUpload){
        //var_dump($image, $imageUpload);
        if(($image === "noImage" || empty($image)) && empty($imageUpload))
            return false;

        if($image !== "noImage" && !empty($image)) {
            require_once("./modele/VoitureDB.php");
            return VoitureDB::doesImageExists($image);
        }
        else{
            $tabExtension = explode('.', $imageUpload["name"]);
            $extension = strtolower(end($tabExtension));
            $extensions = ["webp", "jpeg", "jpg", "png"];
            return in_array($extension, $extensions);
        }


    }

    /**
     * @param $car array tab à JSONifié
     * @return false|string le tab JSONifié
     */
    private function getJsonTableFromCar($car){
        $tab = array();
        $tab["climatisation"] = $car["carClim"];
        $tab["vitesse"] = $car["carVitesse"];
        $tab["color"] = $car["carColor"];
        $tab["moteur"] = $car["carMoteur"];
        $tab["categ"] = $car["carCateg"];
        $tab["places"] = $car["carPlaces"];
        $tab["portes"] = $car["carPortes"];
        return json_encode($tab);
    }

    /**
     * @return array Les inputs du form
     */
    private function getPostInfo(){
        $car = array();
        $car["carClim"] = isset($_POST["carClim"]);
        $car["carVitesse"] = isset($_POST["carVitesse"]);
        $car["carName"] = isset($_POST["carName"]) ? $_POST["carName"] : "";
        $car["carEtat"] = isset($_POST["carEtat"]) ? $_POST["carEtat"] : "";
        $car["carPlate"] = isset($_POST["carPlate"]) ? $_POST["carPlate"] : "";
        $car["carPrice"] = isset($_POST["carPrice"]) ? $_POST["carPrice"] : "";
        $car["carColor"] = isset($_POST["carColor"]) ? $_POST["carColor"] : "";
        $car["carMoteur"] = isset($_POST["carMoteur"]) ? $_POST["carMoteur"] : "";
        $car["carCateg"] = isset($_POST["carCateg"]) ? $_POST["carCateg"] : "";
        $car["carPlaces"] = isset($_POST["carPlaces"]) ? $_POST["carPlaces"] : "";
        $car["carPortes"] = isset($_POST["carPortes"]) ? $_POST["carPortes"] : "";

        $car["carImage"] = isset($_POST["carImage"]) ? $_POST["carImage"] : "";
        $car["carImageUpload"] = isset($_FILES["carImageUpload"]) ? $_FILES["carImageUpload"] : null;

        return $car;
    }

    /**
     * @param $plaque string L'input mis dans le champs plaque
     * @return false|int true si la plaque est au bon format
     */
    private function isPlaquePattern($plaque){
        $plaque = strtoupper($plaque);
        $pattern = "[[0-9][0-9]-[A-Z][A-Z][A-Z]-[0-9][0-9]]";
        return preg_match($pattern, $plaque);
    }

    /**
     * @param $car array tab des inputs
     * @return bool true si tous les champs sont remplis
     */
    private function isCarInfoFilled($car){
        $res = true;


        foreach($car as $key => $value){
            if($key === "carClim" || $key === "carVitesse" || $key === "carImage" || $key==="carImageUpload")
                continue;

            if (empty($value))
                $res = false;

        }

        return $res;

    }

}