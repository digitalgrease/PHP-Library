<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Utils;

/**
 * Formatting and string utilities.
 * 
 * @author Tom Gray
 */
class StringUtils
{
    
    /**
     * Titles that people may use in their names.
     * 
     * @var array
     */
    protected static $titles = [
        'Mr',
        'Mrs',
        'Miss',
        'Ms',
        'Dr',
        'Rev',
        'Reverend',
        'Major',
        'Lt Col'
    ];
    
    /**
     * Generate a list of twig tags from an array of data.
     * 
     * @param array  $data   The array to generate the list of tags from.
     * @param string $prefix Only used by the recursion unless you want to add a
     *                       prefix to all the tags.
     * 
     * @return string
     */
    public static function generateTwigTagList(array $data, $prefix = '')
    {
        $tagList = '';
        foreach ($data as $key => $data) {
            if (is_array($data)) {
                $tagList .= StringUtils::generateTwigTagList(
                    $data,
                    $prefix.$key.'.'
                ).PHP_EOL;
            } else {
                $tagList .= $prefix.$key.PHP_EOL;
            }
        }
        return $tagList;
    }
    
    /**
     * Get the value of an attribute if found in a string.
     * 
     * GL IMPROVEMENT: Implement with Moore pattern matching algorithm and
     * native regex and perform benchmarking.
     * 
     * @param string $attribute The name of the attribute to search the string
     *  for.
     * @param string $string The string to search for the attribute and value.
     * 
     * @return string The value of the attribute if found. Returns an empty
     *  string if the attribute is not in the string.
     */
    public static function getAttributeValue($attribute, $string)
    {
        $value = '';
        $attributeLength = strlen($attribute);
        $maxI = strlen($string) - $attributeLength + 1;
        $i = 0;
        $isMatch = false;
        while ($i < $maxI && !$isMatch) {
            $k = 0;
            $isMatch = $attribute[$k++] == $string[$i++];
            while ($isMatch && $k < $attributeLength) {
                $isMatch = $attribute[$k++] == $string[$i++];
            }
            
            if ($isMatch) {
                
                // Ensure the attribute is indeed an attribute; the key followed
                // by '=';
                $tmp = '';
                while ($string[$i] == '=' || $string[$i] == ' ') {
                    $tmp = $string[$i++];
                }
                if ('=' == trim($tmp)) {

                    // Skip to the start of the attribute value.
                    while ($string[$i] == '"' || $string[$i] == ' ') {
                        ++$i;
                    }

                    // Build the value until the end of the value is found.
                    while (
                        $string[$i] != ' '
                        && $string[$i] != '"'
                        && $string[$i] != '>'
                        && $string[$i] != ';'
                    ) {
                        $value .= $string[$i++];
                    }
                }
            }
        }
        return $value;
    }
    
    /**
     * Get the blocks of the string that are enclosed in the given markers,
     * inclusively.
     * Returns an empty array if there are no blocks with the given markers.
     * RETURNS INNER BLOCKS FOUND ONLY.
     * 
     * GL TEST: Works with a nesting level of 1. Test with greater levels of
     * nesting.
     * 
     * GL FURTHER DEVELOPMENT: Return outer and inner blocks.
     * GL FURTHER DEVELOPMENT: Return the blocks found at each level of nesting.
     * 
     * GL IMPROVEMENT: Implement with Moore pattern matching algorithm and
     * native regex and perform benchmarking.
     * 
     * @param string  $string
     * @param string  $startMarker 
     * @param string  $endMarker
     * @param int     $startIndex
     * @param int     $endIndex
     * 
     * @return array
     */
    public static function getBlocks(
        $string,
        $startMarker,
        $endMarker,
        $startIndex = 0,
        &$endIndex = 0
    ) {
        $blocks = [];
        
        // Records the level of nesting.
        static $level = -1;
        ++$level;
        
        // Loop over the string while still possible to match a complete block.
        $maxIndex = strlen($string) - strlen($startMarker) - strlen($endMarker);
        while ($startIndex < $maxIndex) {

            // Look for a marker.
            while (
                $startIndex < $maxIndex
                && 0 !== substr_compare(
                    $string,
                    $startMarker,
                    $startIndex,
                    strlen($startMarker),
                    true
                )
                && 0 !== substr_compare(
                    $string, $endMarker, $startIndex, strlen($endMarker), true
                )
            ) {
                ++$startIndex;
            }
            
            // If not reached the end of the string the then a marker has been
            // found.
            if ($startIndex < $maxIndex) {
                
                // Check if the start marker of a block has been found.
                if (
                    0 === substr_compare(
                        $string,
                        $startMarker,
                        $startIndex,
                        strlen($startMarker),
                        true
                    )
                ) {
                    
                    // Start marker has been found here.
                    $startBlockIndex = $startIndex;
                    $startIndex += strlen($startMarker);

                    // Look for a marker.
                    while (
                        $startIndex < $maxIndex
                        && 0 !== substr_compare(
                            $string,
                            $startMarker,
                            $startIndex,
                            strlen($startMarker),
                            true
                        )
                        && 0 !== substr_compare(
                            $string,
                            $endMarker,
                            $startIndex,
                            strlen($endMarker),
                            true
                        )
                    ) {
                        ++$startIndex;
                    }

                    // If not reached the end of the string then a marker has
                    // been found.
                    if ($startIndex < $maxIndex) {
                        
                        // Check if the end marker of a block has been found.
                        if (
                            0 === substr_compare(
                                $string,
                                $endMarker,
                                $startIndex,
                                strlen($endMarker),
                                true
                            )
                        ) {
                            
                            // Complete block has been found here.
                            $startIndex += strlen($endMarker);
                            $endIndex = $startIndex;
                            
                            $blocks[] = substr(
                                $string,
                                $startBlockIndex,
                                $endIndex - $startBlockIndex
                            );
                        } else {
                            
                            // Nested start block marker has been found.
                            $blocks = array_merge(
                                $blocks,
                                self::getBlocks(
                                    $string,
                                    $startMarker,
                                    $endMarker,
                                    $startIndex,
                                    $startIndex
                                )
                            );
                        }
                    }
                } else {
                    
                    // End marker has been found with no corresponding start
                    // marker. If at a nested level then this is the end marker
                    // for an outer block. Return the inner blocks found.
                    if ($level) {
                        --$level;
                        return $blocks;
                    }

                    // Complete block found.
                    $startIndex += strlen($endMarker);
                    $endIndex = $startIndex;
                    ++$startIndex;
                    
                    // DOES NOT RETURN OUTER LEVELS, ONLY INNER NESTED BLOCKS.
//                    $blocks[] = substr(
//                        $string,
//                        $startBlockIndex,
//                        $endIndex - $startBlockIndex
//                    );
                }
            }
        }
        return $blocks;
    }
    
    /**
     * Get the value of this key with HTML br tags replacing line breaks.
     * 
     * @param string $text
     * 
     * @return string
     */
    public static function getHtmlLineBreaks($text)
    {
        $html = '';
        
        $i = 0;
        $n = strlen($text);
        
        while ($i < $n) {
            if ($text[$i] == PHP_EOL) {
                $html .= '<br/>';
            } else {
                $html .= $text[$i];
            }
            ++$i;
        }
        
        return $html;
    }
    
    /**
     * Get the value of this key with HTML p tags surrounding text that is
     * separated with two new line breaks.
     * 
     * @param string $text
     * 
     * @return string
     */
    public static function getHtmlParagraphs($text)
    {
        $html = '<p>';
        
        $i = 0;
        $n = strlen($text);
        
        while ($i < $n) {
            if (
                // This is the case when loaded from file.
                ($text[$i] == PHP_EOL && $text[$i + 1] == PHP_EOL)
                
                // This is the case when the data has been saved in the database from a textarea on a form.
                || ($text[$i] == chr(0x0D) && $text[$i + 1] == chr(0x0A)
                && $text[$i + 2] == chr(0x0D) && $text[$i + 3] == chr(0x0A))
            ) {
                $html .= '</p><p>';
            } else {
                $html .= $text[$i];
            }
            ++$i;
        }
        
        return $html . '</p>';
    }
    
    /**
     * Get a title from the given name if it starts with one.
     * 
     * @param string $name
     * 
     * @return string
     */
    public static function getTitle($name)
    {
        $i = 0;
        $n = count(self::titles);
        
        while ($i < $n && stripos($name, self::$titles[$i]) !== 0) {
            ++$i;
        }
        
        if ($i < $n) {
            return self::$titles[$i];
        } else {
            return '';
        }
    }
    
    /**
     * Format the given date time to return the hour.
     * 
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public static function hour(\DateTime $dateTime)
    {
        return $dateTime->format('H');
    }
    
    /**
     * Get whether a string is a single character.
     * 
     * @param string $str
     * 
     * @return boolean
     */
    public function isChar($str)
    {
        if (preg_match('/^([a-z]|[A-Z])$/', $str)) {
            return true;
        }
        return false;
    }
    
    /**
     * Get whether a string consists of only digits.
     * 
     * @param string $str
     * @param int $length
     * 
     * @return boolean
     */
    public function isDigits($str, $length = null)
    {
        // DO TG StringUtils: Improvement? Compare regexes.
//        if (!preg_match('/^\d+$/', $year) || strlen($year) != 4) {
        
        $regex = '/^[0-9]';
        if ($length) {
            $regex .= '{'.$length.'}';
        } else {
            $regex .= '+';
        }
        $regex .= '$/';
        
        if (preg_match($regex, $str)) {
            return true;
        }
        return false;
    }
    
    /**
     * Format the given date time to return the minutes past the hour.
     * 
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public static function minutes(\DateTime $dateTime)
    {
        return $dateTime->format('i');
    }
    
    /**
     * Converts a string (sentence) that contains a web address into an HTML
     * anchor. If there is no web address then this just returns the original
     * string.
     * 
     * @param string $string
     * 
     * @return string
     */
    public static function toLink($string)
    {
        $words = [];
        $href = '';
        foreach (explode(' ', $string) as $word) {
            if (substr($word, 0, 4) == 'http') {
                $href = $word;
            } else {
                $words[] = $word;
            }
        }
        
        if ($href && $words) {
            $link = '<a href="' . $href . '" target="_blank">'
                . implode(' ', $words) . '</a>';
        } elseif ($href) {
            $link = '<a href="' . $href . '" target="_blank">'
                . $href . '</a>';
        } else {
            $link = $string;
        }
        
        return $link;
    }
    
    /**
     * Format the given date time as a UK date string.
     * 
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public static function ukDateFormat(\DateTime $dateTime = null)
    {
        return empty($dateTime) ? '' : $dateTime->format('d/m/Y');
    }
    
    /**
     * Format the given date time as a UK date and time string.
     * 
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public static function ukDateTimeFormat(\DateTime $dateTime = null)
    {
        return empty($dateTime) ? '' : $dateTime->format('d/m/Y H:i:s');
    }
}
