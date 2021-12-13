<?php

class UserDB{


    private $email, $pw;
    private $userAuth = false;

    /**
     * Constructeur
     * @param $email string email du user, utilisée comme login
     * @param $pw string le password du user
     */
    public function __construct($email, $pw){
        $this->email = $email;
        $this->pw = $pw;
    }

    /**
     * Fonction permettant de connecter un user
     * @return boolean le cookie d'authentification si la connexion est ok, false dans le cas contraire
     */
    public function connectUser(){
        //$this->userAuth = $this->userCanConnect() ? true : false;
        if (!$this->userCanConnect())
            return false;
        return $this->createAuthCookie();
    }


    /**
     * Fonction permettant de déterminer si un user existe en utilisant l'email
     * @param $email string l'email du user
     * @return bool true si le user existe, false dans le cas contraire
     */
    public static function doesUserExists($email){
        if(empty($email))
            return false;

        require("./modele/connect.php");

        $sql = "SELECT email FROM user WHERE email=:email";
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

    /**
     * Fonction permettant de déterminer si un user existe en utilisant l'id
     * @param $userId string le user id
     * @return bool true si le user existe, false dans le cas contraire
     */
    public static function doesUserExistsWithId($userId){
        if(empty($userId))
            return false;

        require("./modele/connect.php");

        $sql = "SELECT email FROM user WHERE id=:id";
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

    /**
     * Fonction permettant de récupérer le groupe d'un user (loueur ou client)
     * @param $email string l'email du user
     * @return string 0 si client, 1 si loueur et empty si user n'existe pas
     */
    public static function getGroup($email){
        require("./modele/connect.php");

        $sql = "SELECT groupe FROM user WHERE email=:email";
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

    /**
     * Fonction permettant d'ajouter un user dans la db
     * @param $profil array Les infos d'un futur user
     * @param $group int le groupe du user
     * @return bool true si tout s'est bien passé
     */
    public static function insertUserIntoBdd($profil, $group = 0){
        require("./modele/connect.php");
        $hashedPassword = password_hash($profil["pw"], PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (nom, prenom, nomEntreprise, codePostal, password, email, groupe) VALUES (:nom, :prenom, :nomEntreprise, :codePostal, :pw, :email, :groupe)";
        try{
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':pw', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':nom', $profil["nom"], PDO::PARAM_STR);
            $stmt->bindParam(':nomEntreprise', $profil["nomEntreprise"], PDO::PARAM_STR);
            $stmt->bindParam(':codePostal', $profil["codePostal"], PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $profil["prenom"], PDO::PARAM_STR);
            $stmt->bindParam(':email', $profil["email"], PDO::PARAM_STR);
            $stmt->bindParam(':groupe', $group, PDO::PARAM_STR);
            if (!$stmt->execute())
                die  ("Echec de requête SQL ICI\n");
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

        $sql = "SELECT email, jetonTime FROM user WHERE email=:email AND jeton=:jeton";
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

    /**
     * Permet de récuperer le nom et prénom d'un user avec l'id
     * @param $userId string l'id du user
     * @return string le nom et prenom d'un user
     */
    public static function getNameWithUserId($userId){
        require("./modele/connect.php");

        $sql = "SELECT nom, prenom FROM user WHERE id=:userId";
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
     * Fonction permettant de récupérer l'id d'un user
     * @param $email string l'email du user
     * @return int l'id en bdd du user
     */
    public static function getUserId($email){
        require("./modele/connect.php");

        $sql = "SELECT id FROM user WHERE email=:email";

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


    /**
     * Fonction permettant de récupérer l'email d'un user
     * @param $userId string l'id du user
     * @return int l'email du user
     */
    public static function getUserMail($userId){
        require("./modele/connect.php");

        $sql = "SELECT email FROM user WHERE id=:userId";

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


    /**
     * Créer et enregistre en bdd le cookie d'authentification
     * @return string|null le cookie en string ou null si erreur
     */
    private function createAuthCookie(){
        require("./modele/connect.php");

        if (!$this->userAuth)
            return false;

        $sql = "UPDATE user SET jeton = :cookie, jetonTime = :cookieTime WHERE id = :id";

        $id = UserDB::getUserId($this->email);
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
     * Fonction permettant de générer une string 'cookie' d'authentification
     * @param $id string id du user
     * @return array Génère le cookie unique en premier indice et le timestamp max du cookie en deuxième indice
     */
    private function generateCookie($id){
        $cookies = array();
        $cookies[0]= $id . $this->generateRandomString();
        $cookies[1] = time() + 0.50 * 3600;
        return $cookies;
    }


    /**
     * Fonction permettant de générer une string random
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
     * Détermine si l'utilisateur peut se connecter
     * @return bool true si le user existe et peut se connecter car login + pw corrects, false dans le cas contraire
     */
    private function userCanConnect(){
        if (!isset($this->email) || !isset($this->pw))
            return false;

        require("./modele/connect.php");

        // hash pw

        $sql = "SELECT email, password FROM user WHERE email=:email";
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
        if (empty($resultat))
            return false;

        $this->userAuth = password_verify($this->pw, $resultat[0]["password"]);

        return $this->userAuth;
    }

}