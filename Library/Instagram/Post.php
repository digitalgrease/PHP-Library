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
    
    public function __construct(
        $id,
        $date,
        $caption,
        $thumbnailUrl,
        $mediaUrl,
        $isVideo
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->caption = $caption;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->mediaUrl = $mediaUrl;
        $this->isVideo = $isVideo;
    }
    
    public function url()
    {
        return $this->mediaUrl;
    }
}
