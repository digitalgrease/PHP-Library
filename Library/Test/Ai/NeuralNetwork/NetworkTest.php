<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 15th April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test;

require_once '../Ai/NeuralNetwork/Network.php';
require_once '../Ai/NeuralNetwork/SigmoidNeuron.php';
require_once '../Utils/MathUtils.php';

use GreasyLab\Library\Ai\NeuralNetwork\Network;
use GreasyLab\Library\Ai\NeuralNetwork\SigmoidNeuron;
use GreasyLab\Library\Utils\MathUtils;

/**
 * Tests for the Network class.
 *
 * @author Tom Gray
 */
class NetworkTest extends \PHPUnit_Framework_TestCase
{
    
    const NUM_OF_TRAINING_ITERATIONS = 100000;
    
    protected $network;
    
    /**
     * Create the network.
     * 
     * @before
     */
    public function setUp()
    {
        $this->network = new Network(
            [
                [
                    new SigmoidNeuron(SigmoidNeuron::generateWeights(2)),
                    new SigmoidNeuron(SigmoidNeuron::generateWeights(2))
                ],
                [
                    new SigmoidNeuron(SigmoidNeuron::generateWeights(2))
                ]
            ]
        );
    }
    
    /**
     * @test
     */
    public function correctlyGuessesXor()
    {
        $this->trainXor(self::NUM_OF_TRAINING_ITERATIONS);
        $this->runXor();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenXorOfTwoInputsIsWrong()
    {
        $this->trainXor(1);
        $this->runXor();
    }
    
    /**
     * Get whether the network is returning the desired outputs.
     * 
     * @return boolean
     */
    private function areAllOutputsWithinThreshold()
    {
        try {
            $this->runXor();
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }
    
    /**
     * 
     * @throws \Exception
     */
    private function runXor()
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                
                if (1 == $x && 1 == $y) {
                    $output = 0;
                } elseif (1 == $x || 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $result = $this->network->feedForward([$x, $y])[0];
                if (!MathUtils::areFloatsEqual($result, $output, 0.01)) {
                    throw new \Exception(
                        'Failed for input ('.$x.','.$y.') => Expected: '
                        . $output . ' / Actual: ' . $result
                    );
                }
            }
        }
    }
    
    /**
     * Train the network to learn the outcome of XOR.
     * 
     * @param int $iterations
     */
    private function trainXor($iterations)
    {
        echo 'Starting weights = ' . $this->network . PHP_EOL;
        $weights = [];
        
        $i = 0;
        while (
            !$this->areAllOutputsWithinThreshold()
            && $i < $iterations
        ) {
            
            for ($x = 0; $x < 2; ++$x) {
                for ($y = 0; $y < 2; ++$y) {

                    if (1 == $x && 1 == $y) {
                        $output = 0;
                    } elseif (1 == $x || 1 == $y) {
                        $output = 1;
                    } else {
                        $output = 0;
                    }
                    
                    $this->network->train([$x, $y], [$output]);
                }
            }
            
            // Store the weights after the final 10 complete iterations for
            // comparison and analysis.
            $weights[$i % 10] = $this->network->__toString();
            
            ++$i;
        }
        
        echo 'Total Iterations = ' . $i . PHP_EOL;
        
        echo 'Weights after the last 10 iterations:' . PHP_EOL;
        
        // Display the end of the array of weights for the oldest recorded.
        $w = 0;
        for ($j = $i % 10; $j < 10; ++$j) {
            echo ++$w . ' => ' . $weights[$j] . PHP_EOL;
        }
        
        // Display the start of the array of weights for the latest recorded.
        for ($j = 0; $j < $i % 10; ++$j) {
            echo ++$w . ' => ' . $weights[$j] . PHP_EOL;
        }
    }
}
