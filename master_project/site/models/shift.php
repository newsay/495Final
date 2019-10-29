<?php
/**
 * Class to represent a Shift. Shifts are the main source of object interaction
 * in the system, outside of Users. Users have multiple Shifts, a Request has
 * a Shift, Audit Trails have Shifts, and Organizations have Shifts.
 * 
 * @author Andrew Ritchie
 */
class Shift {
        private $shift_id;
        private $assigned_user_id;
        private $start_datetime;
        private $end_datetime;
        private $required_position;
        private $pay_differential;
        private $special_requirements;
        private $zip_code;
        private $organization_id;
        private $status;
        private $assigned_user;
        private $audit_trails = array();
        private $requests = array();

        const STATUS_UNASSIGNED = 0;
        const STATUS_ASSIGNED = 1;
        /**
         * Get the shift id property value
         * @return The shift id value
         */
        public function get_shift_id() {
                return $this->shift_id;
        }//end method get_shift_id

        /**
         * Set the shift id property value
         * @param $shift_id: The shift id value
         */
        public function set_shift_id($shift_id) {
                $this->shift_id = $shift_id;
        }//end method set_shift_id


        /**
         * Get the assigned user id property value
         * @return The assigned user id value
         */
        public function get_assigned_user_id() {
                return $this->assigned_user_id;
        }//end method get_assigned_user_id

        /**
         * Set the assigned user id property value
         * @param $assigned_user_id: The assigned user id value
         */
        public function set_assigned_user_id($assigned_user_id) {
                $this->assigned_user_id = $assigned_user_id;
        }//end method set_assigned_user_id

        /**
         * Get the assigned user property value
         * @return The assigned user value
         */
        public function get_assigned_user() {
                return $this->assigned_user;
        }//end method get_assigned_user

        /**
         * Set the assigned user property value
         * @param $assigned_user: The assigned user value
         */
        public function set_assigned_user($assigned_user) {
                $this->assigned_user = $assigned_user;
        }//end method set_assigned_user

        /**
         * Get the start datetime property value
         * @return The start datetime value
         */
        public function get_start_datetime() {
                return $this->start_datetime;
        }//end method get_start_datetime

        /**
         * Set the start datetime property value
         * @param $start_datetime: The start datetime value
         */
        public function set_start_datetime($start_datetime) {
                $this->start_datetime = $start_datetime;
        }//end method set_start_datetime

        /**
         * Get the end datetime property value
         * @return The end datetime value
         */
        public function get_end_datetime() {
                return $this->end_datetime;
        }//end method get_end_datetime

        /**
         * Set the end datetime property value
         * @param $end_datetime: The end datetime value
         */
        public function set_end_datetime($end_datetime) {
                $this->end_datetime = $end_datetime;
        }//end method set_end_datetime

        /**
         * Get the required position property value
         * @return The required position value
         */
        public function get_required_position() {
                return $this->required_position;
        }//end method get_required_position

        /**
         * Set the required position property value
         * @param $required_position: The required position value
         */
        public function set_required_position($required_position) {
                $this->required_position = $required_position;
        }//end method set_required_position

        /**
         * Get the pay differential property value
         * @return The pay differential value
         */
        public function get_pay_differential() {
                return $this->pay_differential;
        }//end method get_pay_differential

        /**
         * Set the pay differential property value
         * @param $pay_differential: The pay differential value
         */
        public function set_pay_differential($pay_differential) {
                $this->pay_differential = $pay_differential;
        }//end method set_pay_differential

        /**
         * Get the special requirements property value
         * @return The special requirements value
         */
        public function get_special_requirements() {
                return $this->special_requirements;
        }//end method get_special_requirements

        /**
         * Set the special requirements property value
         * @param $special_requirements: The special requirements value
         */
        public function set_special_requirements($special_requirements) {
                $this->special_requirements = $special_requirements;
        }//end method set_special_requirements
        
        /**
         * Get the zip code property value
         * @return The zip code value
         */
        public function get_zip_code() {
                return $this->zip_code;
        }//end method get_zip_code

        /**
         * Set the zip code property value
         * @param $zip_code: The zip code value
         */
        public function set_zip_code($zip_code) {
                $this->zip_code = $zip_code;
        }//end method set_zip_code

        /**
         * Get the organization id property value
         * @return The organization id value
         */
        public function get_organization_id() {
                return $this->organization_id;
        }
         
        /**
         * Set the organization id property value
         * @param $organization_id: The organization id value
         */
        public function set_organization_id($organization_id) {
                $this->organization_id = $organization_id;
        } //End method set_organization_id

        /**
         * Get the status property value
         * @return The status value
         */
        public function get_status() {
                return $this->status;
        } //End method get_status

        /**
         * Set the status property value
         * @param $status: The status value
         */
        public function set_status($status) {
                $this->status = $status;
        } //End method set_status
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
        public function remove_request($request) {
          $found_request = -1;
          for ($i = 0; $i < sizeof($this->requests); $i++) {
              $thisrequest = $this->requests[$i];
              if ($request->get_request_id() == $thisrequest->get_request_id) {
                  $found_request = $i;
              }
          }
          if ($found_request != -1) {
              unset($this->requests[$i]);
              $this->requests = array_values($this->requests);
              return true;
          }
          else {
              return false;
          }
        }
        
        /**
        * Get the list of audit_trails
        * @return Array of audit_trails
        */
        public function get_audit_trails() {
                return $this->audit_trails;
        } //end method get_audit_trails
    
        /**
        * Add to the list of audit_trails
        * @param $audit_trail: audit_trail to add
        */
        public function add_audit_trail($audit_trail) {
          array_push($this->audit_trails, $audit_trail);
        } //end method add_audit_trail
        
        /**
        * Remove from the list of audit_trails if exists
        * @param $audit_trail: audit_trail to remove
        * @return Whether the audit_trail was removed
        */
        public function remove_audit_trail($audit_trail) {
          $found_audit_trail = -1;
          for ($i = 0; $i < sizeof($this->audit_trails); $i++) {
              $thisaudit_trail = $this->audit_trails[$i];
              if ($audit_trail->get_audit_trail_id() == $thisaudit_trail->get_audit_trail_id) {
                  $found_audit_trail = $i;
              }
          }
          if ($found_audit_trail != -1) {
              unset($this->audit_trails[$i]);
              $audit_trails = array_values($this->audit_trails);
              return true;
          }
          else {
              return false;
          }
        }
        
        
} //end class Shift
?>