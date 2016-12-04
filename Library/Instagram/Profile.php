<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Monday 28th November 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Instagram;

require_once 'Post.php';

/**
 * An Instagram user profile.
 *
 * @author Tom Gray
 */
class Profile
{
    
    protected $id;
    
    protected $username;
    
    protected $name;
    
    protected $biography;
    
    protected $facebookPage;
    
    protected $numberFollowedByUser;
    
    protected $numberFollowingUser;
    
    protected $externalUrlLinked;
    
    protected $profilePictureUrl;
    
    protected $posts;
    
    public function __construct(
        $id,
        $username,
        $name,
        $biography,
        $facebookPage,
        $numberFollowedByUser,
        $numberFollowingUser,
        $externalUrlLinked,
        $profilePictureUrl
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->name = $name;
        $this->biography = $biography;
        $this->facebookPage = $facebookPage;
        $this->numberFollowedByUser = $numberFollowedByUser;
        $this->numberFollowingUser = $numberFollowingUser;
        $this->externalUrlLinked = $externalUrlLinked;
        $this->profilePictureUrl = $profilePictureUrl;
    }
    
    public function addPost(Post $post)
    {
        $this->posts[] = $post;
    }
    
    public function numberOfPosts()
    {
        return count($this->posts);
    }
    
    public function post($iPost)
    {
        return $this->posts[$iPost];
    }
    
    public function posts()
    {
        return $this->posts;
    }
}
