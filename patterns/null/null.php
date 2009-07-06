<?php
    /**
     * An implementation of the “Null” design pattern in PHP
     * 
     * The “Null” design pattern returns null for *EVERYTHING*
     * 
     *
     * @package     PHPLibs
     * @category    Design Patterns
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     http://creativecommons.org/licenses/by-sa/3.0/ CC Share Alike 3.0
     */
    class Null
    {
        /**
         * The universal return value
         *
         * @var         NULL
         * @access      private
         * @static
         */
        private static $_return = NULL;
        
        /**
         * Magic method for class method calls
         *
         * @return      @see $_return
         * @access      public
         * @author      Kim Burgestrand <kim@burgestrand.se>
         */
        public function __call($name = NULL, $args = array())
        {
            return self::$_return;
        }
        
        /**
         * Magic method for static method callls
         *
         * @return      @see $_return
         * @access      public
         * @author      Kim Burgestrand <kim@burgestrand.se>
         */
        public static function __callStatic($name = NULL, $args = array())
        {
            return self::$_return;
        }
        
        /**
         * Magic method for class attribute access
         *
         * @return      @see $_return
         * @access      public
         * @author      Kim Burgestrand <kim@burgestrand.se>
         */
        public function __get($name = NULL)
        {
            return self::$_return;
        }
        
        /**
         * Magic method for setting properties
         *
         * @return      @see $_return
         * @access      public
         * @author      Kim Burgestrand <kim@burgestrand.se>
         */
        public function __set($name = NULL, $value = NULL)
        {
            return self::$_return;
        }
    }
    
    // $test = new Null();
    //     $test->a = 'B';
    //     var_dump($test->a, $test->a(), $test::a(), $test->a = 'b');
    
/* End of file null.php */
/* Location: ./patterns/null/null.php */ 