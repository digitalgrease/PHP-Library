<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 15th April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test;

require_once '../Ai/NeuralNetwork/Network.php';
require_once '../Ai/NeuralNetwork/SigmoidNeuron.php';
require_once '../Utils/MathUtils.php';

use DigitalGrease\Library\Ai\NeuralNetwork\Network;
use DigitalGrease\Library\Ai\NeuralNetwork\SigmoidNeuron;
use DigitalGrease\Library\Utils\MathUtils;

/**
 * Tests for the Network class.
 * 
 * @author Tom Gray
 */
class NetworkTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * The maximum number of iterations to perform when training the network.
     * 
     * @var int
     */
    const MAX_NUM_OF_TRAINING_ITERATIONS = 100000;
    
    /**
     * The maximum values to use for X and Y when training the neuron to guess
     * the points on a straight line.
     * 
     * @var int
     */
    const MAX_X_Y = 5;
    
    /**
     * Defines the accuracy required for the network to pass the tests.
     * 
     * @var float
     */
    const THRESHOLD = 0.01;
    
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
        $this->trainXor(self::MAX_NUM_OF_TRAINING_ITERATIONS);
        $this->runXor(self::THRESHOLD);
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenXorOfTwoInputsIsWrong()
    {
        $this->trainXor(1);
        $this->runXor(self::THRESHOLD);
    }
    
    /**
     * Get whether the network is returning the desired outputs to a certain
     * degree of accuracy.
     * 
     * @return boolean
     */
    private function areAllOutputsWithinThreshold()
    {
        try {
            $this->runXor(self::THRESHOLD);
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }
    
    /**
     * Test the accuracy of the network at guessing the output of the AND of two
     * boolean values.
     * 
     * @param float $threshold The degree of accuracy to test the output to.
     * 
     * @throws \Exception Throws an exception if the network does not guess the
     *  output to within the threshold.
     */
    private function runAnd($threshold)
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x && 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $this->verifyOutput($x, $y, $output, $threshold);
            }
        }
    }
    
    /**
     * Test the accuracy of the network at guessing the output of the OR of two
     * boolean values.
     * 
     * @param float $threshold The degree of accuracy to test the output to.
     * 
     * @throws \Exception Throws an exception if the network does not guess the
     *  output to within the threshold.
     */
    private function runOr($threshold)
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x || 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $this->verifyOutput($x, $y, $output, $threshold);
            }
        }
    }
    
    /**
     * Test the accuracy of the network at guessing whether points are above or
     * below a straight line.
     * 
     * @param float $threshold The degree of accuracy to test the output to.
     * 
     * @throws \Exception Throws an exception if the network does not guess the
     *  output to within the threshold.
     */
    private function runStraightLine($threshold)
    {
        for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
            for ($y = 0; $y <= self::MAX_X_Y; ++$y) {
                
                // If point is below the line y = x then output should be 0.
                if ($y < $x) {
                    $output = 0;
                } else {
                    $output = 1;
                }
                
                $this->verifyOutput($x, $y, $output, $threshold);
            }
        }
    }
    
    /**
     * Test the accuracy of the network at guessing the output of the XOR of two
     * boolean values.
     * 
     * @param float $threshold The degree of accuracy to test the output to.
     * 
     * @throws \Exception Throws an exception if the network does not guess the
     *  output to within the threshold.
     */
    private function runXor($threshold)
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
                
                $this->verifyOutput($x, $y, $output, $threshold);
            }
        }
    }
    
    /**
     * Train the neuron to learn the outcome of AND.
     * 
     * @param int $iterations
     */
    private function trainAnd($iterations)
    {
        $i = 0;
        while (
            !$this->areAllOutputsWithinThreshold()
            && $i < $iterations
        ) {
            
            for ($x = 0; $x < 2; ++$x) {
                for ($y = 0; $y < 2; ++$y) {
                    
                    if (1 == $x && 1 == $y) {
                        $output = 1;
                    } else {
                        $output = 0;
                    }

                    $this->network->train([$x, $y], [$output]);
                }
            }
        }
    }
    
    /**
     * Train the neuron to learn the outcome of OR.
     * 
     * @param int $iterations
     */
    private function trainOr($iterations)
    {
        $i = 0;
        while (
            !$this->areAllOutputsWithinThreshold()
            && $i < $iterations
        ) {
            
            for ($x = 0; $x < 2; ++$x) {
                for ($y = 0; $y < 2; ++$y) {
                    
                    if (1 == $x || 1 == $y) {
                        $output = 1;
                    } else {
                        $output = 0;
                    }

                    $this->network->train([$x, $y], [$output]);
                }
            }
        }
    }
    
    /**
     * Train the neuron to guess whether points are above or below a straight
     * line.
     * 
     * @param int $iterations
     */
    private function trainStraightLine($iterations)
    {
        $i = 0;
        while (
            !$this->areAllOutputsWithinThreshold()
            && $i < $iterations
        ) {
            
            for ($x = 0; $x < self::MAX_X_Y; ++$x) {
                for ($y = 0; $y < self::MAX_X_Y; ++$y) {
                    
                    if ($y < $x) {
                        $output = 0;
                    } else {
                        $output = 1;
                    }
                    
                    $this->network->train([$x, $y], [$output]);
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
        }
    }
    
    /**
     * Verify the output of the neuron.
     * 
     * @param int $x
     * @param int $y
     * @param float $output
     * @param float $threshold
     * 
     * @throws \Exception
     */
    private function verifyOutput($x, $y, $output, $threshold)
    {
        $result = $this->network->feedForward([$x, $y])[0];
        
        if (!MathUtils::areFloatsEqual($result, $output, $threshold)) {
            throw new \Exception(
                'Failed for input ('.$x.','.$y.') => Expected: '
                . $output . ' / Actual: ' . $result
            );
        }
    }
}
