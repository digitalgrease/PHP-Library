<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Utils;

/**
 * Defines a library function to repeatedly attempt to call a function that
 * throws an exception until it no longer throws the exception or a pre-defined
 * number of attempts has been reached.
 * 
 * @author Tom Gray
 */
class Persevere
{
    const NUMBER_OF_ATTEMPTS = 10;
    
    /**
     * Persevere with calling the given function repeatedly if it throws an
     * exception.
     * 
     * @param string  $functionName
     * @param array   $args
     * @param object  $object
     * @param integer $nAttempts
     * 
     * @return mixed
     * 
     * @throws Exception
     */
    public static function persevere(
        $functionName,
        array $args = [],
        object $object = null,
        $nAttempts = self::NUMBER_OF_ATTEMPTS
    ) {
        // Build the complete function call.
        $functionCall = empty($object) ? '' : $object . '->';
        $functionCall .= $functionName . '(';
        if (empty($args)) {
            $functionCall .= ')';
        } else {
            foreach ($args as $arg) {
                $functionCall .= $arg . ', ';
            }
            $functionCall = substr($functionCall, 0, -2) . ')';
        }
        
        echo $functionCall;die;
        
        // Attempt to call the function repeatedly.
        $ex = null;
        $iAttempts = 0;
        while ($iAttempts < $nAttempts) {
            try {
                return true; // call the full function here
            } catch (Exception $ex) {
                ++$iAttempts;
            }
        }
        throw $ex;
    }
}
