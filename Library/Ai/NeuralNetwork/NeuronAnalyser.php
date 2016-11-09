<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Wednesday 9th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'AbstractAnalyser.php';

/**
 * Analyses neurons as they undergo training to try and determine if they are a
 * failing or passing neuron.
 *
 * @author Tom Gray
 */
class NeuronAnalyser extends AbstractAnalyser
{
    
    /**
     * The neuron being analysed.
     * 
     * @var NeuronInterface
     */
    protected $neuron;
    
    /**
     * The last weights of the neuron that were recorded.
     * 
     * @var array
     */
    protected $previousWeights;
    
    /**
     * Construct a neuron analyser.
     * 
     * @param NeuronInterface $neuron
     * @param string $outputDir
     */
    public function __construct(NeuronInterface $neuron, $outputDir)
    {
        parent::__construct($outputDir);
        $this->neuron = $neuron;
        $this->previousWeights = $neuron->weightsWithBias();
    }
    
    /**
     * @inheritDoc
     */
    public function generatePlots()
    {
        $this->generateAdjustmentsPlot($this->neuron->weightsWithBias());
        $this->generateWeightsPlot($this->neuron->weightsWithBias());
    }
    
    /**
     * @inheritDoc
     */
    public function record($iteration)
    {
        $adjustments = $this->compareWeights(
            $this->previousWeights,
            $this->neuron->weightsWithBias()
        );
        $this->recordAdjustments($iteration, $adjustments);
        $this->recordWeights($iteration, $this->neuron->weightsWithBias());
    }
}
