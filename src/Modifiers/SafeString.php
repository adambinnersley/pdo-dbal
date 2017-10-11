<?php

namespace DBAL\Modifiers;

class SafeString{
    
    /**
     * Make any SQL field names safe
     * @param string $string The string that you wish to make safe
     * @return string Will return the safe value
     */
    public static function makeSafe($string){
        return preg_replace("/^[a-z0-9_]+$/i", "", $string);
    }
}
