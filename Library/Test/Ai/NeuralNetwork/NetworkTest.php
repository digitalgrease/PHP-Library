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
 * DO TG * Complete a test class for Network which solves XOR.
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
        for ($i = 0; $i < $iterations; ++$i) {
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
}
