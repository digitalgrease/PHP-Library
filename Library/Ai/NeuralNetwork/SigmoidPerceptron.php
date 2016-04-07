<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Tuesday 5th April 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Ai\NeuralNetwork;

require_once 'GreasyLab/Library/Utils/MathUtils.php';
require_once 'Neuron.php';

use GreasyLab\Library\Utils\MathUtils;

/**
 * Implements a perceptron that uses a sigmoid function for the activation
 * function.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class SigmoidPerceptron extends Neuron
{
    
    /**
     * {@inheritdoc}
     */
    protected function activationFunction($input)
    {
        $output = MathUtils::sigmoid($input);
        $gradient = MathUtils::sigmoidDerivative($output);
        
        $this->learningConstant = max([$gradient, self::LEARNING_CONSTANT]);
        
        return $output;
    }
}
