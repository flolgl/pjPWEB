<?php

class LoginClass
{
    private $email, $pw;
    private $userAuth = false;

    /**
     * @param $email string email du user, utilisée comme login
     * @param $pw string le password du user
     */
    public function __construct($email, $pw){
        $this->email = $email;
        $this->pw = $pw;
    }

    /**
     * @return boolean le cookie d'authentification si la connexion est ok, false dans le cas contraire
     */
    public function connectUser(){
        //$this->userAuth = $this->userCanConnect() ? true : false;
        if (!$this->userCanConnect())
            return false;
        return $this->createAuthCookie();
    }

    /**
     * Créer et enregistre en bdd le cookie d'authentification
     * @return string|null le cookie en string ou null si erreur
     */
    private function createAuthCookie(){
        require("./modele/connect.php");

        if (!$this->userAuth)
            return false;

        $sql = "UPDATE client SET jeton = :cookie, jetonTime = :cookieTime WHERE id = :id";

        $id = $this->getUserId();
        $cookie = $this->generateCookie($id);


        //enregistrer cookie

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':cookie', $cookie[0], PDO::PARAM_STR);
            $stmt->bindParam(':cookieTime', $cookie[1], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if (!$stmt->execute())
                return null;
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }
        return $cookie[0];
    }

    /**
     * @return array Génère le cookie unique en premier indice et le timestamp max du cookie en deuxième indice
     */
    private function generateCookie($id){
        $cookies = array();
        $cookies[0]= $id . $this->generateRandomString();
        $cookies[1] = time() + 0.50 * 3600;
        return $cookies;
    }


    /**
     * @return string Une string random
     */
    private function generateRandomString(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < strlen($characters); $i++)
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        return $randomString;
    }

    /**
     * @return int l'id en bdd du user
     */
    private function getUserId(){
        require("./modele/connect.php");

        $sql = "SELECT id FROM client WHERE email=:email";

        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);

            if (!$stmt->execute())
                die  ("Echec de requête SQL 1\n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        return $resultat[0]["id"];
    }

    /**
     * Détermine si l'utilisateur peut se connecter
     * @return bool true si le user existe et peut se connecter car login + pw corrects, false dans le cas contraire
     */
    private function userCanConnect(){
        require("./modele/connect.php");

        // hash pw

        $sql = "SELECT email, password FROM client WHERE email=:email";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL 2\n");
            else
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }catch(PDOException $e){
            die  ("Echec de requête SQL : " . utf8_encode($e->getMessage()) . "\n");
        }

        //var_dump($resultat[0]["password"]); die();

        $this->userAuth = password_verify($this->pw, $resultat[0]["password"]);

        return $this->userAuth;
    }

}