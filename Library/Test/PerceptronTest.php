<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Friday 1st April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Test;

require_once '../Ai/NeuralNetwork/Perceptron.php';

use GreasyLab\Library\Ai\NeuralNetwork\Perceptron;

/**
 * Tests for the perceptron.
 *
 * @author Tom Gray
 */
class PerceptronTest extends \PHPUnit_Framework_TestCase
{
    
    const MAX_X_Y = 100;
    
    protected $perceptron;
    
    /**
     * Create a new perceptron with random weights.
     * 
     * @before
     */
    public function setUp()
    {
        $this->perceptron = new Perceptron(
            [
                mt_rand(-100, 100) / 100,
                mt_rand(-100, 100) / 100
            ]
        );
    }
    
    /**
     * @test
     */
    public function correctlyGuessesAllPointLocationsForStraightLine()
    {
        $this->trainPerceptronStraightLine(10000);
        $this->testPerceptronStraightLine();
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
    public function throwsExceptionWhenPointIsGuessedWrong()
    {
        $this->trainPerceptronStraightLine(100);
        $this->testPerceptronStraightLine();
    }
    
    /**
     * 
     * 
     * @throws \Exception
     */
    private function testPerceptronStraightLine()
    {
        for ($x = 0; $x <= self::MAX_X_Y; ++$x) {
            for ($y = 0; $y <= self::MAX_X_Y; ++$y) {
                
                // If point is below the line y = x then output should be -1.
                if ($y < $x) {
                    $output = -1;
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
     * Train the perceptron to guess whether points are above or below a
     * straight line.
     * 
     * @param int $iterations
     */
    private function trainPerceptronStraightLine($iterations)
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
            
            $this->perceptron->train([$x, $y], $output);
        }
    }
}
