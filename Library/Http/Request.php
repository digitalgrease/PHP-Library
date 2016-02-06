<?php

/*
 * Copyright (c) 2015 Greasy Lab.
 * 
 * Tuesday 24th November 2015
 * 
 * Tom Gray
 */

namespace GreasyLab\Library\Http;

require_once __DIR__.'/../../../Library/src/Http/Response.php';
require_once __DIR__.'/../../../Library/src/Http/Url.php';

use GreasyLab\Library\Http\Response;
use GreasyLab\Library\Http\Url;

/**
 * Makes an HTTP request.
 *
 * @version 1.0
 * @author Tom Gray
 */
class Request
{
    /**
     * Send request.
     * 
     * @param Url $url
     * @param Url $referer
     * @param bool $redirect Recursively follow any redirects.
     * 
     * @return Response The response.
     */
    public function send(Url $url, Url $referer = null, $redirect = true)
    {
        $response = $this->sendRequest($url, $referer);
        if ($response->isRedirect() && $redirect) {
            var_dump('Redirecting to '.$response->redirectUrl());
            $response = $this->sendRequest($response->redirectUrl());
        }
        return $response;
    }
    
    /**
     * Send request.
     * 
     * @param Url $url
     * @param Url $referer
     * 
     * @return Response The response.
     */
    private function sendRequest(Url $url, Url $referer = null)
    {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, trim($url));
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_HEADER, true);
        curl_setopt(
            $curl_handle,
            CURLOPT_USERAGENT,
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR '
            . '1.0.3705; .NET CLR 1.1.4322)'
        );
        if ($referer) {
            curl_setopt($curl_handle, CURLOPT_REFERER, trim($referer));
        } else {
            curl_setopt(
                $curl_handle,
                CURLOPT_REFERER,
                trim($url->protocol() . $url->domain())
            );
        }
        
        $response = curl_exec($curl_handle);
        $headerSize = curl_getinfo($curl_handle, CURLINFO_HEADER_SIZE);
        
        $response = new Response(
            substr($response, 0, $headerSize),
            substr($response, $headerSize),
            curl_getinfo($curl_handle, CURLINFO_RESPONSE_CODE),
            new Url(curl_getinfo($curl_handle, CURLINFO_REDIRECT_URL))
        );
        
        curl_close($curl_handle);
        
        return $response;
    }
}
