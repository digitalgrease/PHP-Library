<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Saturday 30th January 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Http;

require_once 'ResponseStatusCode.php';
require_once 'Url.php';

use DigitalGrease\Library\Http\ResponseStatusCode;
use DigitalGrease\Library\Http\Url;

/**
 * Represents an HTTP response.
 *
 * @author Tom Gray
 */
class Response
{
    /**
     * Response body.
     * 
     * @var string
     */
    protected $body;
    
    /**
     * Response header.
     * 
     * @var string
     */
    protected $header;
    
    /**
     * URL to redirect to if included in the response.
     * 
     * @var URL
     */
    protected $redirectUrl;
    
    /**
     * Response status code.
     * 
     * @var int
     */
    protected $status;
    
    /**
     * Construct an HTTP response object.
     * 
     * @param string $header
     * @param string $body
     * @param string $status
     * @param Url $redirectUrl
     */
    public function __construct(
        $header,
        $body,
        $status,
        Url $redirectUrl
    ) {
        $this->header = $header;
        $this->body = $body;
        $this->status = $status;
        $this->redirectUrl = $redirectUrl;
    }
    
    /**
     * 
     * @return string
     */
    public function body()
    {
        return $this->body;
    }
    
    /**
     * 
     * @return string
     */
    public function header()
    {
        return $this->header;
    }
    
    /**
     * 
     * @return bool
     */
    public function isRedirect()
    {
        switch ($this->status) {
            case ResponseStatusCode::REDIRECT:
            case ResponseStatusCode::PERMANENTLY_MOVED:
                return true;
            default:
                return false;
        }
    }
    
    /**
     * 
     * @return string
     */
    public function redirectUrl()
    {
        return $this->redirectUrl;
    }
    
    /**
     * 
     * @return int
     */
    public function status()
    {
        return $this->status;
    }
}
