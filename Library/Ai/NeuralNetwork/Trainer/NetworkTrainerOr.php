<?php

/*
 * Copyright (c) 2017 Digital Grease Limited.
 * 
 * Version 1.0 Tuesday 24th January 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork\Trainer;

require_once 'AbstractBinaryNetworkTrainer.php';

use DigitalGrease\Library\Ai\NeuralNetwork\NetworkAnalyser;
use DigitalGrease\Library\Ai\NeuralNetwork\NetworkInterface;

/**
 * Neural network trainer to train a network on the outcome of the OR of two
 * boolean values.
 * 
 * @version 1.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
class NetworkTrainerOr extends AbstractBinaryNetworkTrainer
{
    
    /**
     * Test the accuracy of a network at guessing the output of the OR of two
     * boolean values.
     * 
     * @param NetworkInterface $network
     * 
     * @return float The success rate of the network as a percentage.
     */
    public function test(NetworkInterface $network)
    {
        $this->log = [];
        $nTests = $passedTests = $failedTests = 0;
        
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x || 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $hasPassed = $this->verifyNetworkOutput(
                    $network,
                    $x,
                    $y,
                    $output,
                    self::THRESHOLD
                );
                ++$nTests;
                if ($hasPassed) {
                    ++$passedTests;
                } else {
                    ++$failedTests;
                }
            }
        }
        
        return $passedTests / $nTests * 100;
    }
    
    /**
     * Train a network on the outcome of the OR of two boolean values.
     * 
     * @param NetworkInterface $network
     * @param NetworkAnalyser $analyser
     * @param int $iterations
     * 
     * @return void
     */
    public function train(
        NetworkInterface $network,
        NetworkAnalyser $analyser = null,
        $iterations = self::MAX_NUM_OF_TRAINING_ITERATIONS
    ) {
        for ($i = 0; $i < $iterations; ++$i) {
            for ($x = 0; $x < 2; ++$x) {
                for ($y = 0; $y < 2; ++$y) {
                    
                    if (1 == $x || 1 == $y) {
                        $output = 1;
                    } else {
                        $output = 0;
                    }

                    $network->train([$x, $y], [$output]);
                }
            }
            
            if ($analyser) $analyser->record();
        }
    }
}
