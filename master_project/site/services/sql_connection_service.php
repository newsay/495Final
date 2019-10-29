<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
/**
 * @author Andrew Ritchie
 */
interface ISQLConnectionService
{
    public function connect();
    public function is_connected();
    public function disconnect();
    public function execute_query($query, $paramtypes, ...$params);
    public function execute_multiple($query);
    public function get_last_id();
}


/**
 * Class to represent a SQL connection.
 * Purpose is to abstract the inner workings of how SQL works from other classes, other than the SQL query syntax.
 */
class SQLConnectionService implements ISQLConnectionService
{
    //Private variables
    private $configuration;     //A Configuration object
    private $conn = null;       //A mysqli object
    private $connected = false; //Whether the mysqli object is connected
    private $error = null;      //Any error that occurs

    //Class variables
    private static $instances = array();
    /**
     * Private constructor
     * The static variables of this class maintain a list of currently active SQL connections by configuration
     * Each class is only instantiated a single time per configuration.
     * @param $configuration: Configuration object to be used for resolving hostname, user, password, and db. If null, default to default Configuration object.
     * 
     */
    private function __construct($configuration = null)
    {
        if ($configuration == null) {
            $configuration = Configuration::get_configuration();
        }
        $this->configuration = $configuration;
    } //end constructor

    /**
     * Get an instance of the object with a given Configuration object.
     * If it exists, use the existing one; if it does not exist, create a new one.
     * @param $configuration: Configuration object to be used for resolving hostname, user, password, and db. If null, default to default Configuration object.
     */
    public static function get_instance($configuration = null)
    {
        if ($configuration == null) {
            $configuration = Configuration::get_configuration();
        }
        $configuration_type = gettype($configuration);
        if (!array_key_exists($configuration_type, self::$instances)) {
            self::$instances[$configuration_type] = new SQLConnectionService($configuration);
        }
        return self::$instances[$configuration_type];
    } //end function get_instance

    /**
     * Attempt to connect to the SQL server. If the server is already connected, do nothing.
     * @throws: Exception if there is a connection error.
     */
    public function connect()
    {
        if ($this->connected) {
            return;
        }
        $this->conn = new mysqli(
            $this->configuration->sql_server_name(),
            $this->configuration->sql_server_user(),
            $this->configuration->sql_server_password(),
            $this->configuration->sql_server_db_name(),
            $this->configuration->sql_server_db_port()
        ) or die(mysql_error());;
        if ($this->conn->connect_error) {
            $this->error = $this->conn->connect_error;
            throw (new Exception($this->error));
        } else {
            $this->connected = true;
        }
    } //end function connect

    /**
     * Get whether the SQL server is currently connected.
     * @return Whether the SQL server is connected
     */
    public function is_connected()
    {
        return $this->connected == true;
    } //end function is_connected

    /**
     * Get the error of a connection, if one exists.
     * @return String, error of connection
     */
    public function get_error()
    {
        return $this->error;
    } //end function get_error

    /**
     * Disconnect from the SQL server. If already disconnected, do nothing.
     */
    public function disconnect()
    {
        if ($this->connected) {
            $this->conn->close();
            $this->connected = false;
        }
    } //end function disconnect

    /**
     * Run a parameterized SQL query.
     * @param $query: The SQL query to execute, i.e. SELECT * FROM Users WHERE UserId=?
     * @param $paramtypes: The parameters of the SQL query, i.e. "d" or "sss"
     * @param $params: A list of parameters for the SQL query.
     */
    public function execute_query($query, $paramtypes = null, ...$params)
    {
        if (!$this->connected) {
            throw new Exception("Not connected to SQL database");
        }
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error in generating prepared statement " . $query . "\n" . $this->conn->error);
        }
        if ($paramtypes != null && $params != null) {
            //array_unshift($params, $paramtypes);
            //call_user_func_array(array($stmt,"bind_param"),$params);
            $stmt->bind_param($paramtypes, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $results = array($stmt->affected_rows, array());
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                array_push($results[1], $row);
            }
        }
        $stmt->close();
        return $results;
    } //end function execute_query

    /**
     * Execute multiple queries.
     * NOTE: This is not parameterized, use with caution
     */
    public function execute_multiple($query)
    {
        if (!$this->connected) {
            throw new Exception("Not connected to SQL database");
            //TODO: Implement
        }
        if (!$this->conn->multi_query($query)) {
            throw new Exception($this->conn->error);
        } else {
            do {
                $result = $this->conn->store_result();
            } while ($this->conn->next_result());
        }
    } //end method execute_multiple

    /**
     * Get the ID of the last record created
     * @return Last ID created
     */
    public function get_last_id()
    {
        return $this->conn->insert_id;
    }
} //end class SQLConnectionService
