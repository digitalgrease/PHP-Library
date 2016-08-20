<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Utils;

/**
 * Executes a command multiple times in parallel processes with different
 * arguments.
 * 
 * @author The Janitor <thejanitor@DigitalGreases.com>
 */
class ParallelProcessor
{
    /**
     * The default maximum number of processes to be executing in parallel at
     * one time.
     */
    const MAX_NUM_PROCESSES = 40;
    
    /**
     * The default name to give the automatically generated script that is to be
     * executed in parallel.
     */
    const PROCESS_SCRIPT_FILENAME = 'execute-process';
    
    /**
     * The default suffix to give to the filenames that the processes write
     * their output to.
     */
    const PROCESS_OUTPUT_FILENAME = 'process.log';
    
    /**
     * The default suffix to give to the filenames that the processes write
     * their errors to.
     */
    const PROCESS_ERROR_LOG_FILENAME = 'error.log';
    
    /**
     * The time in seconds that the processor will sleep for before checking if
     * a process has become available when the maximum number or processes are
     * executing.
     */
    const NUM_SECONDS_TO_WAIT_FOR_FREE_PROCESS = 2;
    
    /**
     * The command that is to be executed multiple times in parallel.
     * 
     * @var string
     */
    protected $command;
    
    /**
     * The maximum number of processes to be executing in parallel at one time.
     * 
     * @var integer
     */
    protected $nProcesses;
    
    /**
     * The full path to the directory where all the output files will be
     * written.
     * 
     * @var string
     */
    protected $outputDirectory;
    
    /**
     * Automatically generated PHP script code that will be executed in parallel
     * to run the command.
     * 
     * @var string
     */
    protected $processScript;
    
    /**
     * The full path to the automatically generated script that is to be
     * executed in parallel.
     * 
     * @var string
     */
    protected $processScriptFilePath;
    
    /**
     * Generate the script that is to be executed in parallel and write it to
     * disk.
     * 
     * @param string  $command    The full path to the command to be executed in
     *                            parallel.
     * @param integer $nProcesses The maximum number of processes to be
     *                            executing in parallel at one time.
     */
    public function __construct($command, $nProcesses = self::MAX_NUM_PROCESSES)
    {
        $this->command = $command;
        $this->nProcesses = $nProcesses;
        $this->outputDirectory = __DIR__ . '/log/';
        $this->processScript = $this->buildProcessScript();
        $this->processScriptFilePath = $this->outputDirectory
            . self::PROCESS_SCRIPT_FILENAME . uniqid();
    }
    
    /**
     * Delete the automatically generated script that is to be executed in
     * parallel from the disk.
     */
    public function __destruct()
    {
        if (file_exists($this->processScriptFilePath)) {
            unlink($this->processScriptFilePath);
        }
    }
    
    /**
     * Execute the command with each set of arguments in data in parallel
     * processes.
     * 
     * @param array $data An array of single command line arguments or arrays of
     *                    arguments that are to be passed to the command to be
     *                    run.
     */
    public function execute(array $data)
    {
        // Write the process script to disk and make it executable.
        file_put_contents($this->processScriptFilePath, $this->processScript);
        chmod($this->processScriptFilePath, 0700);
        
        $iProcess = 1;
        foreach ($data as $args) {
            
            while (!$this->isFreeProcess()) {
                sleep(self::NUM_SECONDS_TO_WAIT_FOR_FREE_PROCESS);
            }
            
            $processOutputFilePath = $this->outputDirectory
                . $iProcess . '-' . self::PROCESS_OUTPUT_FILENAME;
            $processErrorLogFilePath = $this->outputDirectory
                . $iProcess++ . '-' . self::PROCESS_ERROR_LOG_FILENAME;
            
            $argString = $this->buildCommandArgs($args)
                . ' ' . $processOutputFilePath . ' ' . $processErrorLogFilePath;
            
            exec(
                $this->processScriptFilePath . $argString
                . ' > ' . $processOutputFilePath
                . ' 2> ' . $processErrorLogFilePath
                . ' &'
            );
        }
    }
    
    /**
     * Build the string of commmand line arguments to be passed to the command.
     * 
     * @param string|array $args The set of arguments to build the string from.
     * 
     * @return string
     */
    private function buildCommandArgs($args)
    {
        $argString = '';
        
        if (is_array($args)) {
            foreach ($args as $arg) {
                $argString .= ' ' . $arg;
            }
        } else {
            $argString .= ' ' . $args;
        }
        
        return $argString;
    }
    
    /**
     * Generate the script that is to be executed in parallel.
     * 
     * @return string
     */
    private function buildProcessScript()
    {
        return <<<EOT
#!/usr/bin/env php
<?php

// Display the arguments for debugging and information.
foreach (\$argv as \$i => \$arg) {
    fwrite(STDOUT, 'Argument ' . \$i . ' = ' . \$arg . PHP_EOL);
}

// Execute the script to run in parallel.
\$nArgs = count(\$argv);
\$args = '';
for (\$i = 1; \$i < \$nArgs - 2; ++\$i) {
    \$args .= ' ' . \$argv[\$i];
}
fwrite(STDOUT, 'Executing command $this->command' . PHP_EOL);
system('$this->command' . \$args);

// Delete the process error log file and the process output file.
unlink(\$argv[--\$nArgs]);
unlink(\$argv[--\$nArgs]);

EOT;
    }
    
    /**
     * Get whether there are currently less than the maximum number of processes
     * running.
     * 
     * @return boolean True if there are less than the maximum number of
     *                 processes running.
     *                 False if the maximum number of processes are currently
     *                 running.
     */
    private function isFreeProcess()
    {
        $regex = $this->outputDirectory . '*' . self::PROCESS_OUTPUT_FILENAME;
        return count(glob($regex)) < $this->nProcesses;
    }
}
