<?php
    /**
     * Quicksilver string scoring algorithm in PHP
     *
     * @package     PHPLibs
     * @category    Strings
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     X11 License
     */
    
    /**
     * A PHP implementation of the Quicksilver string score algorithm. Requires “MBString” extension (for unicode support).
     * 
     * Quicksilver URL: http://docs.blacktree.com/quicksilver/development/string_ranking
     * 
     * @param       string      Text to match against.
     * @param       search      Text to try to match with.
     * @param       offset      Offset to start matching from.
     * @return      float       Score (between 0.0 and 1.0).
     * @category    Strings
     */
    function strscore($string, $search, $offset = 0) {
        // Save some time from calling functions
        $stringlen = mb_strlen($string);
        $searchlen = mb_strlen($search);
        
        if($searchlen == 0) { return 0.9; }
        if($searchlen > $stringlen) { return 0; }
        
        // Search for smaller and smaller portions
        for($i = $searchlen; $i > 0; --$i) {
            $part = mb_substr($search, 0, $i);
            $index = mb_stripos($string, $part);
            
            if($index === FALSE) { continue; }
            if(($index + $searchlen) > ($stringlen + $offset)) { continue; }
            
            $next = mb_substr($string, $index + mb_strlen($part));
            $nextlen = mb_strlen($next);
            $abbr = '';
            
            if($i < $searchlen) {
                $abbr = mb_substr($search, $i);
            }
            
            // Search for the rest of the abbreviation (that didn't match)
            $rem = strscore($next, $abbr, $offset + $index);
            if($rem > 0) {
                $score = $stringlen - $nextlen;
                if($index != 0) {
                    if(mb_eregi('\s', mb_substr($string, $index-1, 1))) {
                        for($j = $index - 2; $j > -1; --$j) {
                            if(mb_eregi('\s', mb_substr($string, $j, 1))) {
                                $score -= 1.00;
                            } else {
                                $score -= .15;
                            }
                        }
                    } else {
                        $score -= $index;
                    }
                }
                $score += $rem * $nextlen;
                $score /= $stringlen;
                return (float)$score;
            }
        }
        return 0;
    }

/* End of file strscore.php */
/* Location: ./algorithms/strscore.php */ 