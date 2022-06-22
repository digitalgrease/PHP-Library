<?php

namespace DigitalGrease\Library\Utils;

/**
 * This should be expanded and improved to:
 *  include an exponential backoff
 *  include a circuit breaker element
 *
 * @author Tom Gray
 */
class Persevere
{
    /**
     * The default maximum number of times to call the function in case of an
     * exception.
     *
     * @var int
     */
    const NUMBER_OF_ATTEMPTS = 10;
    
    /**
     * Flag that defines the default setting for whether to throw exceptions.
     * This can be overriden on any individual method call.
     *
     * @var bool
     */
    const THROW_EXCEPTIONS = true;
    
    /**
     * Persevere with calling the given function repeatedly if it throws an
     * exception.
     * If exceptions are not to be thrown and the number of attempts is
     * exhausted then null will be returned.
     *
     * @param callable $function
     * @param bool $throwException
     * @param int $nAttempts
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public static function persevere(
        callable $function,
        bool $throwException = self::THROW_EXCEPTIONS,
        int $nAttempts = self::NUMBER_OF_ATTEMPTS
    ) {
        $exception = null;
        $iAttempt = 0;
        while ($iAttempt < $nAttempts) {
            try {
                return call_user_func($function);
            } catch (\Exception $exception) {
                ++$iAttempt;
            }
        }
        
        if ($throwException) {
            throw $exception;
        }
        
        return null;
    }
}
