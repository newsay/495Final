<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/sql_connection_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of SQLConnectionService.
 */
class SQLConnectionTest extends TestFixture {
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
        parent::__construct("SQL Connection",$configuration);
    } //end function __construct
    /**
     * Before all tests start, retrieve an instance of the SQLConnectionService with a given configuration.
     */
    protected function fixture_setup() {
        $this->service = SQLConnectionService::get_instance($this->configuration);
        test_data_setup($this->service);
    } //end function fixture_setup
    /**
     * Disconnect the MySQL service after complete.
     */
    protected function case_teardown() {
        if ($this->service->is_connected()) {
            $this->service->disconnect();
        }
    } //end function case_teardown
    
    /**
     * Return the list of tests for the SQLConnectionService (by function name)
     */
    protected function get_tests() {
        return array(
            "connect",
            "select"
        );
    } //end function get_tests
    
    /**
     * Test to make sure a Connection succeeds.
     */
    protected function connect() {
        $this->service->connect();
        $this->assert_is_true($this->service->is_connected(), $this->service->get_error());
    } //end function connection
    
    /**
     * Test to make sure a basic SELECT statement succeeds and returns valid results
     */
    protected function select() {
        $this->service->connect();
        $res = $this->service->execute_query("SELECT * FROM Users");
        $this->assert_equals(3,sizeof($res[1]));
        $this->assert_equals("user1@gmail.com",$res[1][0]['Email']);
    } //end function select
} //end class SQLConnectionTest
$fixture = new SQLConnectionTest(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
