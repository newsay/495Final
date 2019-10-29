<?php
/**
 * Model to represent a user object. 
 * @author Andrew Ritchie
 */
class User {
    //Private variables
    private $user_id;
    private $email_address;
    private $user_type;
    private $first_name;
    private $last_name;
    private $home_phone;
    private $mobile_phone;
    private $address_1;
    private $address_2;
    private $city;
    private $state;
    private $zip;
    private $organization_id;
    private $shifts = array();
    private $requests = array();
    const USER_TYPE_EMPLOYEE = 0;
    const USER_TYPE_MANAGER = 1;
    const USER_TYPE_ADMINISTRATOR = 2;
    
    /**
     * Constrcutor: Given a user ID, a user name, and whether the user is an administrator, create a user object
     * @param $user_id: Integer, database ID of user
     * @param $email_address: String, user name of user
     * @param $is_admin: Boolean, whether the user is an admin
     */
    public function __construct($user_id, $email_address, $user_type) {
        $this->user_id = $user_id;
        $this->email_address = $email_address;
        $this->user_type = $user_type;
    } //end function __construct

    /**
     * Return the database ID of the user
     * @return Integer, Database ID of user
     */
    public function get_user_id() {
        return $this->user_id;
    } //end function get_user_id

    /**
     * Return the email address of the user
     * @return String, email address of user
     */
    public function get_email_address() {
        return $this->email_address;
    } //end function get_email_address
    
    /**
     * Set the email address of the user
     * @return String, email address of user
     */
    public function set_email_address($email_address) {
        $this->email_address = $email_address;
    } //end function get_email_address

    /**
     * Return the numeric type of the user
     * @return Number, type of user
     */
    public function get_user_type() {
        return $this->user_type;
    } //end function get_user_type

    
        /**
    * Retrieve the first_name value
    * @return The first_name value
    */
    public function get_first_name() {
        return $this->first_name;
    } //end method get_first_name
    /**
    * Set the first_name value
    * @param first_name: The first_name value to set
    */
    public function set_first_name($first_name) {
        $this->first_name = $first_name;
    } //end method set_first_name
    /**
    * Retrieve the last_name value
    * @return The last_name value
    */
    public function get_last_name() {
        return $this->last_name;
    } //end method get_last_name
    /**
    * Set the last_name value
    * @param last_name: The last_name value to set
    */
    public function set_last_name($last_name) {
        $this->last_name = $last_name;
    } //end method set_last_name
    /**
    * Retrieve the home_phone value
    * @return The home_phone value
    */
    public function get_home_phone() {
        return $this->home_phone;
    } //end method get_home_phone
    /**
    * Set the home_phone value
    * @param home_phone: The home_phone value to set
    */
    public function set_home_phone($home_phone) {
        $this->home_phone = $home_phone;
    } //end method set_home_phone
    /**
    * Retrieve the mobile_phone value
    * @return The mobile_phone value
    */
    public function get_mobile_phone() {
        return $this->mobile_phone;
    } //end method get_mobile_phone
    /**
    * Set the mobile_phone value
    * @param mobile_phone: The mobile_phone value to set
    */
    public function set_mobile_phone($mobile_phone) {
        $this->mobile_phone = $mobile_phone;
    } //end method set_mobile_phone
    /**
    * Retrieve the address_1 value
    * @return The address_1 value
    */
    public function get_address_1() {
        return $this->address_1;
    } //end method get_address_1
    /**
    * Set the address_1 value
    * @param address_1: The address_1 value to set
    */
    public function set_address_1($address_1) {
        $this->address_1 = $address_1;
    } //end method set_address_1
    /**
    * Retrieve the address_2 value
    * @return The address_2 value
    */
    public function get_address_2() {
        return $this->address_2;
    } //end method get_address_2
    /**
    * Set the address_2 value
    * @param address_2: The address_2 value to set
    */
    public function set_address_2($address_2) {
        $this->address_2 = $address_2;
    } //end method set_address_2
    /**
    * Retrieve the city value
    * @return The city value
    */
    public function get_city() {
        return $this->city;
    } //end method get_city
    /**
    * Set the city value
    * @param city: The city value to set
    */
    public function set_city($city) {
        $this->city = $city;
    } //end method set_city
    /**
    * Retrieve the state value
    * @return The state value
    */
    public function get_state() {
        return $this->state;
    } //end method get_state
    /**
    * Set the state value
    * @param state: The state value to set
    */
    public function set_state($state) {
        $this->state = $state;
    } //end method set_state
    /**
    * Retrieve the zip value
    * @return The zip value
    */
    public function get_zip() {
        return $this->zip;
    } //end method get_zip
    /**
    * Set the zip value
    * @param zip: The zip value to set
    */
    public function set_zip($zip) {
        $this->zip = $zip;
    } //end method set_zip
    /**
    * Retrieve the organization_id value
    * @return The organization_id value
    */
    public function get_organization_id() {
        return $this->organization_id;
    } //end method get_organization_id
    /**
    * Set the organization_id value
    * @param organization_id: The organization_id value to set
    */
    public function set_organization_id($organization_id) {
        $this->organization_id = $organization_id;
    } //end method set_organization_id
    
    /**
     * Get the list of shifts
     * @return Array of shifts
     */
    public function get_shifts() {
        return $this->shifts;
    } //end method get_shifts
    
    /**
     * Add to the list of shifts
     * @param $shift: Shift to add
     */
     public function add_shift($shift) {
         array_push($this->shifts, $shift);
     } //end method add_shift
     
     /**
      * Remove from the list of shifts if exists
      * @param $shift: Shift to remove
      * @return Whether the shift was removed
      */
      public function delete_shift($shift) {
          $found_shift = -1;
          for ($i = 0; $i < sizeof($this->shifts); $i++) {
              $thisshift = $this->shifts[$i];
              if ($shift->get_shift_id() == $thisshift->get_shift_id) {
                  $found_shift = $i;
              }
          }
          if ($found_shift != -1) {
              unset($shifts[$i]);
              $shifts = array_values($shifts);
              return true;
          }
          else {
              return false;
          }
      }
      
      /**
     * Get the list of requests
     * @return Array of requests
     */
    public function get_requests() {
        return $this->requests;
    } //end method get_requests
    
    /**
     * Add to the list of requests
     * @param $request: request to add
     */
     public function add_request($request) {
         array_push($this->requests, $request);
     } //end method add_request
     
     /**
      * Remove from the list of requests if exists
      * @param $request: request to remove
      * @return Whether the request was removed
      */
      public function delete_request($request) {
          $found_request = -1;
          for ($i = 0; $i < sizeof($this->requests); $i++) {
              $thisrequest = $this->requests[$i];
              if ($request->get_request_id() == $thisrequest->get_request_id) {
                  $found_request = $i;
              }
          }
          if ($found_request != -1) {
              unset($requests[$i]);
              $requests = array_values($requests);
              return true;
          }
          else {
              return false;
          }
      }
} //end class User
