<?php
    /**
     * A bencode decoder
     *
     * @package     PHPLibs
     * @category    Algorithms
     * @subpackage  bencoding
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     http://creativecommons.org/licenses/by-sa/3.0/ CC Share Alike 3.0
     */

    /**
     * A specific exception, just for us! <3
     * 
     * @package     bencoding
     * @category    Exceptions
     */
    class Bencode_Exception extends Exception {}
    
    /**
     * A class for decoding data from the bencode format.
     * 
     * Credits to Petru Paler for implementation ideas, and for his original
     * “bencode.py” within the BitTorrent source-code available at
     * http://www.bittorrent.com/
     * 
     * @package     bencoding
     * @category    Data Formats
     */
    class Bencode {
        /**
         * Function map.
         * @access private
         */
        private $fnmap = null;
        

        public function __construct()
        {
            $this->fnmap = array_merge(array('d' => 'decode_dict',
                                             'l' => 'decode_list',
                                             'i' => 'decode_int'), 
                                             array_fill(0, 10, 'decode_string'));
        }
        
        /**
         * Decodes a bencoded string continuously.
         * 
         * @param string The bencoded string.
         * @param int An optional starting index from where to start the decoding.
         * @return int|string|array
         */
        public function parse($string, &$index = 0)
        {
            $result = array();
            $length = strlen($string);
            while ($index < $length) {
                $result[] = $this->decode($string, $index);
            }
            return $result;
        }
        
        /**
         * Decodes a string of characters that's bencoded. One decode only.
         * 
         * @param string The bencoded string.
         * @param int An optional starting index from where to start the decoding.
         * @return int|string|array
         */
        public function decode($string, &$index = 0)
        {
            if ( ! is_string($string)) {
                throw new Bencode_Exception('The object to be decoded must be a string.');
            } elseif ( ! strlen($string)) {
                throw new Bencode_Exception('String to decode must not be empty.');
            } else {
                $char = substr($string, $index, 1);
                
                if (array_key_exists($char, $this->fnmap)) {
                    $fn = $this->fnmap[$char];
                    return $this->$fn($string, $index);
                }
                    
                throw new Bencode_Exception(sprintf('Invalid bencoded string (%s at %d).', $char, $index));
            }
        }
        
        /**
         * Parses a bencoded string.
         * 
         * @param string The bencoded string.
         * @param int The index of where to start decoding within the string.
         * @return string The decoded string.
         */
        public function decode_string($string, &$index = 0)
        {
            // Format: ([0-9]+):bytes (length of bytes == \1)
            
            // For some reason range('0', '9') still generate integer values; workaround down below!
            if ( ! in_array(substr($string, $index, 1), array_map('chr', range(0x30, 0x39)), true))
                throw new Bencode_Exception('Not a bencoded string');
            
            // Find the length of the string and validate it
            $colon = $this->str_index($string, ':', $index);
            $colon -= $index;
            $length = (int)substr($string, $index, $colon);
            if($length == 0 && $colon != $index + 1)
                throw new Bencode_Exception("Bencoded string cannot be zero-length and still have a value");

            // Extract the string and increment the index
            $str = substr($string, $index + $colon + 1, $length);
            $index += $colon + $length + 1;
            return $str;
        }
        
        /**
         * Parses a bencoded integer.
         * 
         * @param string The bencoded string.
         * @param int The index of where to start decoding within the string.
         * @return int The decoded integer (signed)
         */
        public function decode_int($string, &$index = 0)
        {
            // Format: i(-|+)([0-9]+)e
            // Cannot have leading zeroes, and can't be a negative zero.
            if (substr($string, $index, 1) != 'i')
                throw new Bencode_Exception("Not a bencoded integer");
            ++$index;
            
            // Extract funny information!
            $number = substr($string, $index, $this->str_index($string, 'e', $index) - $index);
            list($first, $second) = str_split(substr($string, $index, 2), 1);
            $index += strlen($number) + 1;
            
            // Make sure it was a valid format
            if ($first == '-' && $second == '0')
                throw new Bencode_Exception("Bencoded integer cannot be zero-padded, and zero can't be negative");
            elseif ($first == '0' && $second != 'e')
                throw new Bencode_Exception('Bencoded integer cannot be zero-padded');
            
            return (int)$number;
        }
        
        /**
         * Parses a bencoded list.
         * 
         * @param string The bencoded string.
         * @param int The index of where to start decoding within the string.
         * @return array An array containing the decoded contents of the string.
         */
        public function decode_list($string, &$index = 0)
        {
            // Format: l<items>e
            if (substr($string, $index, 1) != 'l')
                throw new Bencode_Exception("Not a bencoded list");
            
            // Decode the contents
            $result = array();
            ++$index;
            while(substr($string, $index, 1) != 'e') {
                $result[] = $this->decode($string, $index);
            }
            ++$index;
            
            return $result;
        }
        
        /**
         * Parses a bencoded dictionary (associative array).
         * 
         * @param string The bencoded string.
         * @param int The index of where to start decoding within the string.
         * @return array An associative array containing the decoded contents of the dictionary.
         */
        public function decode_dict($string, &$index = 0)
        {
            // Format: d<items>e
            if (substr($string, $index, 1) != 'd')
                throw new Bencode_Exception("Not a bencoded dictionary");
            
            // Decode the contents
            $result = array();
            ++$index;
            while(substr($string, $index, 1) != 'e') {
                $key = $this->decode_string($string, $index);
                $val = $this->decode($string, $index);
                $result[$key] = $val;
            }
            ++$index;
            
            return $result;
        }
        
        /**
         * Private function str_slice, kind of like substring but the length
         * parameter is from the beginning of the string.
         * 
         * @access private
         * @param string The haystack string.
         * @param int Start position.
         * @param int Stop position.
         * @return string The sliced string.
         */
        private function str_slice($haystack, $start, $stop)
        {
            return substr($haystack, $start, $stop - $start);
        }
        
        /**
         * Private function str_index. Exactly like strpos, but it throws an
         * exception if the substring can't be found.
         * 
         * @param string The haystack string.
         * @param string The needle string.
         * @param int Optional parameter: offset.
         * @return int|void The position of the needle within haystack.
         */
        private function str_index($haystack, $needle, $offset = 0)
        {
            $pos = strpos($haystack, $needle, $offset);
            if ($pos === FALSE) {
                throw new Bencode_Exception(sprintf('Cannot find index for %s', $needle));
            }
            return $pos;
        }
    }
    
    // Example usage
    /*$bencode = new Bencode();
    var_dump($bencode->parse(file_get_contents('/Users/Kim/Desktop/portal.torrent')));*/
    
/* End of file bencode.php */
/* Location: ./algorithms/bencode.php */