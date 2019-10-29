<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/test/testconfig.php";
/**
 * Base class to define Tests for a given component of the application.
 * See tests/test_tester.php and tests/test_connection.php for examples.
 * Override the methods fixture_setup and fixture_teardown to perform per-fixture setup (once before/after entire fixture is run)
 * Override the methods case_setup and case_teardown to perform per-test case setup (once before/after each test case is run)
 * @author Andrew Ritchie
 */
abstract class TestFixture
{
    private $tests = array();
    //Override for setup/teardown
    /**
     * Set up a test fixture prior to execution of any test cases.
     */
    protected function fixture_setup() {}
    /**
     * Cleanup a test fixture after execution of all test cases.
     */
    protected function fixture_teardown() {}
    /**
     * Set up prior to execution of each test cases.
     */
    protected function case_setup() {}
    /**
     * Set up after execution of each test case.
     */
    protected function case_teardown() {}
    
    //Private variables
    private $name;
    private $result;
    private $test_success;
    private $test_count;
    protected $configuration;
    /**
     * Constructor: Given a test name and a configuration, create a TestFixture.
     */
    public function __construct($name, $configuration=null)
    {
        $this->name = $name;
        if (!$configuration) {
            $this->configuration = Configuration::get_configuration();
        } else {
            $this->configuration = $configuration;
        }
    } //end constructor 
    
    /**
     * Obsolete, to delete
     */
    public function add_test(Test $test)
    {
        array_push($this->tests, $test);
    }
    
    /**
     * Run all tests in the test fixture.
     */
    public function run()
    {
        $this->result = "";
        $this->test_count = 0;
        $this->test_success = 0;
        $tests = $this->get_tests();
        $this->result .= "<table><tr><th>Test Case</th><th>Result</th><th>Detail</th></tr>";
        //First, run the test fixture setup
        
        try {
            $this->fixture_setup();
        }
        catch (Exception $ex) {
            $this->result .= "Error in test fixture setup: " . $ex;
            return;
        }
        try {
            foreach ($tests as $test) {
                //For each test, run a test case
                $this->case_setup();
                $this->test_count++;
                try {
                    call_user_func(array($this,$test)); //Call the test by string name
                    $this->result .= "<tr>";
                    $this->result .= "<td>" . $test . "</td> ";
                    $this->result .= "<td>Pass</td><td></td>";
                    $this->result .= "</tr>";
                    $this->test_success++;
                } catch (Exception $ex) {
                    //If it fails, log a failure.
                    $this->result .= "<tr class='failure'>";
                    $this->result .= "<td>" . $test . "</td>";
                    $this->result .= "<td> Fail</td>";
                    $this->result .= "<td><strong>" . htmlentities($ex->getMessage()) . "</strong><p>" . htmlentities(nl2br($ex->getTraceAsString())) . "</td>";
                }
                finally {
                    //Cleanup the test after a test is run.
                    $this->case_teardown();
                }
                $this->result .= "</tr>";
            }
            
        }
        finally {
            //Clean up the test fixture after all tests have run.
            $this->fixture_teardown();
            $this->result .= "</table><p><hr>";
        }
        //Construct the result.
        $prefix = "<div";
        if ($this->test_count != $this->test_success) {
            $prefix .= " style='color: red'";
        }
        $prefix .=  ">";
        $prefix .=  "<strong>" .$this->test_success . "/" . $this->test_count . " tests passed<p></strong>";
        $this->result = $prefix . "</div>" . $this->result;
        $this->result = $this->result;
    } //end function run
    
    /**
     * Check if an expected value equals an actual value. A message is optional.
     * @param $expected: The expected value.
     * @param $actual: The actual value.
     * @param $message: A message to show if the assertion fails.
     */
    protected function assert_equals($expected, $actual, $message = null)
    {
        if ($expected !== $actual) { 
            if ($message != null) {
                throw new Exception($message);
            }
            else {
                throw new Exception("Assert Failed: Expected " . $expected . " but received " . $actual);
            }
        }
    } //end function assert_equals
    /**
     * Check if an expected value is not equal to an actual value. A message is optional.
     * @param $expected: The expected value.
     * @param $actual: The actual value.
     * @param $message: A message to show if the assertion fails.
     */
    protected function assert_not_equals($expected, $actual, $message = null)
    {
        if ($expected === $actual) { 
            throw new Exception("Assert Failed: Expected not " . $expected . " but received " . $actual .
            ($message != null ? '<br>' . $message : ''));
        }
    } //end function assert_not_equals
    
    /**
     * Check if a value is true.
     * @param $actual: The value to check if true.
     * @param $message: A message to show if the assertion fails.
     */
    protected function assert_is_true($actual, $message = null)
    {
        if ($actual != true) { 
            throw new Exception("Assert Failed: Expected true but received " . $actual .
                                ($message != null ? '<br>' . $message : ''));
        }
    } //end function assert_is_true
    
    /**
     * Check if a value is false.
     * @param $actual: The value to check if true.
     * @param $message: A message to show if the assertion fails.
     */
    protected function assert_is_false($actual, $message = null)
    {
        if ($actual != false) { 
            throw new Exception("Assert Failed: Expected false but received " . $actual .
                                ($message != null ? '<br>' . $message : ''));
        }
    } //end function assert_is_false
    
    /**
     * Get the detailed result of a test. Shows a header, number of tests passed, and a table of tests and their results.
     */
    public function get_detailed_results() {
        return $this->result;
    } //end function get_detailed_results
    /**
     * Get a summary of a test. Shows number of tests that passed
     */
    public function get_summary() {
        return $this->test_success . "/" . $this->test_count . " tests passed.";
    } //end function get_summary
    /**
     * Get whether the test succeeded.
     */
    public function get_success() {
        return $this->test_success == $this->test_count;
    } //end function get_success
    
    /**
     * Get the name of the test
     */
    public function get_name() {
        return $this->name;
    } //end function get_name
    
    /**
     * Get a result as a JSON object to be retrieved by an HTTP Request, to be transformed into a JavaScript object.
     */
    public function get_result_as_json() {
        return "{" .
                    '"name": "' . addslashes($this->get_name()) . '", ' .
                    '"result": "' . str_replace('"', '\\"', str_replace(array("\n", "\r"), '', nl2br($this->get_detailed_results()))) . '", ' .
                    '"summary": "' . addslashes($this->get_summary())  . '", ' .
                    '"success": "' . $this->get_success() . '" ' .
                "}";
    } //end function get_result_as_json
    protected abstract function get_tests(); //Expects an array of strings
} //end class TestFixture
