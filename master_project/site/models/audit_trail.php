<?php
/**
 * Model to represent an Audit Trail object in the database.
 * An Audit Trail is a record of changes between two updates of a Shift.
 * @author Andrew Ritchie
 */
class AuditTrail {
        private $shift_id;
        private $modification_date;
        private $details;
        
        public function __construct($shift_id, $modification_date, $details) {
            $this->shift_id = $shift_id;
            $this->modification_date = $modification_date;
            $this->details = $details;
        }
        /**
         * Get the shift id property value
         * @return The shift id value
         */
        public function get_shift_id() {
                return $this->shift_id;
        }//end method get_shift_id

        /**
         * Get the modification date property value
         * @return The modification date value
         */
        public function get_modification_date() {
                return $this->modification_date;
        }//end method get_modification_date

        /**
         * Get the details property value
         * @return The details value
         */
        public function get_details() {
                return $this->details;
        }//end method get_details

} //end class AuditTrail
