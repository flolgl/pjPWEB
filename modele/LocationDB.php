<?php

class LocationDB{

    public static function getAllLocationsOfUser($login){
        require("./modele/connect.php");

        $sql = "SELECT v.*, location.idLoc, DATE_FORMAT(location.tDebut,'%d %m %Y') AS tDebut, location.prixJour, DATE_FORMAT(location.tFin, '%d %m %Y') AS tFin, DATEDIFF(location.tFin, location.tDebut) AS duree FROM voiture AS v, location, client WHERE location.idClient = client.id AND v.id = location.idVoiture AND client.email = :email";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $login, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    public static function updateLocationFinDate($idLoc){
        require("./modele/connect.php");

        $sql = "UPDATE location SET location.tFin = NOW() WHERE location.idLoc = :idLoc";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoc', $idLoc, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $result = ($stmt->fetchAll(PDO::FETCH_ASSOC)); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

    public static function removeCarFromLocation($idLoc){
        require("./modele/connect.php");

        self::updateLocationFinDate($idLoc);

        $sql = "DELETE FROM location WHERE location.idLoc = :idLoc"; //Trigger s'occupe de mettre en table historique
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoc', $idLoc, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                return empty($stmt->fetchAll(PDO::FETCH_ASSOC)); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

    public static function getFacturation($idLoc){
        require("./modele/connect.php");
        $sql = "SELECT v.type, v.plaque, v.type, l.idLoc, client.nom, client.prenom, l.prixJour, l.prixJour*DATEDIFF(l.tFin, l.tDebut) AS total FROM location AS l, voiture AS v, client WHERE l.idLoc = :idLoc AND l.idVoiture = v.id AND client.id = l.idClient"; //Trigger s'occupe de mettre en table historique
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoc', $idLoc, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                return $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

    public static function getClientAndLocationInfoFromCarId($carId){
        require("./modele/connect.php");

        $sql = "SELECT location.idLoc, DATE_FORMAT(location.tDebut,'%d %m %Y') AS tDebut, location.prixJour, DATE_FORMAT(location.tFin, '%d %m %Y') AS tFin, DATEDIFF(location.tFin, location.tDebut) AS duree, client.nom, client.prenom FROM location, client WHERE location.idVoiture = :carId AND location.idClient = client.id";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':carId', $carId, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    public static function getAllClientsOfLoueur($idLoueur){
        require("./modele/connect.php");

        $sql = "SELECT DISTINCT cli.nom, cli.id FROM client AS cli, location AS l, voiture AS v WHERE v.idLoueur = :idLoueur AND l.idVoiture = v.id AND cli.id = l.idClient";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoueur', $idLoueur, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

}