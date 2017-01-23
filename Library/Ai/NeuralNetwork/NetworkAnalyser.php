<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 8th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'AbstractAnalyser.php';

/**
 * Analyses neural networks as they undergo training to try and determine if
 * they are a failing or passing network.
 *
 * @author Tom Gray
 */
class NetworkAnalyser extends AbstractAnalyser
{
    
    /**
     * The network being analysed.
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
     * Construct a network analyser.
     * 
     * @param NetworkInterface $network
     * @param string $outputDir
     */
    public function __construct(NetworkInterface $network, $outputDir)
    {
        parent::__construct($outputDir);
        $this->network = $network;
        $this->previousWeights = $network->weights();
    }
    
    /**
     * @inheritDoc
     */
    public function generatePlots()
    {
        $this->generateAdjustmentsPlot($this->network->weights());
        $this->generateWeightsPlot($this->network->weights());
    }
    
    /**
     * @inheritDoc
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
}
