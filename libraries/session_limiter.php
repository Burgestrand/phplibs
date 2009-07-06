<?php
    /**
     * Starts a session and limits its' life (not trusting the browser to kill it properly)
     * 
     * You can optionally pass an array of configuration options, using the following fields:
     *     lifetime: How long the session will live (maximum) in seconds (default: 5*60 [5 minutes])
     *     refresh: Refresh the lifetime if the session is not old enough to die (default: TRUE)
     *     field: Session field name to store the information in
     *
     * @package     PHPLibs
     * @category    Sessions
     * @param       array           A configuration array according to format in docstring
     * @return      void
     * @author      Kim Burgestrand <kim@burgestrand.se>
     * @license     http://creativecommons.org/licenses/by-sa/3.0/ CC Share Alike 3.0
     */
    function session_init(array $config = array())
    {
        // Make sure the session hasn’t been started already
        if ( ! (bool) session_id())
            trigger_error('The session hasn’t been started!', E_USER_ERROR);
        
        // Read configuration
        $config = (object) array_merge(array('lifetime' => 5 * 60,
                                             'refresh' => TRUE,
                                             'field' => session_name() . 'BORN'), (array) $config);
        
        // Retrieve birth and death time
        (isset($_SESSION[$config->field]) OR $_SESSION[$config->field] = time());
        $death = $_SESSION[$config->field] + $config->lifetime;
        
        if (time() > $death)
        {
            // Unset session data
            $_SESSION = array();
            
            // Remove session cookie
            (isset($_COOKIE[session_name()])) AND setcookie(session_name(), '', 1, '/'));
            
            // Destroy the session
            session_destroy();
        }
        elseif ($config->refresh)
        {
            // The session is still alive and we should refresh its’ life!
            $_SESSION[$config->field] = time();
        }
    }
    
    // Try
    // session_start();
    //     session_init(array('lifetime' => 10, 'refresh' => TRUE));
    //     (isset($_SESSION['ALIVE']) OR $_SESSION['ALIVE'] = date('H:i:s'));
    //     var_dump($_SESSION);
    
/* End of file session.php */
/* Location: ./Users/Kim/Desktop/session.php */ 