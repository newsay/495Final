<?php
date_default_timezone_set("America/New_York");
include_once $_SERVER['DOCUMENT_ROOT'] . '/services/sql_connection_service.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/models/shift.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/models/request.php';
/**
 * @author Andrew Ritchie
 */
interface IShiftService {
    /**
     * Retrieve a list of shifts in an organization
     * @param $organization_id: The ID of the organization
     * @return An array of Shifts
     */
    public function get_shifts($organization_id);
    /**
     * Retrieve a list of shifts in an organization that start between two dates
     * @param $organization_id: The id of the organization
     * @param $shift_status: Nullable boolean value. If true, retrieves only filled shifts. If false, retrieves only unfilled shifts.
     * @param $start_date: Date object for the start date.
     * @param $end_date: Date object for the end date.
     * @return An array of Shifts
     */
    public function get_shifts_between($organization_id, $shift_status, $start_date, $end_date);
    /**
     * Get a specific shift by ID
     * @param $shift_id: The ID of the shift
     * @return A Shift object
     */
    public function get_shift($id);
    /**
     * Delete user based on employee id
     * @param $user_id: The ID of the user
     */    
    public function delete_shift($id);
    /**
     * Add a new shift
     * @param $shift: A Shift object representing the new Shift
     */
    public function add_shift($shift);
    // /**
    //  * Assign an employee to a shift.
    //  * @param $shift: A Shift object representing the shift.
    //  * @param $employee: A User object representing the employee.
    //  */
    // public function assign_employee($shift, $employee);
    // /**
    //  * Remove an employee from a shift.
    //  * @param $shift: A Shift object representing the shift.
    //  */
    // public function remove_employee($shift);
    /**
     * Update a given shift.
     * If assigned_user_id is not null, all Pending to this shift are Denied.
     * @param $shift: The shift to update.
     */
    public function modify_shift($shift);
    } //end interface IShiftInterface

class ShiftService implements IShiftService {
    private $conn;
    private $user_service;
    
    public function __construct($conn = null, $user_service = null) {
        if ($conn == null) {
            $conn = SQLConnectionService::get_instance();
        }
        if ($user_service == null) {
            $user_service = new UserService($conn);
        }
        $this->conn = $conn;
        $this->user_service = $user_service;
    }
    
    /**
     * Retrieve a list of shifts in an organization
     * @param $organization_id: The ID of the organization
     * @return An array of Shifts
     */
    public function get_shifts($organization_id) {
        $shift_sql = $this->conn->execute_query(
            "SELECT Shifts.ShiftID as ShiftID,
                    Shifts.Status as Status,
                    Shifts.UserID as UserID,
                    Shifts.OrganizationID as OrganizationID,
                    Shifts.ZipCode as ZipCode,
                    Shifts.RequiredPosition as RequiredPosition,
                    Shifts.PayDifferential as PayDifferential,
                    Shifts.StartDate as StartDate,
                    Shifts.StartTime as StartTime,
                    Shifts.EndDate as EndDate,
                    Shifts.EndTime as EndTime,
                    Shifts.SpecialRequirements as SpecialRequirements,
                    Users.FirstName as FirstName,
                    Users.LastName as LastName,
                    Users.Email as Email
            FROM Shifts 
            LEFT OUTER JOIN Users ON Shifts.UserID=Users.UserID 
            WHERE Shifts.OrganizationID=?","d",$organization_id)[1];
        $shifts = array();
        foreach ($shift_sql as $shift_sql) {
            array_push($shifts, ShiftService::shift_from_row($shift_sql));
        }
        return $shifts;
    }
    /**
     * Retrieve a list of shifts in an organization that start between two dates
     * @param $organization_id: The id of the organization
     * @param $shift_status: Nullable boolean value. If true, retrieves only filled shifts. If false, retrieves only unfilled shifts.
     * @param $start_date: Date object for the start date.
     * @param $end_date: Date object for the end date.
     * @return An array of Shifts
     */
    public function get_shifts_between($organization_id, $shift_status, $start_date, $end_date) {
        $include_status = $shift_status !== null;
        $paramsstring = "d";
        $query = "SELECT    Shifts.ShiftID as ShiftID,
                            Shifts.Status as Status,
                            Shifts.UserID as UserID,
                            Shifts.OrganizationID as OrganizationID,
                            Shifts.ZipCode as ZipCode,
                            Shifts.RequiredPosition as RequiredPosition,
                            Shifts.PayDifferential as PayDifferential,
                            Shifts.StartDate as StartDate,
                            Shifts.StartTime as StartTime,
                            Shifts.EndDate as EndDate,
                            Shifts.EndTime as EndTime,
                            Shifts.SpecialRequirements as SpecialRequirements, 
                            Users.FirstName as FirstName,
                            Users.LastName as LastName,
                            Users.Email as Email
                FROM Shifts
                LEFT OUTER JOIN Users ON Shifts.UserID=Users.UserID
                WHERE Shifts.OrganizationId=?";

        $params = array($organization_id);
        if ($include_status) {
            $paramsstring .= "d";
            $query .= " AND Status=?";
            array_push($params,$shift_status);
        }
        array_push($params,date("Y-m-d H:i:s",$start_date),date("Y-m-d H:i:s",$end_date));
        $query .= " AND ? < TIMESTAMP(StartDate, StartTime)
                    AND ? > TIMESTAMP(StartDate, StartTime)
                    
                    ORDER BY StartDate,StartTime;";
        $paramsstring .= "ss";
        $shifts_sql = $this->conn->execute_query($query,$paramsstring,...$params)[1];
        $shifts = array();
        foreach ($shifts_sql as $shift_sql) {
            array_push($shifts, ShiftService::shift_from_row($shift_sql));
        }
        return $shifts;
    }
    /**
     * Delete user based on employee id
     * @param $user_id: The ID of the user
     */    
    public function delete_shift($id){
    	$this->conn->connect();
    	
        $shift_sql = $this->conn->execute_query(
    	    "DELETE FROM Requests WHERE ShiftID=?","d",$id
    	);
    	
    	$shift_sql = $this->conn->execute_query(
    	    "DELETE FROM ShiftAuditTrail WHERE ShiftID=?","d",$id
    	);
    	
    	
    	$shift_sql = $this->conn->execute_query(
    		"DELETE FROM Shifts WHERE ShiftID=?","d",$id
    	);
    }
    /**
     * Get a specific shift by ID
     * @param $shift_id: The ID of the shift
     * @return A Shift object
     */
    public function get_shift($id) {
        //TODO: Remove test data
        $this->conn->connect();
        $shift_sql = $this->conn->execute_query(
            "SELECT * FROM Shifts WHERE ShiftId=?","d",$id
        )[1];
        if (sizeof($shift_sql) == 1) {
            return ShiftService::shift_from_row($shift_sql[0]);
        }
        else {
            return null;
        }
        
    }
    /**
     * Add a new shift
     * @param $shift: A Shift object representing the new Shift
     */
    public function add_shift($shift) {
        if ($shift != null) {
            $this->conn->execute_query(
                "INSERT INTO Shifts (Status, UserID, OrganizationID, ZipCode, RequiredPosition, PayDifferential, StartDate, StartTime, EndDate, EndTime, SpecialRequirements)
                VALUES (?,?,?,?,?,?,?,?,?,?,?);
                ",
                "dddssdsssss",
                $shift->get_status(),
                $shift->get_assigned_user_id(),
                $shift->get_organization_id(),
                $shift->get_zip_code(),
                $shift->get_required_position(),
                $shift->get_pay_differential(),
                ShiftService::get_string_date($shift->get_start_datetime()),
                ShiftService::get_string_time($shift->get_start_datetime()),
                ShiftService::get_string_date($shift->get_end_datetime()),
                ShiftService::get_string_time($shift->get_end_datetime()),
                $shift->get_special_requirements()
            );
            
        }
    }
    /**
     * Update a given shift.
     * If assigned_user_id is not null, all Pending Requests to this shift are Denied.
     * @param $shift: The shift to update.
     */
    public function modify_shift($shift) {
        $this->conn->execute_query(
            "UPDATE Shifts 
                SET Status=?,
                    UserID=?,
                    OrganizationId=?,
                    ZipCode=?,
                    RequiredPosition=?,
                    PayDifferential=?,
                    StartDate=?,
                    StartTime=?,
                    EndDate=?,
                    EndTime=?,
                    SpecialRequirements=?
                WHERE ShiftID=?",
                "dddssdsssssd",
                $shift->get_status(),
                $shift->get_assigned_user_id(),
                $shift->get_organization_id(),
                $shift->get_zip_code(),
                $shift->get_required_position(),
                $shift->get_pay_differential(),
                $this->get_string_date($shift->get_start_datetime()),
                $this->get_string_time($shift->get_start_datetime()),
                $this->get_string_date($shift->get_end_datetime()),
                $this->get_string_time($shift->get_end_datetime()),
                $shift->get_special_requirements(),
                $shift->get_shift_id()
        );
        if ($shift->get_assigned_user_id() != null) {
            $this->conn->execute_query("UPDATE Requests SET Status=? WHERE ShiftID=? AND Status=? AND Type=?",
                                                "dddd",
                                               Request::STATUS_DENIED,$shift->get_shift_id(),Request::STATUS_PENDING,Request::REQUEST_ASSIGNMENT);
        }
    }
    
    // /**
    //  * Assign an employee to a shift.
    //  * @param $shift: A Shift object representing the shift.
    //  * @param $employee: A User object representing the employee.
    //  */
    // public function assign_employee($shift, $employee) {
    //     //TODO: Implement method
    //     return;
    // }
    // /**
    //  * Remove an employee from a shift.
    //  * @param $shift: A Shift object representing the shift.
    //  */
    // public function remove_employee($shift) {
    //     //TODO: Implement method
    //     return;
    // }
    
    public static function shift_from_row($row) {
        $shift = new Shift();
        $shift->set_shift_id(ShiftService::get_key($row,"ShiftID"));
        $shift->set_status(ShiftService::get_key($row,"Status"));
        $shift->set_assigned_user_id(ShiftService::get_key($row,"UserID"));
        $shift->set_organization_id(ShiftService::get_key($row,"OrganizationID"));
        $shift->set_zip_code(ShiftService::get_key($row,"ZipCode"));
        $shift->set_required_position(ShiftService::get_key($row,"RequiredPosition"));
        $shift->set_pay_differential(ShiftService::get_key($row,"PayDifferential"));
        $shift->set_start_datetime(
            ShiftService::parse_date_time(
                ShiftService::get_key($row,"StartDate"),
                ShiftService::get_key($row,"StartTime")
            ));
        $shift->set_end_datetime(
            ShiftService::parse_date_time(
                ShiftService::get_key($row,"EndDate"),
                ShiftService::get_key($row,"EndTime")
            ));
        $shift->set_special_requirements(ShiftService::get_key($row,"SpecialRequirements"));    
        if ($shift->get_assigned_user_id() !== null) {
            $shift->set_assigned_user(UserService::user_from_row($row));
        }
        return $shift;
    }

    /**
     * Given a date and time, return a PHP time object.
     * @param $date SQL date object
     * @param $time SQL time object
     * @return PHP time object
     */
    private static function parse_date_time($date, $time) {
        if ($date == null && $time == null) {
            return null;
        }
        else {
            $str = '';
            if ($date == null) {
                $str = $time;
            }
            else if ($time == null) {
                $str = $date;
            }
            else {
                $str = $date . ' ' . $time;
            }
            return strtotime($str);
        }
    }
    /**
     * Given a PHP time object, get the SQL String that represents date.
     * @param $date: The PHP time object
     * @return The MySQL Date
     */
    public static function get_string_date($date) {
        if ($date == null) {
            return null;
        }
        return date('Y-m-d',$date);
    }
    /**
     * Given a PHP time object, get the SQL String that represents the time.
     * @param $date: The PHP time object
     * @return The MySQL Time
     */
    public static function get_string_time($date) {
        if ($date == null) {
            return null;
        }
        return date('H:i:s',$date);
    }

    private static function get_key($row, $key) {
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
        else {
            return null;
        }
    }
} //end class ShiftService
