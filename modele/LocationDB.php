<?php

class LocationDB{

    /**
     * Fonction permettant de récupérer toutes les locations d'un user
     * @param $login L'adresse mail du user
     * @return array L'ensemble des locations d'un user
     */
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

    /**
     * Fonction permettant d'update la fin de location d'une voiture
     * @param $idLoc string L'id de la location à update
     */
    public static function updateLocationFinDate($idLoc){
        require("./modele/connect.php");

        $sql = "UPDATE location SET location.tFin = NOW() WHERE location.idLoc = :idLoc";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':idLoc', $idLoc, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL ICI\n");
            else
                $result = ($stmt->fetchAll(PDO::FETCH_ASSOC)); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

    /**
     * Permet de retirer une voiture du statut en location
     * @param $idLoc string L'id de la voiture pour laquelle on met fin à la location
     */
    public static function removeCarFromLocation($idLoc){
        require("./modele/connect.php");

        var_dump($idLoc);
        self::updateLocationFinDate($idLoc);

        $sql = "DELETE FROM `location` WHERE `location`.`idLoc` = :idLoc"; //Trigger s'occupe de mettre en table historique
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":idLoc", $idLoc, PDO::PARAM_STR);

            var_dump($stmt->execute());
            if ($stmt->execute())
                echo 'Suppression done';
            else
                die("error");
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

    /**
     * Fonction permettant de récupérer la facturation d'une location
     * @param $idLoc string l'id de la location
     * @return array Array d'une ligne avec la facturation de la location ou array vide si la location n'existe pas
     */
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

    /**
     * Fonction permettant de récupérere le client id et l'id de location associés à une voiture
     * @param $carId l'id de la voiture
     * @return array un table d'une ligne avec les infos de la location et du client ou un tableau vide si rien n'a été trouvé
     */
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

    /**
     * Fonction permettant de récupérer les client d'un loueur
     * @param $idLoueur string l'id du loueur
     * @return array la liste des clients
     */
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


    /**
     * Permet de récupérer toutes les factures clients d'un loueur
     * @param $idLoueur string l'id du loueur
     * @return array Liste contenant les factures clients d'un loueur
     */
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

    /**
     * Permet de récupérer toutes les factures clients d'un loueur sur une date
     * @param $loueurId string l'id du loueur
     * @param $month string le mois pour lequel on veut les factures
     * @param $year string l'année associée au mois
     * @return array Liste contenant les factures clients d'un loueur
     */
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

    /**
     * Permet de récupérer toutes les factures clients d'un loueur sur un mois
     * @param $sql string la string sql
     * @param $month string le mois pour lequel on veut les factures
     * @param $year string l'année associée au mois
     * @param $loueurId string l'id du loueur
     * @return array Liste contenant les factures clients d'un loueur
     */
    //TODO : refactor le param sql (avec un UNION)
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

    /*
     * Fonction permettant de récupérer les mois pour lesquels un loueur à eu des factures de clients
     */
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


    /**
     * Fonction permettant de déterminer qu'une voiture est en état de location
     * @param $vId string l'id de la voiture
     * @return bool true si la voiture est louée, false dans le cas contraire
     */
    public static function isVoitureLouee($vId){
        require("./modele/connect.php");

        $sql = "SELECT idLoc FROM location WHERE idVoiture=:id";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $vId, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL idVoitureLoue\n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return !empty($resultat);
    }


    /**
     * Fonction permettant de louer une voiture
     * @param $vId string L'id de la voiture
     * @param $cId string L'id du client
     * @param $debut string Date de début de la location
     * @param $fin string Date de fin de la location
     * @param $payDate string Date de paiement de la location
     */
    public static function louerVoiture($vId, $cId, $debut, $fin, $payDate){
        require_once("./modele/VoitureDB.php");
        if (!VoitureDB::isVoitureDispo($vId) || self::isVoitureLouee($vId))
            return;

        require("./modele/connect.php");
        $debut = strtotime($debut);
        $debut = date('Y-m-d',$debut);

        $fin = strtotime($fin);
        $fin = date('Y-m-d',$fin);

        $sql = "INSERT INTO location (idLoc, idVoiture, idClient, tDebut, tFin, prixJour, payDate) VALUES (DEFAULT, :vId, :cId, :tDebut, :tFin, :prixJour, current_date)";
        $prix = VoitureDB::getPrix($vId);
        var_dump($vId, $cId, $debut, $fin, $payDate, $prix);

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':vId', $vId, PDO::PARAM_STR);
            $stmt->bindParam(':cId', $cId, PDO::PARAM_STR);
            $stmt->bindParam(':tDebut', $debut, PDO::PARAM_STR);
            $stmt->bindParam(':tFin', $fin, PDO::PARAM_STR);
            $stmt->bindParam(':prixJour', $prix, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                return;
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }
}