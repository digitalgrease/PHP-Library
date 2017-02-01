<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Wednesday 9th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'DigitalGrease/Library/Files/LocalFileSystem.php';

use DigitalGrease\Library\Files\LocalFileSystem;

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
     * 
     *
     * @var FileSystemInterface
     */
    protected $fileSystem;

    /**
     * The neural network being analysed.
     * 
     * @var NetworkInterface
     */
    protected $network;
    
    /**
     * Directory to store the output in.
     * 
     * @var string
     */
    protected $outputDir;
    
    /**
     * Flag that defines whether to overwrite existing data in the output
     * directory or append to it.
     * 
     * @var boolean
     */
    protected $overwrite;
    
    /**
     * 
     * 
     * @var int
     */
    protected $currentIteration = 0;
    
    /**
     * The last weights of the network that were recorded.
     * 
     * @var array
     */
    protected $previousWeights;
    
    protected $adjustmentsDataFilePath;
    
    protected $adjustmentsPlotFilePath;
    
    protected $weightsDataFilePath;
    
    protected $weightsPlotFilePath;
    
    /**
     * DO TG Comment
     * 
     * @param string $inputDir
     * 
     * @return NetworkAnalyser|null
     */
    public static function createFromData($inputDir, $overwrite = false)
    {
        $analyser = null;
        $weights = self::readWeightsFromData($inputDir);
        
        if ($weights) {
            $network = Network::createFromWeights($weights);
            $analyser = new NetworkAnalyser($network, $inputDir, $overwrite);
        }
        
        return $analyser;
    }
    
    /**
     * DO TG Comment
     * 
     * @param string $inputDir
     * 
     * @return array
     */
    public static function readWeightsFromData($inputDir)
    {
        $fileSystem = new LocalFileSystem();
        
        $weights = [];
        
        $weightFilenameLength = strlen(self::WEIGHTS_DATA_FILENAME);
        
        foreach ($fileSystem->getFileList($inputDir) as $fileName) {
            if (strstr($fileName, self::WEIGHTS_DATA_FILENAME)) {
                $i = substr($fileName, $weightFilenameLength);
                $weight = $fileSystem->readFromEof($inputDir . $fileName, ' ');
                $weights[$i[0]][$i[1]][$i[2]] = $weight;
            }
        }
        
        return $weights;
    }
    
    /**
     * Construct a network analyser.
     * 
     * @param NetworkInterface $network
     * @param string $outputDir
     * @param boolean $overwrite
     */
    public function __construct(
        NetworkInterface $network,
        $outputDir,
        $overwrite = true
    ) {
        $this->network = $network;
        $this->previousWeights = $network->weights();
        
        $this->fileSystem = new LocalFileSystem();
        $this->outputDir = $outputDir;
        $this->overwrite = $overwrite;
        
        $this->adjustmentsDataFilePath = $outputDir
            . self::ADJUSTMENTS_DATA_FILENAME;
        $this->adjustmentsPlotFilePath = $outputDir
            . self::ADJUSTMENTS_PLOT_FILENAME;
        $this->weightsDataFilePath = $outputDir . self::WEIGHTS_DATA_FILENAME;
        $this->weightsPlotFilePath = $outputDir . self::WEIGHTS_PLOT_FILENAME;
        
        $this->initialiseCurrentIteration();
        
        // Record starting weights.
        if (0 == $this->currentIteration) {
            $this->removeDataFiles();
            $this->recordData(
                $this->network->weights(),
                $this->weightsDataFilePath
            );
        }
    }
    
    /**
     * Analyse the gradients of the recorded adjustments and weights.
     * 
     * @return boolean True if the network is deemed as accurate, false if not?
     */
    public function analyseGradients()
    {
        // DO TG 1 Implement: Create this method to analyse the gradients.
        // Analyse gradients from the beginning to see how training is
        // progressing?
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
     * Get the network being analysed.
     * 
     * @return NetworkInterface
     */
    public function getNetwork()
    {
        return $this->network;
    }
    
    /**
     * Record the current state of the network for analysis.
     * 
     * @return void
     */
    public function record()
    {
        ++$this->currentIteration;
        
        $adjustments = $this->compareWeights(
            $this->previousWeights,
            $this->network->weights()
        );
        
        $this->recordData($adjustments, $this->adjustmentsDataFilePath);
        $this->recordData(
            $this->network->weights(),
            $this->weightsDataFilePath
        );
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
     * DO TG Comment
     */
    protected final function initialiseCurrentIteration()
    {
        $filePath = $this->weightsDataFilePath . '000';
        if (!$this->overwrite && $this->fileSystem->isFile($filePath)) {
            $lastLine = $this->fileSystem->readFromEof($filePath);
            $this->currentIteration = strstr(
                $lastLine, ' ',
                $before_needle = true
            );
        }
    }
    
    /**
     * DO TG Comment
     * 
     * @param array $data
     * @param string $filePath
     * 
     * @return void
     */
    protected function recordData(array $data, $filePath)
    {
        foreach ($data as $l => $layer) {
            
            if (is_array($layer)) {
                
                // Record weights of a multiple neuron network.
                foreach ($layer as $n => $neuron) {
                    foreach ($neuron as $w => $weight) {
                        $file = fopen($filePath . $l . $n . $w, 'a');
                        fwrite(
                            $file,
                            $this->currentIteration . ' ' . $weight . PHP_EOL
                        );
                        fclose($file);
                    }
                }
            } else {
                
                // Record weights of a single neuron network.
                $file = fopen($filePath . $l, 'a');
                fwrite($file, $this->currentIteration . ' ' . $layer . PHP_EOL);
                fclose($file);
            }
        }
    }
    
    /**
     * DO TG Comment
     * 
     * @return void
     */
    protected function removeDataFiles()
    {
        if ($this->fileSystem->isFile($this->adjustmentsDataFilePath)) {
            $this->fileSystem->unlink($this->adjustmentsDataFilePath);
        }
        if ($this->fileSystem->isFile($this->weightsDataFilePath)) {
            $this->fileSystem->unlink($this->weightsDataFilePath);
        }
    }
}
