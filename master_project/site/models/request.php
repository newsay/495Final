<?php
/**
 * Class to represent a Request in the database. A Request represents when
 * an Employee would like to either cancel a Shift or assign one to themselves.
 * 
 * @author Andrew Ritchie
 */
class Request {
        private $request_id;
        private $user_id;
        private $shift_id;
        private $type;
        private $status;
        private $shift;
        private $user;
        const REQUEST_ASSIGNMENT = 0; 
        const REQUEST_CANCELLATION = 1;
        const STATUS_PENDING = 0;
        const STATUS_APPROVED = 1;
        const STATUS_DENIED = 2;
        
        public function __construct($request_id, $user_id, $shift_id, $type, $status) {
            $this->request_id = $request_id;
            $this->user_id = $user_id;
            $this->shift_id = $shift_id;
            $this->type = $type;
            $this->status = $status;
        }
        
        /**
         * Get the request id property value
         * @return The request id value
         */
        public function get_request_id() {
                return $this->request_id;
        }//end method get_request_id

        /**
         * Set the request id property value
         * @param $request_id: The request id value
         */
        public function set_request_id($request_id) {
                $this->request_id = $request_id;
        }//end method set_request_id

        /**
         * Get the user id property value
         * @return The user id value
         */
        public function get_user_id() {
                return $this->user_id;
        }//end method get_user_id

        /**
         * Set the user id property value
         * @param $user_id: The user id value
         */
        public function set_user_id($user_id) {
                $this->user_id = $user_id;
        }//end method set_user_id

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
         * Get the type property value
         * @return The type value
         */
        public function get_type() {
                return $this->type;
        }//end method get_type

        /**
         * Set the type property value
         * @param $type: The type value
         */
        public function set_type($type) {
                $this->type = $type;
        }//end method set_type

        /**
         * Get the status property value
         * @return The status value
         */
        public function get_status() {
                return $this->status;
        }//end method get_status

        /**
         * Set the status property value
         * @param $status: The status value
         */
        public function set_status($status) {
                $this->status = $status;
        }//end method set_status
        /**
         * Get the shift property value
         * @return The shift value
         */
        public function get_shift() {
                return $this->shift;
        }//end method get_shift

        /**
         * Set the shift property value
         * @param $shift: The shift value
         */
        public function set_shift($shift) {
                $this->shift = $shift;
                if ($shift != null) {
                        $this->shift_id = $shift->get_shift_id();
                }
        }//end method set_shift
                /**
         * Get the user property value
         * @return The user value
         */
        public function get_user() {
                return $this->user;
        }//end method get_user

        /**
         * Set the user property value
         * @param $user: The user value
         */
        public function set_user($user) {
                $this->user = $user;
                if ($user != null) {
                        $this->user_id = $user->get_user_id();
                }
        }//end method set_user

} //end class Request
