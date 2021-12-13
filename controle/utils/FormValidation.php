<?php

/**
 * Classe rassemblant toutes les fonctions utils du projet
 */
class FormValidation{
    /**
     * Constructeur en privé afin de permettre l'impossibilité d'instancier la classe
     */
    private function __construct(){}

    /**
     * Fonction permettant de vérifier le format email d'une string
     * @param $email l'input à vérifier
     * @return bool true si l'input correspond au format email, false dans le cas contraire
     */
    public static function isEmailRight($email){
        return !empty($email) && strpos($email, '@') !== false && strpos($email, '.') !== false;
    }

    /**
     * Fonction permettant de vérifier que chacune des cases du tab sont remplies. Chaque case correspond à un user input
     * @param $profil array Tableau à vérifier
     * @return bool true si le tableau ne possède pas de case vide, false dans le cas contraire
     */
    public static function areInputFilled($profil){
        $res = true;
        foreach ($profil as $i)
            if (empty($i))
                $res = false;
        return $res;
    }
}