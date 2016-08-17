<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Friday 10th June 2016
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Utils;

/**
 * Retrieves current PHP configuration values.
 *
 * @author Tom Gray
 */
class PhpSettings
{
    
    /**
     * The PHP working memory limit in MB as defined in php.ini.
     * 
     * @var int
     */
    protected $memoryLimit;
    
    /**
     * Maximum size that can be POSTed to the server in MB as defined in
     * php.ini.
     * 
     * @var int
     */
    protected $postMaxSize;
    
    /**
     * Maximum file size that can be uploaded in MB as defined in php.ini.
     * 
     * @var int
     */
    protected $uploadMaxFilesize;
    
    /**
     * Construct a settings object.
     */
    public function __construct()
    {
        $this->memoryLimit = (int)(ini_get('memory_limit'));
        $this->uploadMaxFilesize = (int)(ini_get('upload_max_filesize'));
        $this->postMaxSize = (int)(ini_get('post_max_size'));
    }
    
    /**
     * Get the maximum possible size of a file that can be uploaded based on the
     * PHP settings.
     * IMPORTANT: The true maximum size may be restricted by other server
     * settings or factors.
     * 
     * @return int
     */
    public function getMaxFileUploadSize()
    {
        return min(
            $this->uploadMaxFilesize,
            $this->postMaxSize,
            $this->memoryLimit
        );
    }
}
