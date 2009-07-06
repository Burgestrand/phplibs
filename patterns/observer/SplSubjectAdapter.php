<?php
    /**
     * An adapter to the Subject class for SPL. Just a quick example implementation.
     */
    require 'Subject.php';
    class SplSubjectAdapter extends Subject implements SplSubject {
        /**
         * notify()-call arguments storage.
         */
        private $_args = array();
        
        /**
         * In accordance to the SplSubject function: notify().
         * 
         * {@inheritdoc}
         */
        public function notify()
        {
            $this->_args = func_get_args();
            foreach ($this->_observers as $obj) {
                $obj->update($this);
            }
            $this->_args = array();
            return $this;
        }
        
        /**
         * Same as {@link Subject::attach}, but it takes only one argument and
         * that argument must be a class which implements the SplObserver interface.
         */
        public function attach(SplObserver $obj)
        {
            return parent::attach($obj); 
        }
        /**
         * Same as {@link Subject::detach}, but it takes only one argument
         * and that argument must be a class which implements the SplObserver interface.
         */
        public function detach(SplObserver $obj)
        {
            return parent::detach($obj);
        }
        
        /**
         * Retrieves the arguments from the last notify()-call.
         * 
         * @return Array A numerically indexed array containing the arguments.
         */
        public function getArgs() 
        {
            return $this->_args; 
        }
    }
    
    /**
     * A wrapper class for global (procedural) functions for usage,
     * making them inherit the SplObserver interface.
     */
    class SplObserverWrapper implements SplObserver {
        /**
         * Private property for storing the actual function.
         */
        private $_function = null;
        
        /**
         * @param mixed A function name (string) or an array. A valid PHP callback, in other words.
         */
        public function __construct($func)
        {
            $this->_function = $func;
        }
        
        /**
         * The required update() function from SplObserver.
         * 
         * @param SplSubjectAdapter The SplSubjectAdapter object.
         * @return void
         */
        public function update(SplSubject $subject)
        {
            call_user_func_array($this->_function, $subject->getArgs());
        }
        
        /**
         * String conversion method.
         */
        public function __toString()
        {
            if (is_array($this->_function)) {
                return vsprintf('%s::%s', $this->_function);
            } else {
                return $this->_function;
            }
        }
    }
    
/* End of file SplSubjectAdapter.php */
/* Location: ./patterns/observer/SplSubjectAdapter.php */ 