<?php

/*
 * Copyright (c) 2016 Digital Grease Limited.
 * 
 * Tuesday 25th October 2016
 * 
 * Tom Gray
 */

namespace DigitalGrease\Library\Instagram;

require_once 'DigitalGrease/Library/Http/Request.php';
require_once 'DigitalGrease/Library/Http/Response.php';
require_once 'DigitalGrease/Library/Http/ResponseStatusCode.php';
require_once 'DigitalGrease/Library/Http/Url.php';
require_once 'DigitalGrease/Library/Utils/StringUtils.php';
require_once 'Post.php';
require_once 'Profile.php';

use DigitalGrease\Library\Http\Request;
use DigitalGrease\Library\Http\Response;
use DigitalGrease\Library\Http\ResponseStatusCode;
use DigitalGrease\Library\Http\Url;
use DigitalGrease\Library\Utils\StringUtils;

/**
 * Download an Instagram user's profile data and images.
 * 
 * DO TG Bug Fix: The last 12 image links are downloaded twice!
 * DO TG Improvement: Select the correct javascript block with the required JSON
 *  programmatically.
 * DO TG Improvement: Ensure that the data has been acquired correctly before
 *  overwriting any existing data to ensure no data loss.
 * 
 * @author Tom Gray
 */
class InstagramDownloader
{
    
    const URL = 'https://www.instagram.com/';
    
    const QUERY_URL = 'https://www.instagram.com/query/';
    
    protected $profile;
    
    protected $error;
    
    protected $username;
    
    public function __construct()
    {
        $this->error = '';
    }
    
    public function error()
    {
        return $this->error;
    }
    
    public function getProfile($username)
    {
        $this->username = $username;
        $this->profile = null;
        
        $url = new Url(self::URL . $this->username . '/');
        $request = new Request();
        $response = $request->get($url);
        
        if ($response->status() == ResponseStatusCode::SUCCESS) {
            $this->profile = $this->getUserData($response);
        } else {
            $this->error = 'Username/URL not found: response status '
                . $response->status();
        }
        
        return $this->profile;
    }
    
    protected function extractJsonData($html)
    {
        $scriptTags = StringUtils::getBlocks(
            $html,
            '<script',
            '</script>'
        );
        
//        foreach ($scriptTags as $index => $block) {
//            echo 'BLOCK ' . $index . PHP_EOL;
//            echo $block . PHP_EOL . PHP_EOL;
//        }
//        die;
        
        $data = json_decode(substr(substr($scriptTags[4], 52), 0, -10));
//        file_put_contents(
//            $this->outputDirectory . '01-data.txt',
//            print_r($data, true)
//        );
        return $data;
    }
    
    protected function getAdditionalPosts(Response $response, $data)
    {
        $totalPosts = $data->entry_data->ProfilePage[0]->user->media->count;
        $nCurrentPosts = count(
            $data->entry_data->ProfilePage[0]->user->media->nodes
        );
        
        $posts = null;
        if ($nCurrentPosts < $totalPosts) {
            $posts = $this->queryForPosts(
                $data->entry_data->ProfilePage[0]->user->id,
                $data->entry_data->ProfilePage[0]->user->media->page_info->end_cursor,
                $totalPosts - $nCurrentPosts,
                $response->cookie('mid'),
                $response->cookie('sessionid'),
                $response->cookie('csrftoken'),
                $response->cookie('s_network')
            );
//            file_put_contents(
//                $this->outputDirectory . '02-additional-posts.txt',
//                print_r($posts, true)
//            );
        }
        return $posts;
    }
    
    protected function getUserData(Response $response)
    {
//        echo $response->body() . PHP_EOL . PHP_EOL;
        
        $data = $this->extractJsonData($response->body());
        $additionalPosts = $this->getAdditionalPosts($response, $data);
        
        $userData = $data->entry_data->ProfilePage[0]->user;
        $profile = new Profile(
            $userData->id,
            $userData->username,
            $userData->full_name,
            $userData->biography,
            $userData->connected_fb_page,
            $userData->follows,
            $userData->followed_by,
            $userData->external_url,
            isset($userData->profile_pic_url_hd) ? $userData->profile_pic_url_hd : ''
        );
        
        foreach ($userData->media->nodes as $post) {
            $profile->addPost(
                new Post(
                    $post->id,
                    $post->date,
                    isset($post->caption) ? $post->caption : '',
                    $post->thumbnail_src,
                    $post->display_src,
                    $post->is_video,
                    $post->dimensions->width,
                    $post->dimensions->height
                )
            );
        }
        if ($additionalPosts) {
            foreach ($additionalPosts->media->nodes as $post) {
                $profile->addPost(
                    new Post(
                        $post->id,
                        $post->date,
                        isset($post->caption) ? $post->caption : '',
                        $post->thumbnail_src,
                        $post->display_src,
                        $post->is_video,
                        $post->dimensions->width,
                        $post->dimensions->height
                    )
                );
            }
        }
        
        return $profile;
    }

    protected function queryForPosts(
        $userId,
        $lastCurrentPostId,
        $numberOfPosts,
        $mid,
        $sessionid,
        $csrftoken,
        $s_network
    ) {
        $post = [
            'q' => 'ig_user(' . $userId
                . ') { media.after('
                . $lastCurrentPostId
                . ', ' . $numberOfPosts
                . ') { count, nodes { caption, code, comments { count }, '
                . 'comments_disabled, date, dimensions { height, width }, '
                . 'display_src, id, is_video, likes { count }, owner { id }, '
                . 'thumbnail_src, video_views }, page_info } }',
            'ref' => 'users::show'
//            'query_id' => 17842962958175392 // Don't know where this ID originates...
        ];
        
        $data = http_build_query($post);
        $context_options = [
            'http' => [
                'method' => 'POST',
                'header'=> 'content-type: application/x-www-form-urlencoded' . PHP_EOL
                    . 'accept: */*' . PHP_EOL
                    . 'accept-encoding:gzip, deflate, br' . PHP_EOL
                    . 'accept-language:en-US,en;q=0.8' . PHP_EOL
                    . 'cookie:mid=' . $mid . '; sessionid=' . $sessionid
                    . '; csrftoken=' . $csrftoken . '; s_network=' . $s_network
                    . '; ig_pr=1; ig_vw=950' . PHP_EOL
                    . 'dnt:1' . PHP_EOL
                    . 'origin:https://www.instagram.com' . PHP_EOL
                    . 'referer:https://www.instagram.com/drfeelgoodtattoo/' . PHP_EOL
                    . 'x-instagram-ajax:1' . PHP_EOL
                    . 'x-requested-with:XMLHttpRequest' . PHP_EOL
                    . 'x-csrftoken:' . $csrftoken,
                'content' => $data
            ]
        ];
        $context = stream_context_create($context_options);
        return json_decode(gzdecode(file_get_contents(self::QUERY_URL, false, $context)));
    }
}
