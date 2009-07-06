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