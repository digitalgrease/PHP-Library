<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Wednesday 9th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

/**
 * Provides methods to record the state and changes of neural networks as they
 * undergo training and methods to analyse the data to try and determine if they
 * are a failing or passing network.
 * 
 * @version 1.0 Wednesday 9th November 2016
 * @author Tom Gray
 */
class NetworkAnalyser
{
    
    const ADJUSTMENTS_DATA_FILENAME = 'adjustments.dat';
    
    const ADJUSTMENTS_PLOT_FILENAME = 'adjustments.p';
    
    const WEIGHTS_DATA_FILENAME = 'weights.dat';
    
    const WEIGHTS_PLOT_FILENAME = 'weights.p';
    
    /**
     * The neural network being analysed.
     * 
     * @var NetworkInterface
     */
    protected $network;
    
    /**
     * The last weights of the network that were recorded.
     * 
     * @var array
     */
    protected $previousWeights;
    
    /**
     * Directory to store the output in.
     * 
     * @var string
     */
    protected $outputDir;
    
    protected $adjustmentsDataFilePath;
    
    protected $adjustmentsPlotFilePath;
    
    protected $weightsDataFilePath;
    
    protected $weightsPlotFilePath;
    
    /**
     * Construct a network analyser.
     * 
     * @param NetworkInterface $network
     * @param string $outputDir
     */
    public function __construct(NetworkInterface $network, $outputDir)
    {
        $this->network = $network;
        $this->previousWeights = $network->weights();
        
        $this->outputDir = $outputDir;
        $this->adjustmentsDataFilePath = $outputDir
            . self::ADJUSTMENTS_DATA_FILENAME;
        $this->adjustmentsPlotFilePath = $outputDir
            . self::ADJUSTMENTS_PLOT_FILENAME;
        $this->weightsDataFilePath = $outputDir . self::WEIGHTS_DATA_FILENAME;
        $this->weightsPlotFilePath = $outputDir . self::WEIGHTS_PLOT_FILENAME;
    }
    
    /**
     * Analyse the gradients of the recorded adjustments and weights.
     */
    public function analyseGradients()
    {
        // DO TG 1 Implement: Create this method to analyse the gradients.
    }
    
    /**
     * Generate plots of the recorded data.
     * 
     * @return void
     */
    public function generatePlots()
    {
        $this->generateAdjustmentsPlot($this->network->weights());
        $this->generateWeightsPlot($this->network->weights());
    }
    
    /**
     * Record the current state of the network for analysis.
     * 
     * @param int $iteration
     * 
     * @return void
     */
    public function record($iteration)
    {
        $adjustments = $this->compareWeights(
            $this->previousWeights,
            $this->network->weights()
        );
        $this->recordAdjustments($iteration, $adjustments);
        $this->recordWeights($iteration, $this->network->weights());
    }
    
    /**
     * Compare two sets of weights and return the differences.
     * 
     * @param array $w1 The first and old weights.
     * @param array $w2 The new weights.
     * 
     * @return array
     */
    protected function compareWeights(array $w1, array $w2)
    {
        $differences = [];
        
        foreach ($w1 as $l => $neurons) {
            
            if (is_array($neurons)) {
                
                // Compare the weights of a network.
                foreach ($neurons as $n => $weights) {
                    foreach ($weights as $w => $weight) {

                        // Deducting the old weight from the new weight gives
                        // the difference and direction the weight changed in.
                        $differences[$l][$n][] = $w2[$l][$n][$w] - $weight;
                    }
                }
            } else {
                
                // Compare the weights of a single neuron.
                // Deducting the old weight from the new weight gives the
                // difference and direction the weight changed in.
                $differences[] = $w2[$l] - $neurons;
            }
        }
        
        return $differences;
    }
    
    /**
     * Generate the plot of the network adjustments.
     * 
     * @param array $weights
     * 
     * @return void
     */
    protected function generateAdjustmentsPlot(array $weights)
    {
        $script = 'set term png' . PHP_EOL
            . 'set output "' . $this->outputDir . 'adjustments.png"' . PHP_EOL
            . 'set title "Network Adjustments Over Iterations"' . PHP_EOL
            . 'set xlabel "Iterations"' . PHP_EOL
            . 'set ylabel "Adjustments"' . PHP_EOL
            . 'set grid' . PHP_EOL
            . 'plot ';
        
        foreach ($weights as $l => $layer) {
            
            if (is_array($layer)) {
                
                // Generating a plot of adjustments of a network.
                foreach ($layer as $n => $neuron) {
                    foreach ($neuron as $w => $weight) {
                        $script .= '"' . $this->adjustmentsDataFilePath
                            . $l . $n . $w . '" with linespoints, ';
                    }
                }
            } else {
                
                // Generating a plot of adjustments of a neuron.
                $script .= '"' . $this->adjustmentsDataFilePath . $l
                    . '" with linespoints, ';
            }
        }
        
        $script = substr($script, 0, -2);
        file_put_contents($this->adjustmentsPlotFilePath, $script);
        exec('gnuplot ' . $this->adjustmentsPlotFilePath);
    }
    
    /**
     * Generate the plot of the network weights.
     * 
     * @param array $weights
     * 
     * @return void
     */
    protected function generateWeightsPlot(array $weights)
    {
        $script = 'set term png' . PHP_EOL
            . 'set output "' . $this->outputDir . 'weights.png"' . PHP_EOL
            . 'set title "Network Weights Over Iterations"' . PHP_EOL
            . 'set xlabel "Iterations"' . PHP_EOL
            . 'set ylabel "Weights"' . PHP_EOL
            . 'set grid' . PHP_EOL
            . 'plot ';
        
        foreach ($weights as $l => $layer) {
            
            if (is_array($layer)) {
                
                // Generating a plot of weights of a network.
                foreach ($layer as $n => $neuron) {
                    foreach ($neuron as $w => $weight) {
                        $script .= '"' . $this->weightsDataFilePath
                            . $l . $n . $w . '" with linespoints, ';
                    }
                }
            } else {
                
                // Generating a plot of weights of a neuron.
                $script .= '"' . $this->weightsDataFilePath . $l
                    . '" with linespoints, ';
            }
        }
        
        $script = substr($script, 0, -2);
        file_put_contents($this->weightsPlotFilePath, $script);
        exec('gnuplot ' . $this->weightsPlotFilePath);
    }
    
    /**
     * Write the last weight adjustments to file.
     * 
     * @param int $iteration The current training iteration.
     * @param array $adjustments
     * 
     * @return void
     */
    protected function recordAdjustments($iteration, array $adjustments)
    {
        if ($iteration) {
            $mode = 'a';
        } else {
            $mode = 'w';
        }
        
        foreach ($adjustments as $l => $layer) {
            
            if (is_array($layer)) {
                
                // Record adjustments of a network.
                foreach ($layer as $n => $neuron) {
                    foreach ($neuron as $a => $adjustment) {
                        $file = fopen(
                            $this->adjustmentsDataFilePath . $l . $n . $a,
                            $mode
                        );
                        fwrite($file, $iteration . ' ' . $adjustment . PHP_EOL);
                        fclose($file);
                    }
                }
            } else {
                
                // Record adjustments of a neuron.
                $file = fopen(
                    $this->adjustmentsDataFilePath . $l,
                    $mode
                );
                fwrite($file, $iteration . ' ' . $layer . PHP_EOL);
                fclose($file);
            }
        }
    }
    
    /**
     * Write the current weights to file.
     * 
     * @param int $iteration The current training iteration.
     * @param array $weights
     * 
     * @return array The weights recorded.
     */
    protected function recordWeights($iteration, array $weights)
    {
        if ($iteration) {
            $mode = 'a';
        } else {
            $mode = 'w';
        }
        
        foreach ($weights as $l => $layer) {
            
            if (is_array($layer)) {
                
                // Record weights of a network.
                foreach ($layer as $n => $neuron) {
                    foreach ($neuron as $w => $weight) {
                        $file = fopen(
                            $this->weightsDataFilePath . $l . $n . $w,
                            $mode
                        );
                        fwrite($file, $iteration . ' ' . $weight . PHP_EOL);
                        fclose($file);
                    }
                }
            } else {
                
                // Record weights of a neuron.
                $file = fopen(
                    $this->weightsDataFilePath . $l,
                    $mode
                );
                fwrite($file, $iteration . ' ' . $layer . PHP_EOL);
                fclose($file);
            }
        }
        
        return $weights;
    }
}
