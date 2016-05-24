<?php

/*
 * Copyright (c) 2016 Greasy Lab.
 * 
 * Tuesday 24th May 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Games;

/**
 * Represents a collection of dice to be rolled in a game.
 *
 * @author Tom Gray
 */
class Dice
{
    
    /**
     * The number of dice in this collection.
     * 
     * @var int
     */
    protected $nDice;
    
    /**
     * The number of sides each die in this collection has.
     * 
     * @var int
     */
    protected $nSides;
    
    /**
     * The last values rolled on the dice.
     * 
     * @var array
     */
    protected $values;
    
    /**
     * The sum of the last values rolled on the dice.
     * 
     * @var int
     */
    protected $sum;
    
    /**
     * The single highest value that was last rolled.
     * 
     * @var int
     */
    protected $max;
    
    /**
     * The single lowest value that was last rolled.
     * 
     * @var int
     */
    protected $min;
    
    /**
     * Construct a collection of dice.
     * 
     * @param int $nDice
     * @param int $nSides
     */
    public function __construct($nDice, $nSides)
    {
        $this->nDice = $nDice;
        $this->nSides = $nSides;
    }
    
    /**
     * Roll the dice and return the sum of the values rolled.
     * 
     * @return int
     */
    public function roll()
    {
        $this->resetValues();
        
        for ($i = 0; $i < $this->nDice; ++$i) {
            $this->sum += $this->values[$i] = $val = mt_rand(1, $this->nSides);
            if ($val > $this->max) {
                $this->max = $val;
            }
            if ($val < $this->min) {
                $this->min = $val;
            }
        }
        return $this->sum;
    }
    
    /**
     * Get the single highest value that was last rolled.
     * 
     * @return int
     */
    public function highestValueRolled()
    {
        return $this->max;
    }
    
    /**
     * Get the single lowest value that was last rolled.
     * 
     * @return int
     */
    public function lowestValueRolled()
    {
        return $this->min;
    }
    
    /**
     * Reset the stored values of the last roll.
     * 
     * @return void
     */
    protected function resetValues()
    {
        $this->values = [];
        $this->sum = 0;
        $this->min = $this->nSides;
        $this->max = 0;
    }
}
