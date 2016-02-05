<?php

/*
 * Copyright (c) 2014 Greasy Labs.
 */

namespace GreasyLabs;

/**
 * Simple log file for logging.
 * 
 * @author The Janitor <thejanitor@greasylabs.com>
 */
class LogFile
{
    /**
     * The full path of the log file.
     * 
     * @var string
     */
    protected $filePath;
    
    /**
     * Construct the log file.
     * 
     * @param string $filePath The full path of the log file.
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }
    
    /**
     * Add a time stamped message to the log file.
     * 
     * @param string $message
     * 
     * @return string $message
     */
    public function add($message)
    {
        $logFile = fopen($this->filePath, 'a');
        fwrite($logFile, $this->getTimeStamp() . ' ' . $message . PHP_EOL);
        fclose($logFile);
        return $message;
    }
    
    /**
     * Clear the log file.
     * 
     * @return LogFile This log file to allow method chaining.
     */
    public function clear()
    {
        fclose(fopen($this->filePath, 'w'));
    }
    
    /**
     * Get a human readable time stamp of the current date and time.
     * 
     * @return string A human readable time stamp.
     */
    private function getTimeStamp()
    {
        return date('[d-m-Y H:i:s]', microtime(true));
    }
}
