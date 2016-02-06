<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tom Gray
 * 
 * Date: 9th October 2015
 */

namespace GreasyLab\Library\Data;

/**
 * API for data cleansers.
 * 
 * @version 1.0 9th October 2015
 * @author Tom Gray
 * @copyright 2015 Greasy Lab
 */
interface DataCleanserInterface
{
    /**
     * 
     * 
     * @param mixed $data
     * 
     * @return mixed
     */
    public function cleanse($data);
}
