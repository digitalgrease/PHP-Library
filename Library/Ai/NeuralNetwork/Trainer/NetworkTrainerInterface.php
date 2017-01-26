<?php

/*
 * Copyright (c) 2017 Digital Grease Limited.
 * 
 * Version 1.0 Tuesday 24th January 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork\Trainer;

use DigitalGrease\Library\Ai\NeuralNetwork\NetworkAnalyser;
use DigitalGrease\Library\Ai\NeuralNetwork\NetworkInterface;

/**
 * API for neural network trainers.
 * 
 * @version 1.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
interface NetworkTrainerInterface
{
    
    /**
     * Default number of training iterations.
     * 
     * @var int
     */
    const MAX_NUM_OF_TRAINING_ITERATIONS = 100000;
    
    /**
     * Test the accuracy of a network at guessing output.
     * 
     * @param NetworkInterface $network
     * 
     * @return float The success rate of the network as a percentage.
     */
    public function test(NetworkInterface $network);
    
    /**
     * Train a network for a number of iterations.
     * If an analyser is provided then data is recorded at each iteration.
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
    );
}
