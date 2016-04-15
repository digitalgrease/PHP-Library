<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Tuesday 5th April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'NetworkInterface.php';
require_once 'NeuronInterface.php';

/**
 * Provides shared functionality for a single neuron in an artificial neural
 * network.
 * 
 * @version 1.0
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
     * @param float $learningRate
     * @param float $bias
     */
    public function __construct(
        array $weights,
        $learningRate = self::DEFAULT_LEARNING_RATE,
        $bias = null
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
     * @return mixed
     */
    abstract protected function activationFunction($input);
    
    /**
     * Get the bias weight of this neuron.
     * 
     * @return float
     */
    public function bias()
    {
        return $this->bias;
    }
    
    /**
     * {@inheritdoc}
     */
    public function computeDelta($error)
    {
        return $error * $this->learningRate;
    }
    
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function train(array $inputs, $output)
    {
        $guess = $this->feedForward($inputs);
        $error = $output - $guess;
        
        $this->updateWeightsAndBias($inputs, $error);
    }
    
    /**
     * {@inheritdoc}
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
     * Get the weights of this neuron.
     * 
     * @return array
     */
    public function weights()
    {
        return $this->weights;
    }
    
    /**
     * Sum the weights of the inputs.
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