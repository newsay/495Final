<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/request.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/shift_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/authentication_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of UserService and User class.
 */
class ShiftTests extends TestFixture {
    private $userService;
    private $sqlService;
    private $authService;
    private $shiftService;
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
        $this->sqlService = SQLConnectionService::get_instance($configuration);
        $this->userService = new UserService($this->sqlService);
        $this->shiftService = new ShiftService($this->sqlService, $this->userService);
        $this->authService = new AuthenticationService(null,$this->sqlService);
        parent::__construct("ShiftTest",$configuration);
    } //end function __construct
    /**
     * Return the list of tests for the UserService (by function name)
     */
    protected function get_tests() {
        return array(
            "get_shifts",
            "get_shifts_between",
            "get_shift",
            "add_shift",
            "modify_shift"
        );
    } //end function get_tests
    
    public function case_setup() {
        test_data_setup($this->sqlService);
    }

    public function get_shifts() {
        $shifts = $this->shiftService->get_shifts(1);
        $this->assert_equals(14,sizeof($shifts));
        $shift = $shifts[6];
        $this->assert_equals(2, $shift->get_shift_id());
        $this->assert_equals(3, $shift->get_assigned_user_id());
        $this->assert_equals(strtotime("2019-05-26 02:00:00"),$shift->get_start_datetime());
        $this->assert_equals(strtotime("2019-05-26 10:00:00"),$shift->get_end_datetime());
        $this->assert_equals('B',$shift->get_required_position());
        $this->assert_equals('spec reqs',$shift->get_special_requirements());
        $this->assert_equals("12345-6789",$shift->get_zip_code());
        $this->assert_equals(1,$shift->get_organization_id());
        $this->assert_equals(Shift::STATUS_ASSIGNED, $shift->get_status());
        
    }

    public function get_shifts_between() {
        $shifts = $this->shiftService->get_shifts_between(1, Shift::STATUS_ASSIGNED, strtotime("2019-05-27 00:00:00"), strtotime("2019-05-29 00:00:00"));
        $this->assert_equals(4,sizeof($shifts));
        $this->assert_equals(4, $shifts[0]->get_shift_id());
        $this->assert_equals(5, $shifts[3]->get_shift_id());
    }

    public function get_shift() {
        $shift = $this->shiftService->get_shift(2);
        $this->assert_equals(2, $shift->get_shift_id());
        $this->assert_equals(3, $shift->get_assigned_user_id());
        $this->assert_equals(strtotime("2019-05-26 02:00:00"),$shift->get_start_datetime());
        $this->assert_equals(strtotime("2019-05-26 10:00:00"),$shift->get_end_datetime());
        $this->assert_equals('B',$shift->get_required_position());
        $this->assert_equals('spec reqs',$shift->get_special_requirements());
        $this->assert_equals("12345-6789",$shift->get_zip_code());
        $this->assert_equals(1,$shift->get_organization_id());
        $this->assert_equals(Shift::STATUS_ASSIGNED, $shift->get_status());      
    }

    public function add_shift() {
        $shift = new Shift();
        $shift->set_assigned_user_id(null);
        $startTime = strtotime("2019-05-26 02:00:00");
        $endTime = strtotime("2019-05-26 10:00:00");
        $shift->set_start_datetime($startTime);
        $shift->set_end_datetime($endTime);
        $shift->set_required_position("RN");
        $shift->set_pay_differential(19.20);
        $shift->set_zip_code("21045");
        $shift->set_organization_id(1);
        $shift->set_status(Shift::STATUS_UNASSIGNED);
        $this->shiftService->add_shift($shift);

        $shift_id = $this->sqlService->get_last_id();
        $newShift = $this->shiftService->get_shift($shift_id);
        $this->assert_equals(null, $newShift->get_assigned_user_id());
        $this->assert_equals($startTime, $newShift->get_start_datetime());
        $this->assert_equals($endTime,$newShift->get_end_datetime());
        $this->assert_equals($startTime,$newShift->get_start_datetime());
        $this->assert_equals($endTime,$newShift->get_end_datetime());
        $this->assert_equals("RN",$newShift->get_required_position());
        $this->assert_equals("19.20",$newShift->get_pay_differential());
        $this->assert_equals("21045",$newShift->get_zip_code());
        $this->assert_equals(1,$newShift->get_organization_id());
        $this->assert_equals(Shift::STATUS_UNASSIGNED,$newShift->get_status());
    }

    public function modify_shift() {
        $shift = $this->shiftService->get_shift(12);
        $shift->set_assigned_user_id(2);
        $startTime = strtotime("2019-05-26 02:00:00");
        $endTime = strtotime("2019-05-26 10:00:00");
        $shift->set_start_datetime($startTime);
        $shift->set_end_datetime($endTime);
        $shift->set_required_position("RN");
        $shift->set_pay_differential(19.20);
        $shift->set_zip_code("21045");
        $shift->set_organization_id(1);
        $shift->set_status(Shift::STATUS_ASSIGNED);
        $this->shiftService->modify_shift($shift);

        $newShift = $this->shiftService->get_shift(12);
        $this->assert_equals(2, $newShift->get_assigned_user_id());
        $this->assert_equals($startTime, $newShift->get_start_datetime());
        $this->assert_equals($endTime,$newShift->get_end_datetime());
        $this->assert_equals($startTime,$newShift->get_start_datetime());
        $this->assert_equals($endTime,$newShift->get_end_datetime());
        $this->assert_equals("RN",$newShift->get_required_position());
        $this->assert_equals("19.20",$newShift->get_pay_differential());
        $this->assert_equals("21045",$newShift->get_zip_code());
        $this->assert_equals(1,$newShift->get_organization_id());
        $this->assert_equals(Shift::STATUS_ASSIGNED,$newShift->get_status());

        $requests = $this->sqlService->execute_query("SELECT * FROM Requests WHERE Status=?;","d",Request::STATUS_DENIED);
        $this->assert_equals(2,sizeof($requests[1]));
    }
} //end class UserTests
$fixture = new ShiftTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
