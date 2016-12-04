<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Monday 28th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Instagram;

/**
 * An Instagram post.
 *
 * @author Tom Gray
 */
class Post
{
    
    protected $id;
    
    protected $date;
    
    protected $caption;
    
    protected $thumbnailUrl;
    
    protected $mediaUrl;
    
    protected $isVideo;
    
    protected $height;
    
    protected $width;
    
    public function __construct(
        $id,
        $date,
        $caption,
        $thumbnailUrl,
        $mediaUrl,
        $isVideo,
        $width,
        $height
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->caption = $caption;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->mediaUrl = $mediaUrl;
        $this->isVideo = $isVideo;
        $this->height = $height;
        $this->width = $width;
    }
    
    public function caption()
    {
        return $this->caption;
    }
    
    public function height()
    {
        return $this->height;
    }
    
    public function thumbnailUrl()
    {
        return $this->thumbnailUrl;
    }
    
    public function url()
    {
        return $this->mediaUrl;
    }
    
    public function width()
    {
        return $this->width;
    }
}
