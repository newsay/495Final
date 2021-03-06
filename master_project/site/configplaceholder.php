<?php
/**
 * Configuration file
 * Includes connection strings, including SQL server name, SQL server user, SQL password,
 * SQL database name, SQL server port
 * Passed into various classes for usage. (Dependency Injection)
 * @authors Andrew Ritchie
 */
 
interface IConfiguration {
     /**
     * Get the hostname of the sql server, i.e. sql.place.com
     * @return Hostname of SQL server
     */
    public function sql_server_name();
    
    /**
     * Get the user to access the sql server
     * @return Username of SQL Server
     */
    public function sql_server_user();
    
    /**
     * Get the password of the sql server
     * @return Password of SQL server
     */
    public function sql_server_password();
    
    /**
     * Get the database to use in the sql server
     * @return The database name
     */ 
    public function sql_server_db_name();
    
    /**
     * Get the port of the SQL server
     * @return The port number
     */
    public function sql_server_db_port();
} //end interface IConfiguration


class Configuration implements IConfiguration {
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
            self::$config = new Configuration();
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
        return "servername";
    } //end function sql_server_name
    
    /**
     * Get the user to access the sql server
     * @return Username of SQL Server
     */
    public function sql_server_user() {
        return "serveruser";
    } //end function sql_server_user 
    
    /**
     * Get the password of the sql server
     * @return Password of SQL server
     */
    public function sql_server_password() {
        return "serverpassword";
    } //end function sql_server_password
    
    /**
     * Get the database to use in the sql server
     * @return The database name
     */ 
    public function sql_server_db_name() {
        return "serverdb";
    } //end function sql_server_db_name
    
    /**
     * Get the port of the SQL server
     * @return The port number
     */
    public function sql_server_db_port() {
        return 3306; //default
    } //end function sql_server_db_port
} //end class Configuration
