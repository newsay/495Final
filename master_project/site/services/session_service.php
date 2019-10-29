<?php
/**
 * @author Andrew Ritchie
 */
interface ISessionService {
    /**
     * Start a new session if one is not already active
     */
    public function start_session();
    /**
     * Destroy an active session, resetting all $_SESSION variables
     */
    public function clear_session();
    /**
     * Get a session variable
     * If no session is started, start the session
     * @param $property_name: Session variable to retrieve
     * @returns Value stored in session
     */
    public function get($property_name);
    /**
     * Set a session variable
     * If no session is started, start the session
     * @param $property_name: Session variable to set
     * @param $value: Value to set session variable to
     */
    public function set($property_name, $value);
} //end interface ISessionService

/**
 * Class to represent a PHP Session.
 * Purpose is to abstract the inner workings of Session State from the rest of the application.
 */ 
class SessionService implements ISessionService {
    private static $service_instance = null; //Singleton instance of service
    private $session_active = false;
    /**
     * Private constructor, use get_service instead.
     */
    private function __construct () {
        $this->start_session();
    } //end constructor
    
    /**
     * Start a new session if one is not already active
     */
    public function start_session() {
        if ($this->session_active == false) {
            session_start();
            $this->session_active = true;
        }
    } //end method start_session
    /**
     * Retrieve an instance of the session service
     */
    public static function get_service() {
        if (self::$service_instance == null) {
            self::$service_instance = new SessionService();
        }
        return self::$service_instance;
    } //end method get_service
    
    /**
     * Destroy an active session, resetting all $_SESSION variables
     */
    public function clear_session() {
        $_SESSION = array();
        session_destroy();
        $this->session_active = false;
    } //end method clear_session
    
    /**
     * Get a session variable
     * If no session is started, start the session
     * @param $property_name: Session variable to retrieve
     * @returns Value stored in session
     */
    public function get($property_name) {
        $this->start_session();
        if (array_key_exists($property_name, $_SESSION)) {
            return $_SESSION[$property_name];
        }
        else {
            return null;
        }
    }  //end method get
    
    /**
     * Set a session variable
     * If no session is started, start the session
     * @param $property_name: Session variable to set
     * @param $value: Value to set session variable to
     */
    public function set($property_name, $value) {
        $this->start_session();
        $_SESSION[$property_name] = $value;
    }  //end method set
}
