<?php

/*
 * Copyright (c) 2017 Digital Grease Limited.
 * 
 * Version 1.0 Tuesday 24th January 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork\Trainer;

require_once 'DigitalGrease/Library/Ai/NeuralNetwork/Trainer/NetworkTrainerInterface.php';
require_once 'DigitalGrease/Library/Utils/MathUtils.php';

use DigitalGrease\Library\Ai\NeuralNetwork\NetworkInterface;
use DigitalGrease\Library\Utils\MathUtils;

/**
 * Abstract class for binary network trainers to inherit from and implement.
 * 
 * @version 1.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
abstract class AbstractBinaryNetworkTrainer implements NetworkTrainerInterface
{
    
    const THRESHOLD = 0.01;
    
    /**
     * Log of messages stored by the trainer on each method run.
     * 
     * @var string[]
     */
    protected $log = [];
    
    /**
     * Get the log messages from the trainer.
     * 
     * @return string[]
     */
    public function getLog()
    {
        return $this->log;
    }
    
    /**
     * Verify whether the output of a network is within a given threshold for
     * two values.
     * 
     * @param NetworkInterface $network
     * @param int $x
     * @param int $y
     * @param float $output
     * @param float $threshold
     * 
     * @return boolean
     */
    protected function verifyNetworkOutput(
        NetworkInterface $network,
        $x,
        $y,
        $output,
        $threshold
    ) {
        $result = $network->feedForward([$x, $y])[0];
        
        if (!MathUtils::areFloatsEqual($result, $output, $threshold)) {
            $this->log[] = 'For inputs ' . $x . ',' . $y
                . ' expected ' . $output . ' but got ' . $result;
            return false;
        }
        return true;
    }
}
