<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 8th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

/**
 * Analyses neural networks as they undergo training to try and determine if
 * they are a failing or passing network.
 *
 * @author Tom Gray
 */
class NetworkAnalyser
{
    // DO TG *
    const WEIGHTS_DATA_FILENAME = 'weights.dat';
    
    const ADJUSTMENTS_DATA_FILENAME = 'adjustments.dat';
    
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
    public function recordWeights($iteration, NetworkInterface $network)
    {
        if ($iteration) {
            $mode = 'a';
        } else {
            $mode = 'w';
        }
        
        foreach ($network->weights() as $l => $layer) {
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
        
        return $network->weights();
    }
}
