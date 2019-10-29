<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/services/sql_connection_service.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/models/organization.php';

interface IOrganizationService {
    /**
     * Create a new organization with a given name
     * @param $name: The name of the organization
     * @return: The ID of the new organization
     */
    public function create_organization($name);
    /**
     * Get all Organizations
     * @return: All organizations in the system
     */
    public function get_organizations();
    /**
     * Get Organization by ID
     * $id: The ID of the Organization
     * @return: The matching Organization
     */
    public function get_organization($id);
    /**
     * Set an Organization's status to Disabled, disallowing people from logging in.
     * $id: The ID of the Organization
     */
    public function disable_organization($id);
    /**
     * Set an Organization's status to Enabled, allowing people to log in.
     * $id: The ID of the Organization
     */
    public function enable_organization($id);
} //end interface IOrganizationService

/**
 * Class to create, retrieve, disable, and enable organizations.
 */
class OrganizationService implements IOrganizationService {
    private $sql_service;
    
    /**
     * Create a new OrganizationService given a SQLConnectionService
     * @param $sql_service: The SQLConnectionService to use for manipulating Organizations
     */
    public function __construct($sql_service) {
        $this->sql_service = $sql_service;
    } //end constructor
    
    /**
     * Create a new organization with a given name
     * @param $name: The name of the organization
     * @return: The ID of the new organization
     */
    public function create_organization($name) {
       $this->sql_service->execute_query("INSERT INTO Organizations (OrganizationName, IsEnabled) VALUES (?,1)","s",$name); 
       return $this->sql_service->get_last_id();
    }
    
    
    /**
     * Get all Organizations
     * @return: All Organization objects in the system
     */
    public function get_organizations() {
        $this->sql_service->connect();
        $rows = $this->sql_service->execute_query("SELECT OrganizationID, OrganizationName, IsEnabled FROM Organizations");
        $res = array();
        foreach ($rows[1] as $row) {
            array_push($res, new Organization($row["OrganizationID"],$row["OrganizationName"],$row["IsEnabled"]));
        }
        return $res;
    }
    
    /**
     * Get Organization by ID
     * $id: The ID of the Organization
     * @return: The matching Organization
     */
    public function get_organization($id) {
        $this->sql_service->connect();
        $rows = $this->sql_service->execute_query("SELECT OrganizationID, OrganizationName, IsEnabled FROM Organizations WHERE OrganizationID=?","d",$id);
        if (sizeof($rows[1]) > 0) {
            $row = $rows[1][0];
            return new Organization($row["OrganizationID"],$row["OrganizationName"],$row["IsEnabled"]);
        }
        else {
            return null;
        }
    }
    
    /**
     * Set an Organization's status to Disabled, disallowing people from logging in.
     * $id: The ID of the Organization
     */
    public function disable_organization($id) {
        $this->sql_service->connect();
        $this->sql_service->execute_query("UPDATE Organizations
                                            SET IsEnabled=0
                                            WHERE OrganizationID=?","d",$id);
    }
    /**
     * Set an Organization's status to Enabled, allowing people to log in.
     * $id: The ID of the Organization
     */
    public function enable_organization($id) {
        $this->sql_service->connect();
        $this->sql_service->execute_query("UPDATE Organizations
                                            SET IsEnabled=1
                                            WHERE OrganizationID=?","d",$id);
    }
} //end class OrganizationService
