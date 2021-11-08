<?php

class FormValidation{

    private function __construct(){}

    public static function isEmailRight($email){
        return !empty($email) && strpos($email, '@') !== false && strpos($email, '.') !== false;
    }

    public static function areInputFilled($profil){
        $res = true;
        foreach ($profil as $i)
            if (empty($i))
                $res = false;
        return $res;
    }
}