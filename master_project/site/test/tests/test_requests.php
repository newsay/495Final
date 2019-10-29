<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/request.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/shift_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/request_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/authentication_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of UserService and User class.
 */
class RequestTests extends TestFixture {
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
        $this->requestService = new RequestService($this->sqlService, $this->shiftService, $this->userService);
        parent::__construct("ShiftTest",$configuration);
    } //end function __construct
    /**
     * Return the list of tests for the UserService (by function name)
     */
    protected function get_tests() {
        return array(
            "get_requests",
            "submit_request",
            "approve_request",
            "deny_request"
        );
    } //end function get_tests
    
    public function case_setup() {
        test_data_setup($this->sqlService);
    }

    public function get_requests() {
        $requests = $this->requestService->get_requests(1);
        $this->assert_equals(3,sizeof($requests));
        $request = $requests[1];
        $this->assert_equals(2,$request->get_request_id());
        $this->assert_equals(13,$request->get_shift_id());
        $this->assert_equals(2,$request->get_user_id());
        $this->assert_equals(Request::STATUS_PENDING,$request->get_status());
        $this->assert_equals(Request::REQUEST_CANCELLATION,$request->get_type());

        $requests = $this->requestService->get_requests(1,Request::STATUS_PENDING);
        $this->assert_equals(3,sizeof($requests));

        $requests = $this->requestService->get_requests(1,null,3);
        $this->assert_equals(1,sizeof($requests));
        
        $requests = $this->requestService->get_requests(1,Request::STATUS_PENDING,2);
        $this->assert_equals(2,sizeof($requests));
    }

    public function submit_request() {
        $this->requestService->submit_request(Request::REQUEST_CANCELLATION,2,5);
        $requests = $this->requestService->get_requests(1);
        
        $this->assert_equals(4, sizeof($requests));
        $request = $requests[3];
        $this->assert_equals(5,$request->get_shift_id());
        $this->assert_equals(2,$request->get_user_id());
        $this->assert_equals(Request::STATUS_PENDING,$request->get_status());
        $this->assert_equals(Request::REQUEST_CANCELLATION,$request->get_type());
    }
    public function approve_request() {
        $this->requestService->approve_request(1);
        $requests = $this->requestService->get_requests(1);
        $this->assert_equals(Request::STATUS_APPROVED, $requests[0]->get_status());
        $this->assert_equals(Request::STATUS_PENDING, $requests[1]->get_status());
        $this->assert_equals(Request::STATUS_DENIED, $requests[2]->get_status());
        $shift = $this->shiftService->get_shift(12);
        $this->assert_equals(2,$shift->get_assigned_user_id());
    }

    public function deny_request() {
        $this->requestService->deny_request(1);
        $requests = $this->requestService->get_requests(1);
        $this->assert_equals(Request::STATUS_DENIED, $requests[0]->get_status());
        $this->assert_equals(Request::STATUS_PENDING, $requests[1]->get_status());
        $this->assert_equals(Request::STATUS_PENDING, $requests[2]->get_status());
    }
} //end class RequestTests
$fixture = new RequestTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
