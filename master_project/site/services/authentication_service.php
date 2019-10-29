<?php

/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/session_service.php";

interface IAuthenticationService
{
    public function login($email_address, $password);
    public function validate_user($email_address, $password);
    public function validate_security_questions($email_address, $answer1, $answer2, $answer3);
    public function get_user_id();
    public function get_user_full_name();
    public function get_user_organization();
    public function get_user_type();
    public function get_security_questions($email_address);
    public function is_logged_in();
    public function is_initialized();
    public function logoff();
}
/**
 * Class to allow authentication with the database, and if the user is authenticated.
 * Purpose is to abstract the inner workings of authentication
 */
class AuthenticationService implements IAuthenticationService
{

    private $session_service; //Service to save session state to
    private $sql_service; //Service to retrieve users from

    /**
     * Given a Session Service and SQL Service, create a new Authentication Service
     * @param $session_service: SessionService. Used to store login state.
     * @param $sql_service: SQLConnectionService. Used to retrieve SQL database values.
     */
    public function __construct($session_service, $sql_service)
    {
        $this->session_service = $session_service;
        $this->sql_service = $sql_service;
    }

    /**
     * Given a user name and password, log in to the system and store the session state.
     * @param $email_address: The email address the user is using to log in.
     * @param $password: The password the user is using to log in.
     * @return True if login is successful. False if login fails.
     */
    public function login($email_address, $password)
    {
        $this->sql_service->connect();
        $result = $this->sql_service->execute_query("SELECT UserId, Password, FirstName, LastName, OrganizationId, UserType FROM Users WHERE Email=?", "s", $email_address)[1];
        if (sizeof($result) == 0) {
            return false;
        } else {
            $result = $result[0];
            $db_password = $result["Password"];
            if (password_verify($password, $db_password)) {
                $this->session_service->set("user_id", $result["UserId"]);
                $this->session_service->set("user_name", $result["FirstName"] . " " . $result["LastName"]);
                $this->session_service->set("user_organization", $result["OrganizationId"]);
                $this->session_service->set("user_type", $result["UserType"]);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Returns true if an email and password match an account
     */
    public function validate_user($email_address, $password)
    {
        $this->sql_service->connect();
        $result = $this->sql_service->execute_query("SELECT UserId, Password, FirstName, LastName, OrganizationId, UserType FROM Users WHERE Email=?", "s", $email_address)[1];
        if (sizeof($result) == 0) {
            return false;
        } else {
            $result = $result[0];
            $db_password = $result["Password"];
            if (password_verify($password, $db_password)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Returns true if security questions match an account
     */
    public function validate_security_questions($email_address, $answer1, $answer2, $answer3)
    {
        $this->sql_service->connect();
        $result = $this->sql_service->execute_query("SELECT UserId, SecurityQuestion1Answer, SecurityQuestion2Answer, SecurityQuestion3Answer, FirstName, LastName, OrganizationId, UserType FROM Users WHERE Email=?", "s", $email_address)[1];
        if (sizeof($result) == 0) {
            return false;
        } else {
            $result = $result[0];
            if (password_verify($answer1, $result["SecurityQuestion1Answer"]) && password_verify($answer2, $result["SecurityQuestion2Answer"]) && password_verify($answer3, $result["SecurityQuestion3Answer"])) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Get the ID of the logged in user
     * @return the ID of the logged in user.
     */
    public function get_user_id()
    {
        return $this->session_service->get("user_id");
    }

    /**
     * Get the full name of the logged in user
     * @return the full name of the logged in user.
     */
    public function get_user_full_name()
    {
        return $this->session_service->get("user_name");
    }

    /**
     * Get the organization ID of the logged in user
     * @return the organization of the logged in user.
     */
    public function get_user_organization()
    {
        return $this->session_service->get("user_organization");
    }

    /**
     * Get the type of the logged in user
     * @return the type of the logged in user.
     */
    public function get_user_type()
    {
        return $this->session_service->get("user_type");
    }

    /**
     * Get the security questions of a given user
     * @param $userEmail: The email of the user to retrieve security questions for
     * @return An array of security questions
     */
    public function get_security_questions($email_address)
    {
        $this->sql_service->connect();
        $result = $this->sql_service->execute_query("SELECT UserId, FirstName, LastName, OrganizationId, UserType, SecurityQuestion1, SecurityQuestion2, SecurityQuestion3 FROM Users WHERE Email=?", "s", $email_address)[1];
        if (sizeof($result) == 0) {
            return null;
        }
        $result = $result[0];
        return array($result["SecurityQuestion1"], $result["SecurityQuestion2"], $result["SecurityQuestion3"]);
    }
    /**
     * Get whether the user is logged in
     * @return whether the user is logged in
     */
    public function is_logged_in()
    {
        return $this->session_service->get("user_id") != null;
    }

    /**
     * Get whether the user is initialized; i.e., if the user has security question answers
     * @return whether the user is initialized
     */
    public function is_initialized()
    {
        $this->sql_service->connect();
        $result = $this->sql_service->execute_query("SELECT UserId, Password, FirstName, LastName, OrganizationId, UserType, SecurityQuestion1Answer, SecurityQuestion2Answer, SecurityQuestion3Answer FROM Users WHERE UserId=?", "d", $this->get_user_id())[1];
        if (sizeof($result) == 0) {
            return false;
        }
        $result = $result[0];
        if (!$result["SecurityQuestion1Answer"] || !$result["SecurityQuestion2Answer"] || !$result["SecurityQuestion3Answer"]) {
            return false;
        }
        return true;
    }

    /**
     * Log out of the system
     */
    public function logoff()
    {
        $this->session_service->set("user_id", null);
        $this->session_service->set("user_name", null);
        $this->session_service->set("user_type", null);
        $this->session_service->set("user_organization", null);
    }
} //end class AuthenticationService
