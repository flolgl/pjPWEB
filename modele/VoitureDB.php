<?php

class VoitureDB{

    public static function getVoituresDispo(){
        require("./modele/connect.php");

        $sql = "SELECT * FROM voiture WHERE etatL='disponible'";
        try{
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return $resultat;
    }

    public static function getAllVoituresNamesAndPhotoName(){
        require("./modele/connect.php");

        $sql = "SELECT DISTINCT type, photo FROM voiture";
        try{
            $stmt = $pdo->prepare($sql);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return $resultat;
    }

    public static function getVoitureFromId($id){
        require("./modele/connect.php");

        $sql = "SELECT * FROM voiture WHERE id=:id";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return $resultat;
    }

    public static function doesVoitureExists($plaque){

        require("./modele/connect.php");

        $sql = "SELECT * FROM voiture WHERE plaque=:plaque";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':plaque', $plaque, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return !empty($resultat);
    }

    public static function insertNewVoiture($car, $json){
        require("./modele/connect.php");


        $sql = "INSERT INTO voiture (type, caract, photo, plaque, etatL, prix) VALUES (:carName, :caract, :photo, :plaque, :etatL, :prix)";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':carName', $car["carName"], PDO::PARAM_STR);
            $stmt->bindParam(':caract', $json, PDO::PARAM_STR);
            $stmt->bindParam(':photo', $car["carImage"], PDO::PARAM_STR);
            $stmt->bindParam(':plaque', $car["carPlate"], PDO::PARAM_STR);
            $stmt->bindParam(':etatL', $car["carEtat"], PDO::PARAM_STR);
            $stmt->bindParam(':prix', $car["carPrice"], PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return true;
    }

}