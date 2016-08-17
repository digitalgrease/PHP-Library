<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Thursday 28th January 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Http;

/**
 * Represents a URL.
 *
 * @author Tom Gray
 */
class Url
{
    /**
     *
     * @var string
     */
    protected $domain;
    
    /**
     *
     * @var string
     */
    protected $path;
    
    /**
     * The protocol of the URL.
     * 
     * @var string
     */
    protected $protocol;
    
    /**
     * 
     * @param string $url
     */
    public function __construct($url)
    {
        // Extract and remove any protocol from the URL string.
        preg_match('/^[\w]+\:\/+/', $url, $protocol);
        $this->protocol = $protocol ? $protocol[0] : '';
        $url = substr($url, strlen($this->protocol));
        if (!$this->protocol) {
            $this->protocol = 'http://';
        }
        
        // Extract any domain.
        $isNoPathElement = strpos($url, '/') == false;
        $isDomainBeforePathElement = strpos($url, '.') < strpos($url, '/');
        if ($isNoPathElement || $isDomainBeforePathElement) {
            preg_match('/[-\w]+\.[-\w]+(\.[-\w]+)*/', $url, $domain);
            $this->domain = $domain ? $domain[0] : '';
            $url = substr($url, strlen($this->domain));
        }
        
        // Extract any path.
        $this->path = $url;
    }
    
    /**
     * 
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }
    
    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return !$this->domain;
    }
    
    /**
     * @return bool
     */
    public function isRelative()
    {
        return $this->domain == '';
    }
    
    /**
     * 
     * @return string
     */
    public function path()
    {
        return $this->path;
    }
    
    /**
     * 
     * @return string
     */
    public function protocol()
    {
        return $this->protocol;
    }
    
    /**
     * Set the URL domain.
     * 
     * @param string $domain
     * 
     * @return Url This URL to allow method chaining.
     */
    public function setDomain($domain)
    {
        // DO TG Improvement: Validate the domain with a regex.
        var_dump($domain);
        $this->domain = $domain;
        var_dump($this->domain);
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function url()
    {
        return $this->protocol . $this->domain . $this->path;
    }
    
    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->url();
    }
}
