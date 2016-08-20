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
 * DO TG * Analyse the data to establish pattern for failing and passing
 *  networks.
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
    
    const NUM_OF_WEIGHTS_TO_COMPARE = 10;
    
    /**
     * Defines the accuracy required for the network to pass the tests.
     * 
     * @var float
     */
    const THRESHOLD = 0.01;
    
    const WEIGHTS_DATA_FILENAME = 'weights.dat';
    
    const ADJUSTMENTS_DATA_FILENAME = 'adjustments.dat';
    
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
//        $this->trainAnd(self::MAX_NUM_OF_TRAINING_ITERATIONS);
//        $this->runAnd(self::THRESHOLD);
//        $this->trainOr(self::MAX_NUM_OF_TRAINING_ITERATIONS);
//        $this->runOr(self::THRESHOLD);
//        $this->trainStraightLine(self::MAX_NUM_OF_TRAINING_ITERATIONS);
//        $this->runStraightLine(self::THRESHOLD);
        $this->trainXor(self::MAX_NUM_OF_TRAINING_ITERATIONS);
        $this->runXor(self::THRESHOLD);
    }
    
    /**
     * @test
     * @expectedException \Exception
     */
//    public function throwsExceptionWhenXorOfTwoInputsIsWrong()
//    {
//        $this->trainXor(1);
//        $this->runXor(self::THRESHOLD);
//    }
    
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
     * Generate the plot of the network adjustments.
     * 
     * @return void
     */
    private function generateAdjustmentsPlot()
    {
        $script = 'set term png' . PHP_EOL
            . 'set output "adjustments.png"' . PHP_EOL
            . 'set title "Network Adjustments Over Iterations"' . PHP_EOL
            . 'set xlabel "Iterations"' . PHP_EOL
            . 'set ylabel "Adjustments"' . PHP_EOL
            . 'set grid' . PHP_EOL
            . 'plot ';
        foreach ($this->network->weights() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                foreach ($neuron as $w => $weight) {
                    $script .= '"' . self::ADJUSTMENTS_DATA_FILENAME
                        . $l . $n . $w . '" with linespoints, ';
                }
            }
        }
        $script = substr($script, 0, -2);
        file_put_contents('adjustments.p', $script);
        exec('gnuplot adjustments.p');
    }
    
    /**
     * Generate the plot of the network weights.
     * 
     * @return void
     */
    private function generateWeightsPlot()
    {
        $script = 'set term png' . PHP_EOL
            . 'set output "weights.png"' . PHP_EOL
            . 'set title "Network Weights Over Iterations"' . PHP_EOL
            . 'set xlabel "Iterations"' . PHP_EOL
            . 'set ylabel "Weights"' . PHP_EOL
            . 'set grid' . PHP_EOL
            . 'plot ';
        foreach ($this->network->weights() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                foreach ($neuron as $w => $weight) {
                    $script .= '"' . self::WEIGHTS_DATA_FILENAME . $l . $n . $w
                        . '" with linespoints, ';
                }
            }
        }
        $script = substr($script, 0, -2);
        file_put_contents('weights.p', $script);
        exec('gnuplot weights.p');
    }
    
    /**
     * Write the last weight adjustments of the network to file.
     * 
     * @param int $iteration
     * @param array $oldWeights
     * @param array $newWeights
     * 
     * @return void
     */
    private function recordAdjustments(
        $iteration,
        array $oldWeights,
        array $newWeights
    ) {
        if ($iteration) {
            $mode = 'a';
        } else {
            $mode = 'w';
        }
        
        $adjustments = $this->compareWeights($oldWeights, $newWeights);
        foreach ($adjustments as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                foreach ($neuron as $a => $adjustment) {
                    $file = fopen(
                        self::ADJUSTMENTS_DATA_FILENAME . $l . $n . $a,
                        $mode
                    );
                    fwrite($file, $iteration . ' ' . $adjustment . PHP_EOL);
                    fclose($file);
                }
            }
        }
    }
    
    /**
     * Write the current weights of the network to file.
     * 
     * @param int $iteration The current training iteration.
     * 
     * @return array The weights recorded.
     */
    private function recordWeights($iteration)
    {
        if ($iteration) {
            $mode = 'a';
        } else {
            $mode = 'w';
        }
        
        foreach ($this->network->weights() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                foreach ($neuron as $w => $weight) {
                    $file = fopen(
                        self::WEIGHTS_DATA_FILENAME . $l . $n . $w,
                        $mode
                    );
                    fwrite($file, $iteration . ' ' . $weight . PHP_EOL);
                    fclose($file);
                }
            }
        }
        
        return $this->network->weights();
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
        $oldWeights = $this->recordWeights($i);
        
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
            $newWeights = $this->recordWeights(++$i);
            $this->recordAdjustments($i, $oldWeights, $newWeights);
            $oldWeights = $newWeights;
        }
        
        $this->generateAdjustmentsPlot();
        $this->generateWeightsPlot();
    }
    
    /**
     * Train the neuron to learn the outcome of OR.
     * 
     * @param int $iterations
     */
    private function trainOr($iterations)
    {
        $i = 0;
        $oldWeights = $this->recordWeights($i);
        
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
            $newWeights = $this->recordWeights(++$i);
            $this->recordAdjustments($i, $oldWeights, $newWeights);
            $oldWeights = $newWeights;
        }
        
        $this->generateAdjustmentsPlot();
        $this->generateWeightsPlot();
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
        $oldWeights = $this->recordWeights($i);
        
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
            $newWeights = $this->recordWeights(++$i);
            $this->recordAdjustments($i, $oldWeights, $newWeights);
            $oldWeights = $newWeights;
        }
        
        $this->generateAdjustmentsPlot();
        $this->generateWeightsPlot();
    }
    
    /**
     * Train the network to learn the outcome of XOR.
     * 
     * @param int $iterations
     */
    private function trainXor($iterations)
    {
        $i = 0;
        $oldWeights = $this->recordWeights($i);
        
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
            $newWeights = $this->recordWeights(++$i);
            $this->recordAdjustments($i, $oldWeights, $newWeights);
            $oldWeights = $newWeights;
        }
        
        $this->generateAdjustmentsPlot();
        $this->generateWeightsPlot();
    }
    
    /**
     * Compare two sets of network weights and return the differences.
     * 
     * @param array $w1 The first and old network weights.
     * @param array $w2 The new network weights.
     * 
     * @return array
     */
    private function compareWeights(array $w1, array $w2)
    {
        $differences = [];
        
        foreach ($w1 as $l => $neurons) {
            foreach ($neurons as $n => $weights) {
                foreach ($weights as $w => $weight) {
                    
                    // Deducting the old weight from the new weight gives the
                    // difference and direction the weight changed in.
                    $differences[$l][$n][] = $w2[$l][$n][$w] - $weight;
                }
            }
        }
        
        return $differences;
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
