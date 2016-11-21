<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Saturday 30th January 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Http;

/**
 * Defines constants for the HTTP response status codes.
 * 
 * @author Tom Gray
 */
interface ResponseStatusCode
{
    const SUCCESS = 200;
    const PERMANENTLY_MOVED = 301;
    const REDIRECT = 302;
    const NOT_FOUND = 404;
}
