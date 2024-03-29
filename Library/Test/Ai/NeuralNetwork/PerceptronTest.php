<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 1st April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Test;

use DigitalGrease\Library\Ai\NeuralNetwork\Neuron;
use DigitalGrease\Library\Ai\NeuralNetwork\Perceptron;
use Tests\TestCase;

/**
 * Tests for the Perceptron.
 *
 * @author Tom Gray
 */
class PerceptronTest extends TestCase
{
    const MAX_X_Y = 100;
    
    const NUM_OF_TRAINING_ITERATIONS = 100000;
    
    protected $perceptron;
    
    /**
     * Create the perceptron with random weights.
     * 
     * @before
     */
    public function setUp(): void
    {
        $this->perceptron = new Perceptron(Neuron::generateWeights(2));
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAllPointLocationsForStraightLine()
    {
        $this->trainStraightLine(self::NUM_OF_TRAINING_ITERATIONS);
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
                
                if ($this->perceptron->feedForward([$x, $y]) != $output) {
                    throw new \Exception('Failed for input ('.$x.','.$y.')');
                }
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
                
                if ($this->perceptron->feedForward([$x, $y]) != $output) {
                    throw new \Exception('Failed for input ('.$x.','.$y.')');
                }
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
                
                // If point is below the line y = x then output should be -1.
                if ($y < $x) {
                    $output = 0;
                } else {
                    $output = 1;
                }
                
                if ($this->perceptron->feedForward([$x, $y]) != $output) {
                    throw new \Exception('Failed for ('.$x.','.$y.')');
                }
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
            $x = mt_rand(0, 1);
            $y = mt_rand(0, 1);
            
            if (1 == $x && 1 == $y) {
                $output = 1;
            } else {
                $output = 0;
            }
            
            $this->perceptron->train([$x, $y], $output);
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
            
            $this->perceptron->train([$x, $y], $output);
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
            
            // Pick graph co-ordinates.
            $x = mt_rand(0, self::MAX_X_Y);
            $y = mt_rand(0, self::MAX_X_Y);
            
            // If point is below the line y = x then output should be -1.
            if ($y < $x) {
                $output = 0;
            } else {
                $output = 1;
            }
            
            $this->perceptron->train([$x, $y], $output);
        }
    }
}
