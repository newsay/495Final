<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/test_fixture.php";

/**
 * An example Test file to use as a guide.
 */
class SampleTestFixture extends TestFixture {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct("#1", new TestConfiguration());
    } //end function __construct
    
    /**
     * Return the list of tests for the SQLConnectionService (by function name)
     */
    protected function get_tests() {
        return array(
            "assert_equals_test",
            "assert_equals_test_fail",
            "assert_not_equals_test",
            "assert_not_equals_test_fail"
        );
    } //end method get_tests
    
    /**
     * Check to make sure assert_equals works, which fails if the two values do not match.
     */
    protected function assert_equals_test() {
        $this->assert_equals(5,5);
    }
    /**
     * This test will fail; it tests if two values match.
     */
    protected function assert_equals_test_fail() {
        $this->assert_equals(4,5);
    }
    /**
     * Check to make sure assert_not_equals works, which fails if two values match.
     */
    protected function assert_not_equals_test() {
        $this->assert_not_equals(4,5);
    }
    /**
     * This test will fail; it tests if two values do not match.
     */
    protected function assert_not_equals_test_fail() {
        $this->assert_not_equals(5,5);
    }
} //end class SampleTestFixture

$fixture = new SampleTestFixture();
$fixture->run();
echo $fixture->get_result_as_json();
