<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Version 1.0 Tuesday 5th April 2016
 * Version 2.0 Tuesday 24th January 2017
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'NetworkInterface.php';
require_once 'NeuronInterface.php';

/**
 * Provides shared functionality for a single neuron in an artificial neural
 * network.
 * 
 * @version 2.0 Tuesday 24th January 2017
 * @author Tom Gray
 */
abstract class Neuron implements NeuronInterface
{
    
    /**
     * Default learning rate for training a neuron.
     * 
     * @var float
     */
    const DEFAULT_LEARNING_RATE = 0.05;
    
    /**
     * Generate an array of weights between -1 and 1.
     * 
     * DO TG Improvement: Generate a set of weights with a mean of zero. Why is
     *  this recommended? Does that generally include the bias weight too?
     * 
     * @param int $nWeights The number of weights to generate.
     * 
     * @return array
     */
    public static function generateWeights($nWeights)
    {
        for ($i = 0; $i < $nWeights; ++$i) {
            $weights[] = mt_rand(-100, 100) / 100;
        }
        return $weights;
    }
    
    /**
     * The bias weight.
     * 
     * @var float
     */
    protected $bias;
    
    /**
     * Value that controls the learning rate. A high value will change the
     * weights more drastically and may result in a solution faster but may
     * also overshoot the optimal weights. A lower value will adjust the weights
     * slower and require more training time but may improve the network's
     * accuracy.
     * 
     * @var float
     */
    protected $learningRate;
    
    /**
     * Multiplying the inputs by these weights gives the strengths of the
     * received signals.
     * 
     * @var array
     */
    protected $weights;
    
    /**
     * Construct a neuron with weights.
     * 
     * @param array $weights
     * @param float $bias
     * @param float $learningRate
     */
    public function __construct(
        array $weights,
        $bias = null,
        $learningRate = self::DEFAULT_LEARNING_RATE
    ) {
        $this->weights = $weights;
        $this->learningRate = $learningRate;
        if ($bias) {
            $this->bias = $bias;
        } else {
            $this->bias = mt_rand(-100, 100) / 100;
        }
    }
    
    /**
     * Provide a string representation of this neuron.
     * 
     * @return string
     */
    public function __toString()
    {
        return 'Weights => ' . implode(',', $this->weights)
            . ' / Bias => ' . $this->bias;
    }
    
    /**
     * The neuron's activation function.
     * 
     * @param float $input
     * 
     * @return array
     */
    abstract protected function activationFunction($input);
    
    /**
     * @inheritDoc
     */
    public function bias()
    {
        return $this->bias;
    }
    
    /**
     * @inheritDoc
     */
    public function computeDelta($error)
    {
        return $error * $this->learningRate;
    }
    
    /**
     * @inheritDoc
     */
    public function feedForward(array $inputs)
    {
        if (count($this->weights) == count($inputs)) {
            return $this->activationFunction($this->sumWeights($inputs));
        } else {
            throw new \Exception(
                'The number of inputs does not match the number of weights.'
            );
        }
    }
    
    /**
     * @inheritDoc
     */
    public function train(array $inputs, array $outputs)
    {
        $guess = $this->feedForward($inputs);
        $error = $outputs[0] - $guess[0];
        
        $this->updateWeightsAndBias($inputs, $error);
    }
    
    /**
     * @inheritDoc
     */
    public function updateWeightsAndBias(array $inputs, $error)
    {
        $delta = $error * $this->learningRate;
        $this->bias += $delta;
        for ($i = 0; $i < count($this->weights); ++$i) {
            $this->weights[$i] += $inputs[$i] * $delta;
        }
        return $delta;
    }
    
    /**
     * @inheritDoc
     */
    public function weights()
    {
        $weights = $this->weights;
        $weights[] = $this->bias;
        return $weights;
    }
    
    /**
     * @inheritDoc
     */
    public function weightsWithoutBias()
    {
        return $this->weights;
    }
    
    /**
     * Sum the weights of the inputs, including the bias.
     * 
     * @param array $inputs
     * 
     * @return float
     */
    protected function sumWeights(array $inputs)
    {
        $sum = $this->bias;
        $i = 0;
        foreach ($inputs as $input) {
            $sum += $this->weights[$i++] * $input;
        }
        return $sum;
    }
}
