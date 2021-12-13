<?php

/**
 * Modèle gérant l'aspect admin du site
 */
class AdminDB{


    /**
     * Détermine si l'admin peut se connecter
     * @return bool true si l'admin existe et peut se connecter car login + pw corrects, false dans le cas contraire
     */
    public static function adminCanConnect($login, $pw){
        require("./modele/connect.php");

        $sql = "SELECT pw FROM administrateur WHERE login=:login";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':login', $login, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL 2\n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        //var_dump($resultat[0]["password"]); die();
        return !empty($resultat) && password_verify($pw, $resultat[0]["pw"]);

    }
}