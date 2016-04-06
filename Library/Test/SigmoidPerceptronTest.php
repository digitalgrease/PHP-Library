<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 6th April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test;

require_once '../Ai/NeuralNetwork/Neuron.php';
require_once '../Ai/NeuralNetwork/SigmoidPerceptron.php';
require_once '../Utils/MathUtils.php';

use GreasyLab\Library\Ai\NeuralNetwork\Neuron;
use GreasyLab\Library\Ai\NeuralNetwork\SigmoidPerceptron;
use GreasyLab\Library\Utils\MathUtils;

/**
 * Tests for the SigmoidPerceptron.
 *
 * @author Tom Gray
 */
class SigmoidPerceptronTest extends \PHPUnit_Framework_TestCase
{
    
    const MAX_X_Y = 10;
    
    const NUM_OF_TRAINING_ITERATIONS = 100000;
    
    protected $sigmoidPerceptron;
    
    /**
     * Create the perceptron with random weights.
     * 
     * @before
     */
    public function setUp()
    {
        $this->sigmoidPerceptron = new SigmoidPerceptron(
            Neuron::generateWeights(2)
        );
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
     * Train the perceptron to learn the outcome of AND.
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

                    $this->sigmoidPerceptron->train([$x, $y], $output);
                }
            }
        }
    }
    
    /**
     * Train the perceptron to learn the outcome of OR.
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
            
            $this->sigmoidPerceptron->train([$x, $y], $output);
        }
    }
    
    /**
     * Train the perceptron to guess whether points are above or below a
     * straight line.
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
                    
                    $this->sigmoidPerceptron->train([$x, $y], $output);
                }
            }
        }
    }
    
    /**
     * Verify the output of the perceptron.
     * 
     * @throws \Exception
     */
    private function verifyOutput($x, $y, $output)
    {
        $result = $this->sigmoidPerceptron->feedForward([$x, $y]);
        
        echo 'Expected: ' . $output . ' / Actual: ' . $result . PHP_EOL;
        
        if (!MathUtils::areFloatsEqual($result, $output, 0.01)) {
            throw new \Exception(
                'Failed for input ('.$x.','.$y.') => Expected: ' . $output
                . ' / Actual: ' . $result
            );
        }
    }
}
