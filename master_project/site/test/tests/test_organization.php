<?php

include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/organization_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/helpers/populate_test_data.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";

/**
 * Test to verify functionality of OrganizationService and Organization class.
 */
class OrganizationTests extends TestFixture {
    private $organizationService;
    private $sqlService;
    /**
     * Given a configuration, call the parent constructor.
     * @param $configuration: A Configuration object. This will likely be a TestConfiguration.
     */
    public function __construct($configuration=null) {
        $this->sqlService = SQLConnectionService::get_instance($configuration);
        $this->organizationService = new OrganizationService($this->sqlService);
        parent::__construct("UserTest",$configuration);
    } //end function __construct
    /**
     * Return the list of tests for the UserService (by function name)
     */
    protected function get_tests() {
        return array(
            "create_organization"
            ,"get_organizations"
            ,"get_organization"
            ,"disable_organization"
            ,"enable_organization"
        );
    } //end function get_tests
    
    public function case_setup() {
        test_data_setup($this->sqlService);
    }
    public function create_organization() {
        //Test to verify organization can be created
        $id = $this->organizationService->create_organization("Test");
        $this->assert_not_equals(0,$id);
        
    }
    public function get_organizations() {
        $org = $this->organizationService->get_organizations()[0];
        $this->assert_equals(1,$org->get_id());
        $this->assert_equals("Test Organization",$org->get_name());
        $this->assert_is_true($org->get_is_enabled());
    }
    public function get_organization() {
        $org = $this->organizationService->get_organization(1);
        $this->assert_equals(1,$org->get_id());
        $this->assert_equals("Test Organization",$org->get_name());
        $this->assert_is_true($org->get_is_enabled());
    }
    public function disable_organization() {
        $this->organizationService->disable_organization(1);
        $org = $this->organizationService->get_organization(1);
        $this->assert_is_false($org->get_is_enabled());
    }

    public function enable_organization() {
        $this->sqlService->execute_query("UPDATE Organizations SET IsEnabled=0;");
        $this->organizationService->enable_organization(1);
        $org = $this->organizationService->get_organization(1);
        $this->assert_is_true($org->get_is_enabled());
    }
    
} //end class UserTests
$fixture = new OrganizationTests(new TestConfiguration());
$fixture->run();
echo $fixture->get_result_as_json();
