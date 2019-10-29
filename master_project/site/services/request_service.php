<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/shift.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/shift_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";
interface IRequestService {
    /**
     * Get all requests in an organization, optionally with a request type and employee id.
     * @param $organization_id: The organization to retrieve requests for
     * @param $request_type: Optionally, the type of status of request to retrieve, such as Request::STATUS_PENDING
     * @param $employee_id: Optionally, the employee to retrieve requests for
     * @return An array of Request objects
     */
    public function get_requests($organization_id, $request_type = null, $employee_id = null);
    /**
     * Create a new Request of a given type
     * @param $request_type: The type of the Request
     * @param $shift_id: The ID of the Shift
     */
    public function submit_request($request_type, $user_id, $shift_id);
    /**
     * Approve a Request of a given ID, denying all other Requests of that shift.
     * @param $request_id: The Request to approve
     */
    public function approve_request($request_id);
    /**
     * Deny a Request of a given ID
     * @param $request_id: The Request to approve
     */
    public function deny_request($request_id);
} //end interface IRequestService

class RequestService implements IRequestService {

    private $conn;
    private $shift_service;
    private $user_service;
    public function __construct($conn, $shift_service, $user_service) {
        if ($conn == null) {
            $conn = SQLConnectionService::get_instance();
        }
        if ($user_service == null) {
            $user_service = new UserService($conn);
        }
        if ($shift_service == null) {
            $shift_service = new ShiftService($conn);
        }
        $this->conn = $conn;
        $this->user_service = $user_service;
        $this->shift_service = $shift_service;
    }
    /**
     * Get all requests in an organization, optionally with a request type and employee id.
     * @param $organization_id: The organization to retrieve requests for
     * @param $request_type: Optionally, the type of status of request to retrieve, such as Request::STATUS_PENDING
     * @param $employee_id: Optionally, the employee to retrieve requests for
     * @return An array of Request objects
     */
    public function get_requests($organization_id, $request_type = null, $employee_id = null) {
        $sql_requests = array(0,array());
        if ($request_type === null && $employee_id === null) {
            $sql_requests = $this->conn->execute_query(
                "SELECT Requests.RequestID AS RequestID, 
                        Requests.ShiftID AS ShiftID,
                        Requests.UserID AS UserID,
                        Requests.Status AS RequestStatus,
                        Requests.Type AS Type,
                        Shifts.Status AS Status,
                        Shifts.OrganizationID,
                        Shifts.ZipCode,
                        Shifts.RequiredPosition,
                        Shifts.StartDate,
                        Shifts.StartTime,
                        Shifts.EndDate,
                        Shifts.EndTime,
                        Shifts.SpecialRequirements,
                        Shifts.PayDifferential,
                        Users.FirstName,
                        Users.LastName,
                        Users.Email,
                        Users.UserType
                FROM Requests 
                INNER JOIN Users ON Requests.UserID = Users.UserID
                INNER JOIN Shifts ON Requests.ShiftID = Shifts.ShiftID
                WHERE Users.OrganizationID=?","d",$organization_id);
        } else if ($employee_id === null) {
            $sql_requests = $this->conn->execute_query(
                "SELECT Requests.RequestID AS RequestID, 
                        Requests.ShiftID AS ShiftID,
                        Requests.UserID AS UserID,
                        Requests.Status AS RequestStatus,
                        Requests.Type AS Type,
                        Shifts.Status AS Status,
                        Shifts.OrganizationID,
                        Shifts.ZipCode,
                        Shifts.RequiredPosition,
                        Shifts.StartDate,
                        Shifts.StartTime,
                        Shifts.EndDate,
                        Shifts.EndTime,
                        Shifts.PayDifferential,
                        Shifts.SpecialRequirements,
                        Users.FirstName,
                        Users.LastName,
                        Users.Email,
                        Users.UserType
                FROM Requests 
                LEFT OUTER JOIN Users ON Requests.UserID = Users.UserID
                INNER JOIN Shifts ON Requests.ShiftID = Shifts.ShiftID
                WHERE Users.OrganizationID=? AND Requests.Status=?","dd",$organization_id,$request_type);
        } else if ($request_type === null) {
            $sql_requests = $this->conn->execute_query(
                "SELECT Requests.RequestID AS RequestID, 
                        Requests.ShiftID AS ShiftID,
                        Requests.UserID AS UserID,
                        Requests.Status AS RequestStatus,
                        Requests.Type AS Type,
                        Shifts.Status AS Status,
                        Shifts.OrganizationID,
                        Shifts.ZipCode,
                        Shifts.RequiredPosition,
                        Shifts.StartDate,
                        Shifts.StartTime,
                        Shifts.EndDate,
                        Shifts.PayDifferential,
                        Shifts.EndTime,
                        Shifts.SpecialRequirements,
                        Users.FirstName,
                        Users.LastName,
                        Users.Email,
                        Users.UserType
                FROM Requests 
                INNER JOIN Users ON Requests.UserID = Users.UserID
                INNER JOIN Shifts ON Requests.ShiftID = Shifts.ShiftID
                WHERE Users.OrganizationID=? AND Requests.UserID=?","dd",$organization_id,$employee_id);
        } else {
            $sql_requests = $this->conn->execute_query(
                "SELECT Requests.RequestID AS RequestID, 
                        Requests.ShiftID AS ShiftID,
                        Requests.UserID AS UserID,
                        Requests.Status AS RequestStatus,
                        Requests.Type AS Type,
                        Shifts.Status AS Status,
                        Shifts.OrganizationID,
                        Shifts.ZipCode,
                        Shifts.RequiredPosition,
                        Shifts.StartDate,
                        Shifts.StartTime,
                        Shifts.PayDifferential,
                        Shifts.EndDate,
                        Shifts.EndTime,
                        Shifts.SpecialRequirements,
                        Users.FirstName,
                        Users.LastName
                FROM Requests 
                LEFT OUTER JOIN Users ON Requests.UserID = Users.UserID
                INNER JOIN Shifts ON Requests.ShiftID = Shifts.ShiftID
                WHERE Users.OrganizationID=? AND Requests.UserID=? AND Requests.Status=?;","ddd",$organization_id,$employee_id,$request_type);
        }
        $requests = array();
        foreach ($sql_requests[1] as $sql_request) {
            array_push($requests, $this->request_from_row($sql_request));
        }
        return $requests;
        
    }
    /**
     * Create a new Request of a given type
     * @param $request_type: The type of the Request
     * @param $shift_id: The ID of the Shift
     */
    public function submit_request($request_type, $user_id, $shift_id) {
        $this->conn->execute_query(
            "INSERT INTO Requests (ShiftID, UserID, Status, Type)
                VALUES (?,?,?,?);",
                "dddd",
                $shift_id,
                $user_id,
                Request::STATUS_PENDING,
                $request_type
        );
        return;
    }
    /**
     * Approve a Request of a given ID, denying all other Requests of that shift.
     * @param $request_id: The Request to approve
     */
    public function approve_request($request_id) {
        $requests = $this->conn->execute_query("SELECT RequestID, ShiftID, UserID, Status as RequestStatus, Type FROM Requests WHERE RequestID=?","d",$request_id)[1];
        if (sizeof($requests) != 1) {
            return;
        }
        $request = $this->request_from_row($requests[0], false);
        $user_to_assign = $request->get_type() == Request::REQUEST_ASSIGNMENT ? $request->get_user_id() : null; //Back to null for cancellation requests
        $status_to_asign = $request->get_type() == Request::REQUEST_ASSIGNMENT ? Shift::STATUS_ASSIGNED : STATUS_UNASSIGNED;
        $this->conn->execute_query("UPDATE Requests SET Status=? WHERE RequestID=?","dd",Request::STATUS_APPROVED,$request_id);
        if ($request->get_type() == Request::REQUEST_ASSIGNMENT) {
            $this->conn->execute_query("UPDATE Requests SET Status=? WHERE ShiftID=? AND Status=?","ddd",Request::STATUS_DENIED,$request->get_shift_id(),Request::STATUS_PENDING);
        }
        $this->conn->execute_query("UPDATE Shifts SET UserID=?, Status=? WHERE ShiftID=?","ddd",$user_to_assign, $status_to_asign, $request->get_shift_id());
    }
    /**
     * Deny a Request of a given ID
     * @param $request_id: The Request to approve
     */
    public function deny_request($request_id) {
        $this->conn->execute_query("UPDATE Requests SET Status=? WHERE RequestID=?","dd",Request::STATUS_DENIED,$request_id);
        return;
    }

    private function request_from_row($row, $include_related = true) {
        $request = new Request($row["RequestID"],
                           $row["UserID"],
                           $row["ShiftID"],
                           $row["Type"],
                           $row["RequestStatus"]);
        if ($include_related) {
            $row["UserId"] = $row["UserID"];
            $request->set_user(UserService::user_from_row($row));
            $request->set_shift(ShiftService::shift_from_row($row));
        }
        return $request;
    }

    
} //end class RequestService
