<?php
    /**
     * A singleton pattern for PHP >= 5.3.0
     *
     * PHP Version >= 5.3.0
     *
     * @package     PHPLibs
     * @category    Design Patterns
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     http://creativecommons.org/licenses/by-sa/3.0/ CC Share Alike 3.0
     */

    /**
     * An abstract class that allows one (and only one) instance of the child class at a time
     *
     * @category    Singleton
     * @abstract
     */
    abstract class Singleton
    {
        protected function __construct() {}
        final private function __clone() {}
        
        /**
         * Storage for class instances
         *
         * @var         Singleton
         * @access      private
         */
        private static $__instances = array();
        
        /**
         * Returns a class instance if it exists
         * 
         * 
         * @param       string      (optional) A class, allows usage of Singleton::getInstance('className')
         * @return      Singleton   A class that extends the Singleton
         * @access      public
         * @author      public
         */
        public function getInstance()
        {
            $class = get_called_class();
            if ( ! isset(self::$__instances[$class]))
            {
                self::$__instances[$class] = new $class();
            }
            return self::$__instances[$class];
        }
    }
    
/* End of file Singleton.php */
/* Location: ./patterns/singleton/Singleton.php */ 