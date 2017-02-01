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
 * Neural network trainer to train a network on what side of a straight line two
 * points are.
 * 
 * @version 1.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
class NetworkTrainerStraightLine extends AbstractBinaryNetworkTrainer
{
    
    /**
     * The maximum points on the straight line used for training and testing.
     * 
     * @var int
     */
    const MAX_X_Y = 5;
    
    /**
     * Test the accuracy of a network at guessing on what side of a straight
     * line two points are.
     * 
     * @param NetworkInterface $network
     * 
     * @return float The success rate of the network as a percentage.
     */
    public function test(NetworkInterface $network)
    {
        $this->log = [];
        $nTests = $passedTests = $failedTests = 0;
        
        for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
            for ($y = 0; $y <= self::MAX_X_Y; ++$y) {

                // If point is below the line y = x then output should be 0.
                if ($y < $x) {
                    $output = 0;
                } else {
                    $output = 1;
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
     * Train a network at guessing on what side of a straight line two points
     * are.
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
            for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
                for ($y = 0; $y <= self::MAX_X_Y; ++$y) {

                    // If point is below the line y = x then output should be 0.
                    if ($y < $x) {
                        $output = 0;
                    } else {
                        $output = 1;
                    }

                    $network->train([$x, $y], [$output]);
                }
            }
            
            if ($analyser) $analyser->record();
        }
    }
}
