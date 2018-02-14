<?php

namespace DBAL\Modifiers;

class SafeString{
    
    /**
     * Make any SQL field names safe
     * @param string $string The string that you wish to make safe
     * @return string Will return the safe value
     */
    public static function makeSafe($string){
        return (string)preg_replace("/[^A-Za-z0-9_]/", "", $string);
    }
}
