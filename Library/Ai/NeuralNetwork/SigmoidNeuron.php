<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 5th April 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Ai\NeuralNetwork;

require_once 'DigitalGrease/Library/Utils/MathUtils.php';
require_once 'Neuron.php';

use DigitalGrease\Library\Utils\MathUtils;

/**
 * Implements a sigmoid neuron.
 * 
 * @version 1.0
 * @author Tom Gray
 */
class SigmoidNeuron extends Neuron
{
    
    /**
     * {@inheritdoc}
     */
    protected function activationFunction($input)
    {
        $output = MathUtils::sigmoid($input);
        $gradient = MathUtils::sigmoidDerivative($output);
        
        $this->learningRate = max([$gradient, self::DEFAULT_LEARNING_RATE]);
        
        return $output;
    }
}
