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

}