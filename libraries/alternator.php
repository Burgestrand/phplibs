<?php
    /**
     * An item alternator: iterates through an iterator and returns new items infinitely
     *
     * @package     PHPLibs
     * @category    Utilities
     * @author      Kim Burgestrand <kim@burgestrand.se>
     */
    class Alternator extends ArrayIterator
    {
        private static $instances = array();
        
        /**
         * Note that this method returns the same alternator for the same arguments
         *
         * @return      Alternator
         */
        public static function factory(array $items)
        {
            $hash = hash('md5', serialize($items));
            
            if ( ! isset(self::$instances[$hash]))
            {
                self::$instances[$hash] = new Alternator($items);
            }
            
            return self::$instances[$hash];
        }
        
        /**
         * Initializes the Alternator with values (and rewinds it)
         *
         * @param       Traversable|array
         */
        public function __construct(array $items)
        {
            parent::__construct($items);
            $this->rewind();
        }
        
        /**
         * Extend the @see ArrayIterator::next() method to make an @see InfiniteIterator
         */
        public function next()
        {
            parent::next();
            ($this->valid() OR $this->rewind());
        }
        
        /**
         * Returns the current item and advances the internal pointer
         *
         * @return      mixed
         */
        public function get()
        {
            $current = $this->current();
            $this->next();
            return $current;
        }
    }

/* End of file alternator.php */
/* Location: ./phplibs/libraries/alternator.php */ 