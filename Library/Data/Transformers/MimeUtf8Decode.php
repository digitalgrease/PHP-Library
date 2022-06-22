<?php

namespace DigitalGrease\Library\Data\Transformers;

class MimeUtf8Decode implements Transform
{
    protected const PATTERN = '#=\?UTF-8\?B\?(.*?)\?=#';
    
    public static function transform(?string $value): string
    {
        if ($value) {
            preg_match_all(self::PATTERN, $value, $matches);

            if ($matches[0]) {
                foreach ($matches[0] as $i => &$match) {
                    $match = '#'.preg_quote($match).'#';
                    $matches[1][$i] = base64_decode($matches[1][$i]);
                }

                try {
                    $value = preg_replace($matches[0], $matches[1], $value);
                } catch (\Throwable $t) {
                    dd($t, $value, $matches);
                }
            }
        } else {
            $value = '';
        }
        
        return $value;
    }
}
