<?php
    /**
     * Observer pattern implementation in PHP5.
     * 
     * Many web applications today build on an event model. The user requests
     * a page, and the application responds. This class is one way to make use
     * of that functionality within your applications. Create an event, register 
     * some handlers for it and fire away.
     * 
     * @package     PHPLibs
     * @category    Design Patterns
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     http://creativecommons.org/licenses/by-sa/3.0/ CC Share Alike 3.0
     * @since       Version 1.0
     */ 
    class Subject {
        private $_children = array();
        private $_observers = array();
        private $_name, $_parent;
        
        /**
         * Instance variable.
         * @static
         */
        private static $_instance;
        
        /**
         * Use Subject::getInstance instead of this one at all times.
         * 
         * @param String The new Subjects' name.
         * @param Subject The new Subjects' parent (ie. caller object).
         */
        private function __construct($name = null, Subject $parent = null)
        {
            if ($name and $parent) {
                if (!empty($name) && $parent) {
                    $this->_name = (string)$name;
                    $this->_parent = $parent;
                } else {
                    throw new Exception('Name must be castable to string,
                                         and parent must be a Subject.');
                }
            } elseif ($name or $parent) {
                throw new Exception('Both name and parent must be supplied,
                                     or neither.');
            } else {
                $this->_name = 'Master Event';
                $this->_parent = null;
            }
        }
        
        /**
         * Declared private to prevent cloning of the master event.
         */
        private function __clone() 
        {
            throw new Exception('Cloning of Subjects isn’t allowed.');
        }
        
        /**
         * Static function for retrieving the class instance.
         * 
         * @static
         * @return Subject The “master instance” of all Subjects.
         */
        public static function getInstance()
        {
            if (empty(self::$_instance)) {
                $_class = __CLASS__;
                self::$_instance = new $_class;
            }
            return self::$_instance;
        }
        
        /**
         * A magic function!
         * 
         * When an unknown method gets called this one kicks in and looks
         * through the subjects' children. If none of them exists, it 
         * creates a new Subject with the name of the requested attribute.
         * 
         * @param string The “attribute”, or the new subjects' name.
         * @return Subject Returns the child itself.
         */
        public function __get($name)
        {
            if (!isset($this->_children[$name])) {
                $_class = __CLASS__;
                $this->_children[$name] = new $_class((string)$name, $this);
            }
            return $this->_children[$name];
        }
        
        /**
         * Observer pattern notify() function.
         * 
         * This is the main function, it “calls” the current subject
         * and notifies its' observers by calling their registered functions. 
         * It also bubbles the event up to its' parents.
         * 
         * @return Subject The Subject class instance.
         */
        public function notify()
        {
            $args = func_get_args();
            $ourargs = array_merge(array($this), $args);
            foreach ($this->_observers as $obj) {
                call_user_func_array($obj, $ourargs);
            }
            if (!empty($this->_parent)) {
                call_user_func_array(array($this->_parent, 'notify'), $args);
            }
            return $self;
        }
        
        /**
         * Observer subscription.
         * 
         * Subscribes a function to the list of observers. An unlimited 
         * amount of subscribers can be given.
         * 
         * @return Subject The Subject class instance.
         */
        public function attach()
        {
            $args = func_get_args();
            foreach ($args as $function) {
                if (!in_array($function, $this->_observers)) {
                    $this->_observers[] = $function;
                }
            }
            return $this;
        }
        
        /**
         * Observer unsubscription.
         * 
         * Removes an observer from the list of observers. An unlimited
         * amount of observers can be given. If the observer isn't
         * registered nothing will happen.
         * 
         * @return Subject The Subject class instance.
         */
        public function detach()
        {
            $args = func_get_args();
            // For each parameter…
            foreach ($args as $function) {
                // … remove the function from the subscriber list
                $keys = array_keys($this->_observers, $function, false);
                foreach ($keys as $key) {
                    unset($this->_observers[$key]);
                }
            }
            return $this;
        }
        
        /**
         * String representation of the Subject object.
         * 
         * Converts the subject object into a string. Magic!
         * 
         * @return string String representation of the object.
         */
        public function __toString()
        {
            return sprintf('%s (%d:%d)', 
                           $this->_name, 
                           count($this->_observers),
                           count($this->_children));
        }
    }
    
/* End of file Subject.php */
/* Location: ./patterns/observer/Subject.php */ 