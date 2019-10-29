<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/authentication_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/session_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/sql_connection_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of AuthenticationService.
 */
class AuthenticationTests extends TestFixture {
    private $authService;
    private $sessionService;
    private $sqlService;
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
       
        parent::__construct("AuthenticationTest",$configuration);
    } //end function __construct
    
    /**
     * Return the list of tests for the SessionService
     */
    protected function get_tests() {
        return array(
            "login",
            "login_bad_user",
            "login_bad_password",
            "validate_user",
            "validate_security_questions",
            "get_user_id",
            "get_user_organization",
            "get_user_full_name",
            "get_user_type",
            "is_logged_in",
            "is_initialized",
            "logoff"
        );
    } //end function get_tests
    
    /**
     * Get an instance of the authentication service
     */
    protected function fixture_setup() {
        $sql_service = SQLConnectionService::get_instance($this->configuration);
        $sql_service->connect();
        $this->sessionService = SessionService::get_service();
        $this->authService = new AuthenticationService($this->sessionService, $sql_service);
        $this->sqlService = $sql_service;
    } //end function fixture_setup
        /**
     * Before every case, clear the session state
     */
    protected function case_setup() {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    } //end function case_setup
    
    /**
     * Test login functionality
     */
     protected function login() {
         $this->assert_is_true($this->authService->login("manager1@gmail.com","password3"));
         $this->assert_equals(3,$_SESSION["user_id"]);
         $this->assert_equals("Manager One",$_SESSION["user_name"]);
         $this->assert_equals(1,$_SESSION["user_organization"]);
         $this->assert_equals(1,$_SESSION["user_type"]);
     }
     
     /**
     * Test bad user name
     */
     protected function login_bad_user() {
         $this->assert_is_false($this->authService->login("managerr1@gmail.com","password3"));
     }
     
     /**
     * Test bad password
     */
     protected function login_bad_password() {
         $this->assert_is_false($this->authService->login("manager1@gmail.com","password4"));
     }
     
     protected function validate_user() {
        test_data_setup($this->sqlService);
         $this->assert_is_true($this->authService->validate_user("manager1@gmail.com", "password3"));
     }

     protected function validate_security_questions() {
        test_data_setup($this->sqlService);
        $this->assert_is_true($this->authService->validate_security_questions("manager1@gmail.com", "test1","test2","test3"));
    }
     protected function get_user_id() {
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_equals(3, $this->authService->get_user_id());
     }
     protected function get_user_full_name() {
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_equals("Manager One", $this->authService->get_user_full_name());
     }
     protected function get_user_organization() {
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_equals(1, $this->authService->get_user_organization());
     }
     protected function get_user_type() {
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_equals(1, $this->authService->get_user_type());
     }
     protected function is_logged_in() {
         $this->sessionService->clear_session();
         $this->assert_is_false($this->authService->is_logged_in());
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_is_true($this->authService->is_logged_in());
     }
     protected function is_initialized () {
         $this->sessionService->clear_session();
         $this->assert_is_false($this->authService->is_logged_in());
         $this->authService->login("manager1@gmail.com","password3");
         $this->assert_is_true($this->authService->is_initialized());
     }
     protected function logoff() {
        $this->sessionService->clear_session();
        $this->assert_is_false($this->authService->is_logged_in());
        $this->authService->login("manager1@gmail.com","password3");
        $this->authService->logoff();
        $this->assert_equals(null,$_SESSION["user_id"]);
         $this->assert_equals(null,$_SESSION["user_name"]);
         $this->assert_equals(null,$_SESSION["user_organization"]);
         $this->assert_equals(null,$_SESSION["user_type"]);
    }
} //end class AuthenticationTests
$fixture = new AuthenticationTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
