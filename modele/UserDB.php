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

}