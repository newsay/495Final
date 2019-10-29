<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/audit_trail.php";
interface IAuditService {
    /**
     * Get a list of Audit Trail entries for a given shift.
     * @param $shift_id: The ID of the shift to retrieve Audit History entries for
     * @return A list of audit values
     */
    public function get_audit_history($shift_id);
    /**
     * Add a new log to the audit history
     * @param $shift_id: The ID of the Shift
     * @param $entry: The AuditTrail entry to add
     */
     public function add_audit_log($shift_id, $entry);
} //end interface IAuditService

class AuditService implements IAuditService {

    private $conn;
    public function __construct($conn = null) {
        if ($conn == null) {
            $conn = SQLConnectionService::get_instance();
        }
        $this->conn = $conn;
    }
    /**
     * Get a list of Audit Trail entries for a given shift.
     * @param $shift_id: The ID of the shift to retrieve Audit History entries for
     * @return A list of audit values
     */
    public function get_audit_history($shift_id) {
        $sql_audits = $this->conn->execute_query("SELECT * FROM ShiftAuditTrail WHERE ShiftID=?","d",$shift_id);
        $audits = array();
        foreach ($sql_audits[1] as $sql_audit) {
            array_push($audits, new AuditTrail($sql_audit["ShiftID"],
                                               strtotime($sql_audit["ModificationDate"]),
                                               $sql_audit["Details"]));
        }
        return $audits;
    }
    /**
     * Add a new log to the audit history
     * @param $shift_id: The ID of the Shift
     * @param $entry: The AuditTrail entry to add
     */
     public function add_audit_log($shift_id, $entry) {
         $this->conn->execute_query(
             "INSERT INTO ShiftAuditTrail (ModificationDate, ShiftID, Details)
                     VALUES (?,?,?)"
                     ,"sds",date("Y-m-d h:i:s",$entry->get_modification_date()), $shift_id, $entry->get_details()
         );
     }
} //end interface IAuditService
