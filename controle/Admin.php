<?php

class Admin
{

    private $res = "";

    public function renderAddCar()
    {
        $res = $this->res;
        $car = $this->getPostInfo();
        require("./vue/ajoutVoitures.html");
    }

    public function addVoiture()
    {
        require("./modele/VoitureDB.php");
        $car = $this->getPostInfo();

        if (!$this->isCarInfoFilled($car)) {
            $this->res = "Il est indispensable de remplir tous les champs";
        } else if (!is_numeric($car["carPrice"]) || !is_numeric($car["carPlaces"]) || !is_numeric($car["carPortes"])) {
            $this->res = "Les champs \"Prix à la journée\", \"Nombre de places\" et \"Nombre de portes\" ne peuvent être composées de nombres seulement";
        } else if (!$this->isPlaquePattern($car["carPlate"])) {
            $this->res = "Le format de la plaque est mauvais";
        } else if (VoitureDB::doesVoitureExists($car["carPlate"])) {
            $this->res = "Voiture déjà existante en base de données";
        } else {
            $tab = $this->getJsonTableFromCar($car);
            if (VoitureDB::insertNewVoiture($car, $tab)) {
                header("Location: ./index.php");
                return;
            } else {
                $this->res = "Erreur lors de l'enregistrement";
            }
        }
        $this->renderAddCar();

    }

    private function getJsonTableFromCar($car){
        $tab = array();
        $tab[] = $car["carClim"];
        $tab[] = $car["carVitesse"];
        $tab[] = $car["carColor"];
        $tab[] = $car["carMoteur"];
        $tab[] = $car["carCateg"];
        $tab[] = $car["carPlaces"];
        $tab[] = $car["carPortes"];
        return json_encode($tab);
    }

    private function getPostInfo(){
        $car = array();
        $car["carClim"] = isset($_POST["carClim"]);
        $car["carVitesse"] = isset($_POST["carVitesse"]);
        $car["carName"] = isset($_POST["carName"]) ? $_POST["carName"] : "";
        $car["photo"] = isset($_POST["photo"]) ? $_POST["photo"] : "dd";
        $car["carEtat"] = isset($_POST["carEtat"]) ? $_POST["carEtat"] : "";
        $car["carPlate"] = isset($_POST["carPlate"]) ? $_POST["carPlate"] : "";
        $car["carPrice"] = isset($_POST["carPrice"]) ? $_POST["carPrice"] : "";
        $car["carColor"] = isset($_POST["carColor"]) ? $_POST["carColor"] : "";
        $car["carMoteur"] = isset($_POST["carMoteur"]) ? $_POST["carMoteur"] : "";
        $car["carCateg"] = isset($_POST["carCateg"]) ? $_POST["carCateg"] : "";
        $car["carPlaces"] = isset($_POST["carPlaces"]) ? $_POST["carPlaces"] : "";
        $car["carPortes"] = isset($_POST["carPortes"]) ? $_POST["carPortes"] : "";
        return $car;
    }

    private function isPlaquePattern($plaque){
        $plaque = strtoupper($plaque);
        $pattern = "[[0-9][0-9]-[A-Z][A-Z][A-Z]-[0-9][0-9]]";
        return preg_match($pattern, $plaque);
    }

    private function isCarInfoFilled($car){
        $indice = 0;
        $res = true;

        foreach($car as $c){
            if($indice < 2)
                break;

            if (empty($c))
                $res = false;
            $indice++;
        }
        return $res;

    }

}