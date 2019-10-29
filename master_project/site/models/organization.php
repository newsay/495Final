<?php
/**
 * Class to represent an organization structure.
 * An Organization is the main grouping system of the application. It
 * represents a company, a school, a hospital, etc.
 * @author Andrew Ritchie
 */
class Organization {
    private $id;
    private $name;
    private $is_enabled;
    private $users = array();
    /**
     * Constructor that takes in an id, name, and whether the Organization is enabled.
     * @param $id: Numeric, The database ID of the Organization
     * @param $name: String, The name of the Organization
     * @param $is_enabled: Boolean, whether the Organization is enabled.
     */
    public function __construct($id, $name, $is_enabled) {
        $this->id = $id;
        $this->name = $name;
        $this->is_enabled = $is_enabled;
    } //end constructor
    
    /**
     * Get the ID of the organization
     * @return ID of the organization
     */
    public function get_id() {
        return $this->id;
    } //end method get_id
    
    /**
     * Get the name of the organization
     * @return Name of the organization
     */
    public function get_name() {
        return $this->name;
    } //end method get_name
    
        /**
     * Get whether the organization is enabled
     * @return Whether the organization is enabled
     */
    public function get_is_enabled() {
        return $this->is_enabled;
    } //end method get_is_enabled
    
        /**
    * Get the list of users
    * @return Array of users
    */
    public function get_users() {
            return $this->users;
    } //end method get_users

    /**
    * Add to the list of users
    * @param $user: user to add
    */
    public function add_user($user) {
      array_push($this->users, $user);
    } //end method add_user
    
    /**
    * Remove from the list of users if exists
    * @param $user: user to remove
    * @return Whether the user was removed
    */
    public function remove_user($user) {
      $found_user = -1;
      for ($i = 0; $i < sizeof($this->users); $i++) {
          $thisuser = $this->users[$i];
          if ($user->get_user_id() == $thisuser->get_user_id) {
              $found_user = $i;
          }
      }
      if ($found_user != -1) {
          unset($users[$i]);
          $users = array_values($users);
          return true;
      }
      else {
          return false;
      }
    }
} //end class Organization
