<?php

namespace DigitalGrease\Library\Test\Data\Transformers;

use DigitalGrease\Library\Data\Transformers\MimeUtf8Decode;
use Tests\TestCase;

class MimeUtf8DecodeTest extends TestCase
{
    /**
     * @test
     */
    public function string_without_mime_is_untouched()
    {
        $string = $this->faker->sentence;
        
        $result = MimeUtf8Decode::transform($string);
        
        $this->assertEquals($string, $result);
    }
    
    /**
     * @test
     */
    public function string_with_single_mime_is_decoded()
    {
        $string = $this->faker->sentence;
        
        $result = MimeUtf8Decode::transform($string . " =?UTF-8?B?8J+TuA==?=");
        
        $this->assertEquals($string . " 📸", $result);
    }
    
    /**
     * @test
     */
    public function string_with_multiple_mimes_are_decoded()
    {
        $string = $this->faker->sentence;
        
        $result = MimeUtf8Decode::transform("=?UTF-8?B?8J+ksw==?= " . $string . "=?UTF-8?B?8J+SsOKdjA==?==?UTF-8?B?8J+TuA==?=");
        
        $this->assertEquals("🤳 " . $string . "💰❌📸", $result);
    }
}
