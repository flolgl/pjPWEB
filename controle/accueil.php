<?php

class Accueil{

    public function renderAccueil(){
        $voitures = array();
        $voitures[] = array();
        $voitures[0]["photo"] = "208";
        $voitures[0]["type"] = "Peugeot 208";

        $voitures[] = array();
        $voitures[1]["photo"] = "508";
        $voitures[1]["type"] = "Peugeot 508";

        $voitures[] = array();
        $voitures[2]["photo"] = "208";
        $voitures[2]["type"] = "Peugeot 208";

        $voitures[] = array();
        $voitures[3]["photo"] = "208";
        $voitures[3]["type"] = "Peugeot 208";

        $voitures[] = array();
        $voitures[4]["photo"] = "208";
        $voitures[4]["type"] = "Peugeot 208";

        $voitures[] = array();
        $voitures[5]["photo"] = "208";
        $voitures[5]["type"] = "Peugeot 208";

        $voitures[] = array();
        $voitures[6]["photo"] = "508";
        $voitures[6]["type"] = "Peugeot 508";

        require("./vue/accueil.html");
    }
}