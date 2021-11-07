<?php

class UserDB{

    public static function doesUserExists($email){
        if(empty($email))
            return false;

        require("./modele/connect.php");

        $sql = "SELECT email FROM client WHERE email=:email";
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

        return !empty($resultat);
    }

    public static function doesUserExistsWithId($userId){
        if(empty($userId))
            return false;

        require("./modele/connect.php");

        $sql = "SELECT email FROM client WHERE id=:id";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $userId, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return !empty($resultat);
    }

    public static function getGroup($email){
        require("./modele/connect.php");

        $sql = "SELECT groupe FROM client WHERE email=:email";
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

        return $resultat[0]["groupe"];

    }

    public static function insertUserIntoBdd($profil){
        require("./modele/connect.php");
        $hashedPassword = password_hash($profil["pw"], PASSWORD_DEFAULT);

        $sql = "INSERT INTO client (nom, prenom, password, email) VALUES (:nom, :prenom, :pw, :email)";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':pw', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $profil["nom"], PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $profil["prenom"], PDO::PARAM_STR);
            $stmt->bindParam(':email', $profil["email"], PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return true;
    }

    /**
     * Détermine si un user est connecté et s'il peut rester connecté (session toujours active)
     * @param $email string email du user
     * @param $cookieStr string jeton du user
     * @return boolean true si le user est actuellement connecté, false dans le cas contraire
     */
    public static function isAuthOk($email, $cookieStr){
        if (is_null($email) || is_null($cookieStr))
            return false;

        require("./modele/connect.php");

        $sql = "SELECT email, jetonTime FROM client WHERE email=:email AND jeton=:jeton";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':jeton', $cookieStr, PDO::PARAM_STR);
            if (!$stmt->execute())
                return false;
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            return false;
        }

        if (empty($resultat) || empty($resultat[0]["jetonTime"]) || empty($resultat[0]["email"]))
            return false;

        return time()<$resultat[0]["jetonTime"];
    }

    public static function getNameWithUserId($userId){
        require("./modele/connect.php");

        $sql = "SELECT nom, prenom FROM client WHERE id=:userId";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return $resultat[0]["nom"]." ".$resultat[0]["prenom"];
    }

    /**
     * @return int l'id en bdd du user
     */
    public static function getUserId($email){
        require("./modele/connect.php");

        $sql = "SELECT id FROM client WHERE email=:email";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL 1\n");
            else
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["id"]; //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

    }


    public static function getUserMail($userId){
        require("./modele/connect.php");

        $sql = "SELECT email FROM client WHERE id=:userId";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL \n");
            else
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["email"]; //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
    }

}