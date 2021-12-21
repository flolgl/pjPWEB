<?php

class VoitureDB{

    /**
     * Fonction permettant de set une voiture en mode contraire du mode actuel
     * @param $voitureId string l'id de la voiture
     * @return bool true si tout c'est bien passé, false si la voiture n'existe pas
     */
    public static function setDispo($voitureId){

        $dispo = self::getDispoState($voitureId);

        if (!isset($dispo))
            return false;

        $dispo[0]["etatL"] === "disponible" ?  $etat = "revision" : $etat = "disponible";
        require("./modele/connect.php");

        $sql = "UPDATE voiture SET etatL = :etat WHERE id = :id";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $voitureId, PDO::PARAM_STR);
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");

        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return true;
    }

    /**
     * Fonction permettant de récupérer l'état d'une voiture
     * @param $voitureId string l'id de la voiture
     * @return array Tab d'une ligne comportant l'état de la voiture, tab vide si voiture n'existe pas
     */
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

    /**
     * Fonction permettant de récupérer les voitures disponibles
     * @return array les voitures disponibles
     */
    public static function getVoituresDispo(){
        require("./modele/connect.php");

        $sql = "SELECT voiture.id, voiture.type, voiture.prix, voiture.photo, u.nomEntreprise FROM voiture, user AS u WHERE u.id = voiture.idLoueur AND etatL='disponible' AND voiture.id NOT IN (SELECT location.idVoiture FROM location)";
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

    /**
     * Fonction permettant de récupérer les noms et pic names de voitures
     * @return array Tab comportant noms et pic names de voitures
     */
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

    /**
     * Fonction permettant de récuper les infos d'une voiture depuis un id de voiture
     * @param $id string l'id de la voiture
     * @return array Tab comportant les infos d'une voiture
     */
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

    /**
     * Fonction permettant de savoir si une voiture existe à partir d'une plaque
     * @param $plaque string la plaque
     * @return bool true si la voiture existe, false dans le cas contraire
     */
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

    /**
     * Fonction permettant d'ajouter une nouvelle voiture
     * @param $car array les infos de la voiture
     * @param $json string tab JSON stringifié comportant les caractéristiques d'une voiture
     * @param $idL string l'id du loueur de la voiture
     * @return bool true si tout s'est bien passé
     */
    public static function insertNewVoiture($car, $json, $idL){
        require("./modele/connect.php");

        //var_dump($car, $json, $idL);

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

    /**
     * Fonction permettant de récup le catalogue d'un loueur
     * @param $email string l'email d'un loueur
     * @param $stockCar bool afficher les voitures en stock
     * @param $rentedCar bool afficher les voitures louées
     * @return array le catalogue du loueur
     */
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

    /**
     * Fonction permettant de déterminer le choix d'un user
     * @param $stockCar bool afficher les voitures en stock
     * @param $rentedCar bool afficher les voitures louées
     * @return string la requête sql
     */
    private static function getUserChoiceOfShowing($stockCar, $rentedCar){
        if ($stockCar && $rentedCar) // montrer toutes les voitures
            return "SELECT voiture.* FROM voiture, user WHERE user.email = :email AND voiture.idLoueur = user.id";

        if (!$stockCar && $rentedCar) // montrer seulement les voitures louées
            return "SELECT voiture.* FROM voiture, location, user WHERE location.idVoiture = voiture.id AND voiture.idLoueur = user.id AND user.email = :email";

        if ($stockCar && !$rentedCar) // montrer seulement les voitures en stock
            return "SELECT voiture.* FROM voiture, user WHERE user.email = :email AND voiture.idLoueur = user.id AND voiture.id NOT IN(SELECT location.idVoiture FROM location)";

        return ""; // Ne rien montrer


    }

    /**
     * Fonction permettant de déterminer si une image existe
     * @param $image string le nom de l'image
     * @return bool true si l'image existe
     */
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

    /**
     * Getter du prix d'une voiture
     * @param $vId int l'id du véhicule
     * @return float le prix
     */
    public static function getPrix($vId){
        require("./modele/connect.php");

        $sql = "SELECT prix FROM voiture WHERE id=:id";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $vId, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat[0]["prix"];
    }


    /**
     * Fonction permettant de savoir si une voiture est dispo
     * @param $vId string l'id de la voiture
     * @return bool true si la voiture est dispo
     */
    public static function isVoitureDispo($vId){
        require("./modele/connect.php");

        $sql = "SELECT etatL FROM voiture WHERE id=:id";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $vId, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return !empty($resultat) && $resultat[0]["etatL"] === "disponible";
    }


}