<?php

class VoitureDB{

    public static function setDispo($voitureId){

        $dispo = self::getDispoState($voitureId);

        if (!isset($dispo))
            return;

        if ($dispo[0]["etatL"] === "disponible")
            $etat = "revision";
        else
            $etat = "disponible";

        require("./modele/connect.php");

        $sql = "UPDATE voiture SET etatL = :etat WHERE id = :id";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $voitureId, PDO::PARAM_STR);
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    public static function getDispoState($voitureId){
        require("./modele/connect.php");

        $sql="SELECT etatL FROM voiture WHERE id=:id";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $voitureId, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    public static function getVoituresDispo(){
        require("./modele/connect.php");

        $sql = "SELECT voiture.id, voiture.type, voiture.prix, voiture.photo, u.nomEntreprise FROM voiture, user AS u WHERE u.id = voiture.idLoueur AND etatL='disponible'";
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

    public static function insertNewVoiture($car, $json, $idL){
        require("./modele/connect.php");

        var_dump($car, $json, $idL);

        $sql = "INSERT INTO voiture (type, caract, photo, plaque, etatL, prix, idLoueur) VALUES (:carName, :caract, :photo, :plaque, :etatL, :prix, :idL)";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':carName', $car["carName"], PDO::PARAM_STR);
            $stmt->bindParam(':caract', $json, PDO::PARAM_STR);
            if ($car["carImage"] !== "noImage" && $car["carImage"])
                $stmt->bindParam(':photo', $car["carImage"], PDO::PARAM_STR);
            else
                $stmt->bindParam(':photo', $car["carImageUpload"]["name"], PDO::PARAM_STR);

            $stmt->bindParam(':plaque', $car["carPlate"], PDO::PARAM_STR);
            $stmt->bindParam(':etatL', $car["carEtat"], PDO::PARAM_STR);
            $stmt->bindParam(':prix', $car["carPrice"], PDO::PARAM_STR);
            $stmt->bindParam(':idL', $idL, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return true;
    }

    public static function getCatalogueOfLoueur($email, $stockCar, $rentedCar){
        require("./modele/connect.php");

        $sql = VoitureDB::getUserChoiceOfShowing($stockCar, $rentedCar);
        if (empty($sql))
            return array();

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    private static function getUserChoiceOfShowing($stockCar, $rentedCar){
        if ($stockCar && $rentedCar) // montrer toutes les voitures
            return "SELECT voiture.* FROM voiture, user WHERE user.email = :email AND voiture.idLoueur = user.id";

        if (!$stockCar && $rentedCar) // montrer seulement les voitures louées
            return "SELECT voiture.* FROM voiture, location, user WHERE location.idVoiture = voiture.id AND voiture.idLoueur = user.id AND user.email = :email";

        if ($stockCar && !$rentedCar) // montrer seulement les voitures en stock
            return "SELECT voiture.* FROM voiture, user WHERE user.email = :email AND voiture.idLoueur = user.id AND voiture.id NOT IN(SELECT location.idVoiture FROM location)";

        return ""; // Ne rien montrer


    }

    public static function doesImageExists($image){
        require("./modele/connect.php");

        $sql = "SELECT photo FROM voiture WHERE photo=:image";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return !empty($resultat);
    }
}