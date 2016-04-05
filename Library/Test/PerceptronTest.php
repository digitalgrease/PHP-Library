<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 1st April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test;

require_once '../Ai/NeuralNetwork/Neuron.php';
require_once '../Ai/NeuralNetwork/SimplePerceptron.php';

use GreasyLab\Library\Ai\NeuralNetwork\Neuron;
use GreasyLab\Library\Ai\NeuralNetwork\SimplePerceptron;

/**
 * Tests for the perceptrons.
 *
 * @author Tom Gray
 */
class PerceptronTest extends \PHPUnit_Framework_TestCase
{
    
    const MAX_X_Y = 100;
    
    protected $simplePerceptron;
    
    /**
     * Create the perceptrons with random weights.
     * 
     * @before
     */
    public function setUp()
    {
        $this->simplePerceptron = new SimplePerceptron(
            Neuron::generateWeights(2)
        );
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAllPointLocationsForStraightLine()
    {
        $this->trainStraightLine(100000);
        $this->testStraightLine();
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAndOfTwoInputs()
    {
        $this->trainAnd(10000);
        $this->testAnd();
    }
    
    /**
     * @test
     */
    public function correctlyGuessesOrOfTwoInputs()
    {
        $this->trainOr(10000);
        $this->testOr();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenAndOfTwoInputsIsWrong()
    {
        $this->trainAnd(1);
        $this->testAnd();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenOrOfTwoInputsIsWrong()
    {
        $this->trainOr(1);
        $this->testOr();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenPointLocationForStraightLineIsWrong()
    {
        $this->trainStraightLine(1);
        $this->testStraightLine();
    }
    
    /**
     * 
     * @throws \Exception
     */
    private function testAnd()
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x && 1 == $y) {
                    $output = 1;
                } else {
                    $output = -1;
                }
                
                if ($this->simplePerceptron->feedForward([$x, $y]) != $output) {
                    throw new \Exception('Failed for input ('.$x.','.$y.')');
                }
            }
        }
    }
    
    /**
     * 
     * @throws \Exception
     */
    private function testOr()
    {
        for ($x = 0; $x < 2; ++$x) {
            for ($y = 0; $y < 2; ++$y) {
                if (1 == $x || 1 == $y) {
                    $output = 1;
                } else {
                    $output = -1;
                }
                
                if ($this->simplePerceptron->feedForward([$x, $y]) != $output) {
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
    private function testStraightLine()
    {
        for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
            for ($y = 0; $y <= self::MAX_X_Y; ++$y) {
                
                // If point is below the line y = x then output should be -1.
                if ($y < $x) {
                    $output = -1;
                } else {
                    $output = 1;
                }
                
                if ($this->simplePerceptron->feedForward([$x, $y]) != $output) {
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
                $output = -1;
            }
            
            $this->simplePerceptron->train([$x, $y], $output);
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
                $output = -1;
            }
            
            $this->simplePerceptron->train([$x, $y], $output);
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
                $output = -1;
            } else {
                $output = 1;
            }
            
            $this->simplePerceptron->train([$x, $y], $output);
        }
    }
}
