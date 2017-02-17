<?php

/*
 * Copyright (c) 2017 Digital Grease Limited.
 * 
 * Version 1.0 Friday 17th February 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Utils;

/**
 * General data logger.
 *
 * @author Tom Gray
 * @version 1.0 Friday 17th February 2017
 */
class Logger
{
    
    /**
     * The default number of days before log files expire if no number is given.
     * 
     * @var int
     */
    const DEFAULT_DAYS_TO_KEEP_LOG_FILES = 30;
    
    /**
     * The default name and directory to log data under if no name is specified
     * when logging data.
     * 
     * @var string
     */
    protected $defaultLogName;
    
    /**
     * The number of days before log files expire.
     * 
     * @var int
     */
    protected $fileLife;
    
    /**
     * Absolute path to the parent directory that contains all logs.
     * 
     * @var string
     */
    protected $logDirPath;
    
    /**
     * Construct a logger.
     * 
     * @param string $logDirPath
     * @param string $defaultLogName
     * @param int $fileLifeDays
     */
    public function __construct(
        $logDirPath,
        $defaultLogName = null,
        $fileLifeDays = self::DEFAULT_DAYS_TO_KEEP_LOG_FILES
    ) {
        $this->createParentLogsDirectory($logDirPath);
        $this->defaultLogName = $defaultLogName;
        $this->fileLife = $fileLifeDays;
    }
    
    /**
     * Log data in today's log file under a given directory.
     * 
     * @param array $data
     * @param string $logName
     * 
     * @return void
     */
    public function log(array $data, $logName = null)
    {
        $logDir = $logName ? $logName : $this->defaultLogName;
        if ($logDir) {
            $this->createLogDirectory($logDir);
            $this->removeExpiredLogFiles($logDir);
            $this->addDataToLog($data, $logDir);
        } else {
            throw new \Exception(
                'No log name provided and no default log name has been set.'
            );
        }
    }
    
    /**
     * Add data to today's log.
     * 
     * @param array $data
     * @param string $logDir
     * 
     * @return void
     */
    protected function addDataToLog(array $data, $logDir)
    {
        $now = new \DateTime();
        $logFilePath = $this->logDirPath . rtrim($logDir, '/') . '/'
            . $now->format('Y-m-d') . '.log';
        
        $logString = $now->format('H:i:s') . ' Data:' . PHP_EOL
            . print_r($data, true) . PHP_EOL . PHP_EOL;
        
        $fp = fopen($logFilePath, 'a');
        fputs($fp, $logString);
        fclose($fp);
    }
    
    /**
     * Create a log directory.
     * 
     * @param string $logName
     * 
     * @return void
     * 
     * @throws \Exception
     */
    protected final function createLogDirectory($logName)
    {
        if ($logName) {
            $logPath = $this->logDirPath . $logName;
            
            if (!is_dir($logPath)) {
                if (!mkdir($logPath, 0775)) {
                    throw new \Exception(
                        'Failed to create the directory for logging at '
                        . $logPath
                    );
                }
            }
        }
    }
    
    /**
     * Create the parent logs directory if it does not exist.
     * 
     * @param string $logDirPath
     * 
     * @return void
     * 
     * @throws \Exception
     */
    protected final function createParentLogsDirectory($logDirPath)
    {
        $this->logDirPath = rtrim($logDirPath, '/') . '/';
        if (!is_dir($this->logDirPath)) {
            if (!mkdir($this->logDirPath, 0775, true)) {
                throw new \Exception(
                    'Failed to create the directory for logging at '
                    . $this->logDirPath
                );
            }
        }
    }
    
    /**
     * Remove any expired log files from the log directory.
     * 
     * @param string $logName
     * 
     * @return void
     */
    protected final function removeExpiredLogFiles($logName)
    {
        $logPath = $this->logDirPath . rtrim($logName, '/') . '/';
        
        $expiryDate = new \DateTime();
        $expiryDate->modify('-' . $this->fileLife . ' days');
        
        foreach (scandir($logPath) as $logFile) {
            $date = \DateTime::createFromFormat(
                'Y-m-d',
                substr($logFile, 0, 10)
            );
            
            if ($date && $date < $expiryDate) {
                unlink($logPath . $logFile);
            }
        }
    }
}
