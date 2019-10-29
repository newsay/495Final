<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/session_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of SessionService.
 */
class SessionTests extends TestFixture {
    private $sessionService;
    
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
       
        parent::__construct("SessionTest",$configuration);
    } //end function __construct
    
    /**
     * Return the list of tests for the SessionService
     */
    protected function get_tests() {
        return array(
            "clear_session",
            "get_data",
            "set_data"
        );
    } //end function get_tests
    
    /**
     * Get an instance of the session service
     */
    protected function fixture_setup() {
        $this->sessionService = SessionService::get_service();
    } //end function fixture_setup
        /**
     * Before every case, clear the session state
     */
    protected function case_setup() {
        session_unset();
        session_destroy();
        $this->sessionService->start_session();
    } //end function case_setup
    
    /**
     * Test to verify sessions will properly clear
     */
    protected function clear_session() {
        $_SESSION["test_val"] = "mytest";
        $this->sessionService->clear_session();
        $this->assert_is_false($_SESSION["test_val"]);
    } //end method clear_session
    
    /**
     * Test to verify data can be retrieved from session state
     */
     protected function get_data() {
         $_SESSION["test_val"] = "mytest2";
         $this->assert_equals("mytest2", $this->sessionService->get("test_val"));
     } //end method get_data
     
     /**
     * Test to verify data can be stored in session state
     */
     protected function set_data() {
         $this->sessionService->set("test_val", "mytest3");
         $this->assert_equals("mytest3", $_SESSION["test_val"]);
     } //end method get_data
} //end class SessionTests
$fixture = new SessionTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
