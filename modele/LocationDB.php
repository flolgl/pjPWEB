<?php

class LocationDB{

    public static function getAllLocationsOfUser($login){
        require("./modele/connect.php");

        $sql = "SELECT v.*, location.idLoc, DATE_FORMAT(location.tDebut,'%d %m %Y') AS tDebut, location.prixJour, DATE_FORMAT(location.tFin, '%d %m %Y') AS tFin, DATEDIFF(location.tFin, location.tDebut) AS duree FROM voiture AS v, location, user WHERE location.idClient = user.id AND v.id = location.idVoiture AND user.email = :email";
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
        $sql = "SELECT v.type, v.plaque, v.type, l.idLoc, user.nomEntreprise AS nom, l.prixJour, l.prixJour*DATEDIFF(l.tFin, l.tDebut) AS total, DATEDIFF(l.tFin, l.tDebut) as duree FROM location AS l, voiture AS v, user WHERE l.idLoc = :idLoc AND l.idVoiture = v.id AND user.id = l.idClient"; //Trigger s'occupe de mettre en table historique
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

        $sql = "SELECT location.idLoc, DATE_FORMAT(location.tDebut,'%d %m %Y') AS tDebut, location.prixJour, DATE_FORMAT(location.tFin, '%d %m %Y') AS tFin, DATEDIFF(location.tFin, location.tDebut) AS duree, user.nom, user.prenom FROM location, user WHERE location.idVoiture = :carId AND location.idClient = user.id";
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

        $sql = "SELECT DISTINCT cli.nomEntreprise as nom, cli.id FROM user AS cli, location AS l, voiture AS v WHERE v.idLoueur = :idLoueur AND l.idVoiture = v.id AND cli.id = l.idClient";
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


    public static function getAllFactureOfLoueurDB($idLoueur){
        require("./modele/connect.php");
        $sql = "SELECT v.type, v.plaque, v.type, l.idLoc, u.nomEntreprise AS nom, l.prixJour, l.prixJour*DATEDIFF(l.tFin, l.tDebut) AS total, DATEDIFF(l.tFin, l.tDebut) as duree FROM location AS l, voiture AS v, user AS u WHERE v.idLoueur = :idLoueur AND v.id = l.idVoiture AND l.idClient = u.id";

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

    public static function getAllFactureOfLoueurByMonth($loueurId, $month, $year){
        require("./modele/connect.php");

        $sql = "SELECT v.type, v.plaque, v.type, l.idLoc, u.nomEntreprise AS nom, l.prixJour, l.prixJour*DATEDIFF(l.tFin, l.tDebut) AS total, DATEDIFF(l.tFin, l.tDebut) as duree FROM location AS l, voiture AS v, user AS u WHERE v.idLoueur = :idLoueur AND v.id = l.idVoiture AND l.idClient = u.id AND MONTH(l.payDate) = :mois AND YEAR(l.payDate) = :annee";
        $sql2 = "SELECT v.type, v.plaque, v.type, l.idLoc, u.nomEntreprise AS nom, l.prixJour, l.prixJour*DATEDIFF(l.tFin, l.tDebut) AS total, DATEDIFF(l.tFin, l.tDebut) as duree FROM locationhistory AS l, voiture AS v, user AS u WHERE v.idLoueur = :idLoueur AND v.id = l.idVoiture AND l.idClient = u.id AND MONTH(l.payDate) = :mois AND YEAR(l.payDate) = :annee";

        $factures = self::getAllFactureFromMonth($sql, $month, $year, $loueurId);
        $facturesHistory = self::getAllFactureFromMonth($sql2, $month, $year, $loueurId);

        foreach ($facturesHistory as $k=>$v)
            $factures[] = $v;

        return $factures;
    }

    private static function getAllFactureFromMonth($sql, $month, $year, $loueurId){
        require("./modele/connect.php");
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoueur', $loueurId, PDO::PARAM_STR);
            $stmt->bindParam(':mois', $month, PDO::PARAM_STR);
            $stmt->bindParam(':annee', $year, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat= $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $resultat;
    }

    public static function getAllMonth(){
        require("./modele/connect.php");
        $sql="SELECT MONTH(payDate) AS m, YEAR(payDate) as y FROM location UNION SELECT MONTH(payDate) AS m, YEAR(payDate) as y FROM locationhistory ORDER BY `m`, `y` ASC";

        try{
            $stmt = $pdo->prepare($sql);
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