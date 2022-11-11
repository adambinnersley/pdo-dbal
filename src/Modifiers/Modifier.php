<?php

namespace DBAL\Modifiers;

class Modifier
{
    /**
     * Set value to null if value is empty
     * @param mixed $variable This should be the variable you are checking if it is empty
     * @return mixed Returns either NULL or the original variable
     */
    public static function setNullOnEmpty($variable)
    {
        if (empty(trim($variable ? $variable : ''))) {
            return null;
        }
        return $variable;
    }
    
    /**
     * Set value to 0 if value is empty
     * @param mixed $variable This should be the variable you are checking if it is empty
     * @return mixed Returns either 0 or the original variable
     */
    public static function setZeroOnEmpty($variable)
    {
        if (empty(trim($variable ? $variable : 0)) || (is_numeric($variable) && floatval($variable) == 0)) {
            return 0;
        }
        return $variable;
    }
    
    /**
     * Checks to see if required filed have been filled in and are not empty
     * @param mixed $variable This should be the variable you are checking
     * @return boolean Will return true if not empty and contains at least minimum number of characters else returns false
     */
    public static function isRequiredString($variable, $minStringLength = 2)
    {
        if (!empty(trim($variable ? $variable : '')) && strlen(trim($variable ? $variable : '')) >= $minStringLength) {
            return true;
        }
        return false;
    }
    
    /**
     * Checks to see if the variable is numeric or not
     * @param mixed $variable This should be the variable you are testing if it is a number or not
     * @return boolean Returns true if numeric else returns false
     */
    public static function isRequiredNumeric($variable)
    {
        if (isset($variable) && is_numeric($variable)) {
            return true;
        }
        return false;
    }
    
    /**
     * Checks to see if the variable is non numeric or not
     * @param mixed $variable This should be the variable you are testing if it is a number or not
     * @return boolean Returns true if non numeric else returns false
     */
    public static function isRequiredNonNumeric($variable)
    {
        if (!empty(trim($variable ? $variable : '')) && !is_numeric($variable)) {
            return true;
        }
        return false;
    }
    
    /**
     * Remove anything that isn't a number from a string
     * @param string $string This should be the string that you want to remove any none numerical characters from
     * @return string The filtered string is returned
     */
    public static function removeNoneNumeric($string)
    {
        if (!empty($string)) {
            return preg_replace("/[^0-9 ]+/", "", $string);
        }
        return $string;
    }
    
    /**
     * Removes anything that isn't in the alphabet A-Z
     * @param string $string This should be the string that you want to remove all none alphabetical characters
     * @return string The filtered string is returned
     */
    public static function removeNoneAlpha($string)
    {
        if (!empty($string)) {
            return preg_replace("/[^a-zA-Z ]+/", "", $string);
        }
        return $string;
    }
    
    /**
     * Removes all non Alpha-numeric characters from a string
     * @param string $string The string that you want to remove any non alpha-numerical characters from
     * @return string The filtered string is returned
     */
    public static function removeNoneAlphaNumeric($string)
    {
        if (!empty($string)) {
            return preg_replace("/[^a-zA-Z0-9 ]+/", "", $string);
        }
        return $string;
    }
    
    /**
     * Checks to see if array contains all of the required fields and are not empty
     * @param array $mustContain an array of the values that must be included in the array
     * @param array $valueArray The array you are checking for the values
     * @return boolean If all of the fields exist and are not empty will return true else returns false
     */
    public static function arrayMustContainFields($mustContain, $valueArray)
    {
        if (is_array($mustContain) && !empty($mustContain)) {
            if (is_array($valueArray)) {
                foreach ($mustContain as $essential) {
                    if (!array_key_exists($essential, $valueArray) || !self::isRequiredString($valueArray[$essential])) {
                        return false;
                    }
                }
                return true;
            }
            return false;
        }
        return true;
    }
}
