<?php

class Cart
{
    public function renderCart(){
        $articles = array();
        $articles["nb"] = 0;
        $articles["totalPrix"] = 0;
        $articles["products"] = array();
        $articles["products"][] = array();


        $articles["products"][0]["prix"] = 5;
        $articles["products"][0]["brand"] = "Peugeot";
        $articles["products"][0]["model"] = "508";
        $articles["products"][0]["nomCateg"] = "Voiture prestige";
        $articles["products"][0]["color"] = "Bleue";
        $articles["products"][0]["nbPlaces"] = 5;


        $articles["products"][1]["prix"] = 500;
        $articles["products"][1]["brand"] = "Peugeot";
        $articles["products"][1]["model"] = "208";
        $articles["products"][1]["nomCateg"] = "Voiture normale";
        $articles["products"][1]["color"] = "Bleue";
        $articles["products"][1]["nbPlaces"] = 3;


        $articles["products"][2]["prix"] = 5;
        $articles["products"][2]["brand"] = "Peugeot";
        $articles["products"][2]["model"] = "208";
        $articles["products"][2]["nomCateg"] = "Voiture prestige";
        $articles["products"][2]["color"] = "Bleue";
        $articles["products"][2]["nbPlaces"] = 3;


        $articles["products"][3]["prix"] = 5;
        $articles["products"][3]["brand"] = "Peugeot";
        $articles["products"][3]["model"] = "508";
        $articles["products"][3]["nomCateg"] = "Voiture prestige";
        $articles["products"][3]["color"] = "Bleue";
        $articles["products"][3]["nbPlaces"] = 3;


        $articles["products"][4]["prix"] = 5;
        $articles["products"][4]["brand"] = "Peugeot";
        $articles["products"][4]["model"] = "508";
        $articles["products"][4]["nomCateg"] = "Voiture prestige";
        $articles["products"][4]["color"] = "Bleue";
        $articles["products"][4]["nbPlaces"] = 3;


        $articles["products"][5]["prix"] = 5;
        $articles["products"][5]["brand"] = "Peugeot";
        $articles["products"][5]["model"] = "508";
        $articles["products"][5]["nomCateg"] = "Voiture prestige";
        $articles["products"][5]["color"] = "Bleue";
        $articles["products"][5]["nbPlaces"] = 3;




        require("./vue/cart.html");

    }
}