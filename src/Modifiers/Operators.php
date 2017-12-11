<?php

namespace DBAL\Modifiers;

class Operators {
    CONST OPERATORS = array(
        '>' => array(
            'format' => '> ?',
            'prepared' => true
        ),
        '>=' => array(
            'format' => '>= ?',
            'prepared' => true
        ),
        '<' => array(
            'format' => '< ?',
            'prepared' => true
        ),
        '<=' => array(
            'format' => '<= ?',
            'prepared' => true
        ),
        '!=' => array(
            'format' => '!= ?',
            'prepared' => true
        ),
        'LIKE' => array(
            'format' => 'LIKE ?',
            'prepared' => true
        ),
        'NOT LIKE' => array(
            'format' => 'NOT LIKE ?',
            'prepared' => true
        ),
        'IN' => array(
            'format' => '%s ?',
            'prepared' => true
        ),
        'NOT IN' => array(
            'format' => '%s ?',
            'prepared' => true
        ),
        'BETWEEN' => array(
            'format' => 'BETWEEN ? AND ?',
            'prepared' => true
        ),
        'NOT BETWEEN' => array(
            'format' => 'NOT BETWEEN ? AND ?',
            'prepared' => true
        ),
        'IS NULL' => array(
            'format' => 'IS NULL',
            'prepared' => false
        ),
        'IS NOT NULL' => array(
            'format' => 'IS NOT NULL',
            'prepared' => false
        )
    );
    
    /**
     * Gets the correct formated string for the operator
     * @param string $value This should be the operator value
     * @return string The string to add to the database will be added
     */
    public static function getOperatorFormat($value){
        if(array_key_exists(strtoupper(strval($value)), self::OPERATORS)){return self::OPERATORS[strtoupper($value)]['format'];}
        return '= ?';
    }
    
    /**
     * Checks to see if the operator is valid
     * @param string $operator This should be the operator value
     * @return boolean If the operator exists in the array will return true else returns false
     */
    public static function isOperatorValid($operator){
        if(array_key_exists(strtoupper(strval($operator)), self::OPERATORS)){return true;}
        return false;
    }
    
    /**
     * Checks to see if a prepared statement value should be added 
     * @param string $value This should be the operator value
     * @return boolean If the operator should be prepared returns true else returns false
     */
    public static function isOperatorPrepared($value){
        return self::OPERATORS[strtoupper(strval($value))]['prepared'];
    }
}
