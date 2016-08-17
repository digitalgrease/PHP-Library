<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Saturday 30th January 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Http;

/**
 * Defines constants for the HTTP response status codes.
 * 
 * @author Tom Gray
 */
interface ResponseStatusCode
{
    const REDIRECT = 302;
    const PERMANENTLY_MOVED = 301;
}
