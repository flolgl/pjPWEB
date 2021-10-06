<?php

class Cart
{
    public function renderCart(){
        $articles = array();
        $articles["nb"] = 0;
        $articles["totalPrix"] = 0;
        $articles["products"] = array();
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";
        $articles["products"][] = "dddd";

        require("./vue/cart.html");

    }
}