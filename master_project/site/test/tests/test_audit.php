<?php
/**
 * @author Andrew Ritchie
 */
date_default_timezone_set("America/New_York");
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/request.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/audit_trail.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/shift_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/audit_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of UserService and User class.
 */
class AuditTests extends TestFixture {
    private $sqlService;
    private $shiftService;
    private $auditService;
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
        $this->sqlService = SQLConnectionService::get_instance($configuration);
        $this->shiftService = new ShiftService($this->sqlService, new UserService($this->sqlService));
        $this->auditService = new AuditService(null,$this->sqlService);
        parent::__construct("AuditTest",$configuration);
    } //end function __construct
    /**
     * Return the list of tests for the UserService (by function name)
     */
    protected function get_tests() {
        return array(
            "get_audit_history",
            "add_audit_log"
        );
    } //end function get_tests
    
    public function get_audit_history() {
        $audit_logs = $this->auditService->get_audit_history(2);
        $this->assert_equals(3, sizeof($audit_logs));
        $audit_log = $audit_logs[0];
        $this->assert_equals(strtotime("2019-06-10 01:23:45"),$audit_log->get_modification_date());
        $this->assert_equals(2,$audit_log->get_shift_id());
        $this->assert_equals("Test Details",$audit_log->get_details());
    }
    public function add_audit_log() {
        $this->auditService->add_audit_log(
            5, new AuditTrail(5,strtotime("2019-06-10 01:23:45"),"These are the changes.")
        );
        $audit_logs = $this->auditService->get_audit_history(5);
        $this->assert_equals(1, sizeof($audit_logs));
        $audit_log = $audit_logs[0];
        $this->assert_equals(strtotime("2019-06-10 01:23:45"),$audit_log->get_modification_date());
        $this->assert_equals(5,$audit_log->get_shift_id());
        $this->assert_equals("These are the changes.",$audit_log->get_details());
    }
    public function case_setup() {
        test_data_setup($this->sqlService);
    }


} //end class UserTests
$fixture = new AuditTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
