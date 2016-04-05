<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Wednesday 16th March 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'NetworkInterface.php';
require_once 'NeuronInterface.php';

/**
 * Provides basic functionality for a single perceptron in an artificial neural
 * network.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class Perceptron implements NeuronInterface, NetworkInterface
{
    
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
    protected $learningConstant;
    
    /**
     * Multiplying the inputs by these weights gives the strengths of the
     * received signals.
     * 
     * @var array
     */
    protected $weights;
    
    /**
     * Construct the perceptron with the supplied weights.
     * 
     * @param array $weights
     * @param float $learningConstant
     * @param float $bias
     */
    public function __construct(
        array $weights,
        $learningConstant = 0.05,
        $bias = null
    ) {
        $this->weights = $weights;
        $this->learningConstant = $learningConstant;
        if ($bias) {
            $this->bias = $bias;
        } else {
            $this->bias = mt_rand(-100, 100) / 100;
        }
    }
    
    /**
     * Get the bias weight of this perceptron.
     * 
     * @return float
     */
    public function bias()
    {
        return $this->bias;
    }
    
    /**
     * Pass a collection of inputs through this perceptron.
     * 
     * @param array $inputs
     * 
     * @return mixed
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
        $delta = $error * $this->learningConstant;
        
        $this->updateWeightsAndBias($inputs, $delta);
        
        return $error == 0;
    }
    
    /**
     * {@inheritdoc}
     */
    public function updateWeightsAndBias(array $inputs, $delta)
    {
        $this->bias += $delta;
        for ($i = 0; $i < count($this->weights); ++$i) {
            $this->weights[$i] += $inputs[$i] * $delta;
        }
    }
    
    /**
     * Get the weights of this perceptron.
     * 
     * @return array
     */
    public function weights()
    {
        return $this->weights;
    }
    
    /**
     * The perceptron's activation function.
     * 
     * @param float $input
     * 
     * @return mixed
     */
    protected function activationFunction($input)
    {
        if ($input > 0) {
            return 1;
        } else {
            return -1;
        }
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
