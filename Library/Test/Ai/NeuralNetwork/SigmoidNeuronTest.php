<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Wednesday 6th April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test;

require_once '../Ai/NeuralNetwork/Neuron.php';
require_once '../Ai/NeuralNetwork/SigmoidNeuron.php';
require_once '../Utils/MathUtils.php';

use DigitalGrease\Library\Ai\NeuralNetwork\Neuron;
use DigitalGrease\Library\Ai\NeuralNetwork\SigmoidNeuron;
use DigitalGrease\Library\Utils\MathUtils;

/**
 * Tests for the SigmoidNeuron.
 *
 * @author Tom Gray
 */
class SigmoidNeuronTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * The maximum values to use for X and Y when training the neuron to guess
     * the points on a straight line.
     * 
     * @var int
     */
    const MAX_X_Y = 5;
    
    /**
     * The number of iterations to perform when training the neuron.
     * 
     * @var int
     */
    const NUM_OF_TRAINING_ITERATIONS = 100000;
    
    /**
     * The neuron to be trained and tested.
     * 
     * @var SigmoidNeuron
     */
    protected $sigmoidNeuron;
    
    /**
     * Create the neuron with random weights for training and testing.
     * 
     * @before
     */
    public function setUp()
    {
        $this->sigmoidNeuron = new SigmoidNeuron(Neuron::generateWeights(2));
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAllPointLocationsForStraightLine()
    {
        $this->trainStraightLine(10000);
        $this->runStraightLine();
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAndOfTwoInputs()
    {
        $this->trainAnd(self::NUM_OF_TRAINING_ITERATIONS);
        $this->runAnd();
    }
    
    /**
     * @test
     */
    public function correctlyGuessesOrOfTwoInputs()
    {
        $this->trainOr(self::NUM_OF_TRAINING_ITERATIONS);
        $this->runOr();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenAndOfTwoInputsIsWrong()
    {
        $this->trainAnd(1);
        $this->runAnd();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenOrOfTwoInputsIsWrong()
    {
        $this->trainOr(1);
        $this->runOr();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenPointLocationForStraightLineIsWrong()
    {
        $this->trainStraightLine(1);
        $this->runStraightLine();
    }
    
    /**
     * 
     * @throws \Exception
     */
    private function runAnd()
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x && 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $this->verifyOutput($x, $y, $output);
            }
        }
    }
    
    /**
     * 
     * @throws \Exception
     */
    private function runOr()
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x || 1 == $y) {
                    $output = 1;
                } else {
                    $output = 0;
                }
                
                $this->verifyOutput($x, $y, $output);
            }
        }
    }
    
    /**
     * 
     * 
     * @throws \Exception
     */
    private function runStraightLine()
    {
        for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
            for ($y = 0; $y <= self::MAX_X_Y; ++$y) {
                
                // If point is below the line y = x then output should be 0.
                if ($y < $x) {
                    $output = 0;
                } else {
                    $output = 1;
                }
                
                $this->verifyOutput($x, $y, $output);
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
        for ($i = 0; $i < $iterations; ++$i) {
            for ($x = 0; $x < 2; ++$x) {
                for ($y = 0; $y < 2; ++$y) {
                    
                    if (1 == $x && 1 == $y) {
                        $output = 1;
                    } else {
                        $output = 0;
                    }

                    $this->sigmoidNeuron->train([$x, $y], $output);
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
        for ($i = 0; $i < $iterations; ++$i) {
            $x = mt_rand(0, 1);
            $y = mt_rand(0, 1);
            
            if (1 == $x || 1 == $y) {
                $output = 1;
            } else {
                $output = 0;
            }
            
            $this->sigmoidNeuron->train([$x, $y], $output);
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
        for ($i = 0; $i < $iterations; ++$i) {
            for ($x = 0; $x < self::MAX_X_Y; ++$x) {
                for ($y = 0; $y < self::MAX_X_Y; ++$y) {
                    
                    if ($y < $x) {
                        $output = 0;
                    } else {
                        $output = 1;
                    }
                    
                    $this->sigmoidNeuron->train([$x, $y], $output);
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
     * 
     * @throws \Exception
     */
    private function verifyOutput($x, $y, $output)
    {
        $result = $this->sigmoidNeuron->feedForward([$x, $y]);
        
        if (!MathUtils::areFloatsEqual($result, $output, 0.01)) {
            throw new \Exception(
                'Failed for input ('.$x.','.$y.') => Expected: ' . $output
                . ' / Actual: ' . $result
            );
        }
    }
}
