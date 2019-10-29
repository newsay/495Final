<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

class LocalConfiguration implements IConfiguration {
    private static $config = null;
    /**
     * Initialize a singleton configuration instance.
     * See https://www.geeksforgeeks.org/singleton-class-java/ for description of a Singleton
     * A getter for retrieving the Configuration object is provided, 
     * and the constructor for the object is private.
     * 
     * @return An instance of Configuration type
     */
    public static function get_configuration() {
        if (self::$config == null) {
            self::$config = new LocalConfiguration();
        }
        return self::$config;
    } //end function get_configuration
    
    /**
     * Private constructor: Same as default constructor, except private.
     */
    private function __construct() {
    } //end function __construct
    
    //Sql configuration
    
    /**
     * Get the hostname of the sql server, i.e. sql.place.com
     * @return Hostname of SQL server
     */
    public function sql_server_name() {
        return "csc495.cmmdzy4o0hbl.us-east-2.rds.amazonaws.com";
    } //end function sql_server_name
    
    /**
     * Get the user to access the sql server
     * @return Username of SQL Server
     */
    public function sql_server_user() {
        return "aritchie";
    } //end function sql_server_user 
    
    /**
     * Get the password of the sql server
     * @return Password of SQL server
     */
    public function sql_server_password() {
        return "pFfcm4hUC8kNabxR";
    } //end function sql_server_password
    
    /**
     * Get the database to use in the sql server
     * @return The database name
     */ 
    public function sql_server_db_name() {
        return "db";
    } //end function sql_server_db_name
    
    /**
     * Get the port of the SQL server
     * @return The port number
     */
    public function sql_server_db_port() {
        return 3306; //default
    } //end function sql_server_db_port
} //end class Configuration
