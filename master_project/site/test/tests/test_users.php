<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/authentication_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of UserService and User class.
 */
class UserTests extends TestFixture {
    private $userService;
    private $sqlService;
    private $authService;
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
        $this->sqlService = SQLConnectionService::get_instance($configuration);
        $this->userService = new UserService();
        $this->authService = new AuthenticationService(null,$this->sqlService);
        parent::__construct("UserTest",$configuration);
    } //end function __construct
    /**
     * Return the list of tests for the UserService (by function name)
     */
    protected function get_tests() {
        return array(
            "construct",
            "get_users",
            "get_user_by_id",
            "get_user_by_email_address",
            "create_user",
            "update_password",
            "update_password_and_security_questions",
            "modify_user",
            "delete_user"
        );
    } //end function get_tests
    
    /**
     * Test to verify a User is properly constructed and returns the valid user ID, user name, and if the user is an admin.
     */
    public function construct() {
        $u = new User(1,"user1@gmail.com",0);
        $this->assert_equals(1,$u->get_user_id());
        $this->assert_equals("user1@gmail.com",$u->get_email_address());
        $this->assert_is_false($u->get_user_type() == User::USER_TYPE_ADMINISTRATOR);
    } //end method construct
    /**
     * Test to verify all users are retrieved by get_users
     */
    public function get_users() {
        test_data_setup($this->sqlService);
        $users = $this->userService->get_users(1);
        $this->assert_equals("user1@gmail.com",$users[0]->get_email_address());
        $this->assert_equals(1,$users[0]->get_user_id());
        $this->assert_is_false($users[0]->get_user_type() == User::USER_TYPE_ADMINISTRATOR);
        $this->assert_is_true($users[2]->get_user_type() == User::USER_TYPE_MANAGER);
        $this->assert_equals("Manager",$users[2]->get_first_name());
        $this->assert_equals("One",$users[2]->get_last_name());
        $this->assert_equals("555-555-5555",$users[2]->get_home_phone());
        $this->assert_equals("333-333-3333",$users[2]->get_mobile_phone());
        $this->assert_equals("800 Freedom Blvd",$users[2]->get_address_1());
        $this->assert_equals("Apt A",$users[2]->get_address_2());
        $this->assert_equals("Columbia",$users[2]->get_city());
        $this->assert_equals("Maryland",$users[2]->get_state());
        $this->assert_equals("88888",$users[2]->get_zip());
        $this->assert_equals(1,$users[2]->get_organization_id());
    } //end method retrieve
    
    /**
     * Test to verify only a specific user is retrieved by get_user_by_id
     */
    public function get_user_by_id() {
        test_data_setup($this->sqlService);
        $user = $this->userService->get_user_by_id(3, 1);
        $this->assert_equals("manager1@gmail.com",$user->get_email_address());
        $this->assert_equals(3,$user->get_user_id());
        $this->assert_is_true($user->get_user_type() == User::USER_TYPE_MANAGER);
        $this->assert_equals("Manager",$user->get_first_name());
        $this->assert_equals("One",$user->get_last_name());
        $this->assert_equals("555-555-5555",$user->get_home_phone());
        $this->assert_equals("333-333-3333",$user->get_mobile_phone());
        $this->assert_equals("800 Freedom Blvd",$user->get_address_1());
        $this->assert_equals("Apt A",$user->get_address_2());
        $this->assert_equals("Columbia",$user->get_city());
        $this->assert_equals("Maryland",$user->get_state());
        $this->assert_equals("88888",$user->get_zip());
        $this->assert_equals(1,$user->get_organization_id());
    } //end method retrieve_by_id
    /**
     * Test to verify only a specific user is retrieved by get_user_by_name
     */
    public function get_user_by_email_address() {
        test_data_setup($this->sqlService);
        $user = $this->userService->get_user_by_email_address("manager1@gmail.com", 1);
         $this->assert_equals("manager1@gmail.com",$user->get_email_address());
        $this->assert_equals(3,$user->get_user_id());
        $this->assert_is_true($user->get_user_type() == User::USER_TYPE_MANAGER);
        $this->assert_equals("Manager",$user->get_first_name());
        $this->assert_equals("One",$user->get_last_name());
        $this->assert_equals("555-555-5555",$user->get_home_phone());
        $this->assert_equals("333-333-3333",$user->get_mobile_phone());
        $this->assert_equals("800 Freedom Blvd",$user->get_address_1());
        $this->assert_equals("Apt A",$user->get_address_2());
        $this->assert_equals("Columbia",$user->get_city());
        $this->assert_equals("Maryland",$user->get_state());
        $this->assert_equals("88888",$user->get_zip());
        $this->assert_equals(1,$user->get_organization_id());
    } //end method retrieve_by_name
    
    public function create_user() {
        test_data_setup($this->sqlService);
        $user = new User(null, "test@test.com", 1);
        $user->set_first_name("First");
        $user->set_last_name("Last");
        $user->set_home_phone("111-111-1111");
        $user->set_mobile_phone("000-000-0000");
        $user->set_address_1("111 A Street NW");
        $user->set_address_2("Apt 123");
        $user->set_city("Washington");
        $user->set_state("District of Columbia");
        $user->set_zip("12345");
        $user->set_organization_id(1);
        $userId = $this->userService->create_user($user, "mytest", 1, "question 1", 2, "question 2", 3, "question 3");
        $this->assert_is_true($this->authService->validate_user("test@test.com","mytest"));
        $this->assert_is_true($this->authService->validate_security_questions("test@test.com", "question 1","question 2","question 3"));
        $users = $this->userService->get_users(1);
        $this->userService->get_users(1);
        $user = $users[sizeof($users)-1];
        $this->assert_equals("test@test.com",$user->get_email_address());
        $this->assert_is_true($user->get_user_type() == User::USER_TYPE_MANAGER);
        $this->assert_equals("First",$user->get_first_name());
        $this->assert_equals("Last",$user->get_last_name());
        $this->assert_equals("111-111-1111",$user->get_home_phone());
        $this->assert_equals("000-000-0000",$user->get_mobile_phone());
        $this->assert_equals("111 A Street NW",$user->get_address_1());
        $this->assert_equals("Apt 123",$user->get_address_2());
        $this->assert_equals("Washington",$user->get_city());
        $this->assert_equals("District of Columbia",$user->get_state());
        $this->assert_equals("12345",$user->get_zip());
        $this->assert_equals(1,$user->get_organization_id());
    }
    
    public function update_password() {
        test_data_setup($this->sqlService);
        $user = new User(null, "test@test.com", 1);
        $user->set_first_name("First");
        $user->set_last_name("Last");
        $user->set_home_phone("111-111-1111");
        $user->set_mobile_phone("000-000-0000");
        $user->set_address_1("111 A Street NW");
        $user->set_address_2("Apt 123");
        $user->set_city("Washington");
        $user->set_state("District of Columbia");
        $user->set_zip("12345");
        $user->set_organization_id(1);
        $this->userService->create_user($user, "mytest", 1, "question 1", 2, "question 2", 3, "question 3");
        
        $this->userService->update_password($this->sqlService->get_last_id(), "newpass");
        $this->assert_is_true($this->authService->validate_user("test@test.com","newpass"));
        
    }
    
     public function update_password_and_security_questions() {
        test_data_setup($this->sqlService);
        $user = new User(null, "test@test.com", 1);
        $user->set_first_name("First");
        $user->set_last_name("Last");
        $user->set_home_phone("111-111-1111");
        $user->set_mobile_phone("000-000-0000");
        $user->set_address_1("111 A Street NW");
        $user->set_address_2("Apt 123");
        $user->set_city("Washington");
        $user->set_state("District of Columbia");
        $user->set_zip("12345");
        $user->set_organization_id(1);
        $this->userService->create_user($user, "mytest", 1, "question 1", 2, "question 2", 3, "question 3");
        
        $this->userService->update_password_and_security_questions($this->sqlService->get_last_id(), "newpass", 1, "answer 1", 2, "answer 2", 3, "answer 3");
        $this->assert_is_true($this->authService->validate_user("test@test.com","newpass"));
        $this->assert_is_true($this->authService->validate_security_questions("test@test.com","answer 1", "answer 2", "answer 3"));
        
    }
    
    public function modify_user() {
        test_data_setup($this->sqlService);
        $user = $this->userService->get_user_by_id(1,1);
        $user->set_first_name("Test");
        $user->set_last_name("Name");
        $user->set_home_phone("222-222-2222");
        $user->set_mobile_phone("333-333-3333");
        $user->set_address_1("1234 Main St");
        $user->set_address_2("Ste 300");
        $user->set_city("Columbia");
        $user->set_state("MD");
        $user->set_zip("21045");
        $this->userService->modify_user($user);
        
        $user = $this->userService->get_user_by_id(1,1);
        $this->assert_equals("Test",$user->get_first_name());
        $this->assert_equals("Name",$user->get_last_name());
        $this->assert_equals("222-222-2222",$user->get_home_phone());
        $this->assert_equals("333-333-3333",$user->get_mobile_phone());
        $this->assert_equals("1234 Main St",$user->get_address_1());
        $this->assert_equals("Ste 300",$user->get_address_2());
        $this->assert_equals("Columbia",$user->get_city());
        $this->assert_equals("MD",$user->get_state());
        $this->assert_equals("21045",$user->get_zip());
    }
    
    public function delete_user() {
        test_data_setup($this->sqlService);
        $this->userService->delete_user(2);
        $user = $this->userService->get_user_by_id(2,1);        
        $this->assert_equals(null,$user,"User is not null");
        $shifts = $this->sqlService->execute_query("SELECT * FROM Shifts WHERE UserID = 2");
        $this->assert_equals(0,sizeof($shifts[1]));
        $shifts = $this->sqlService->execute_query("SELECT * FROM Shifts WHERE UserID IS NOT NULL");
        $this->assert_not_equals(0, sizeof($shifts[1]));
        $requests = $this->sqlService->execute_query("SELECT * FROM Requests WHERE UserID = 2");
        $this->assert_equals(0,sizeof($requests[1]));
        $requests = $this->sqlService->execute_query("SELECT * FROM Requests");
        $this->assert_not_equals(0, sizeof($requests[1]));
    }
} //end class UserTests
$fixture = new UserTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
