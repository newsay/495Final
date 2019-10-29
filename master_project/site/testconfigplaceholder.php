<?php
/**
 * Configuration for Test cases; attaches to a different database.
 * @author Andrew Ritchie
 */
class TestConfiguration extends Configuration {
    public function __construct() {
        
    } //end constructor
    public function sql_server_name() {
        return "servername";
    } //end method sql_server_name
    public function sql_server_user() {
        return "serveruser";
    } //end method sql_server_user
    public function sql_server_password() {
        return "serverpassword"; //This is my only usage of this password
    }  //end method sql_server_password
    public function sql_server_db_name() {
        return "testdb";
    } //end method sql_server_db_name
} //end class TestConfiguration
