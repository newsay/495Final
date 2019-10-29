<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/services/sql_connection_service.php';
/**
 * @author Andrew Ritchie
 */
interface IUserService {
    /**
     * Retrieve all users from the database.
     * @param organization_id: The organization of the users. If null, all organizations are returned.
     * @param user_type: The type of user. If null, all user types are returned.
     * @return An array of User objects.
     */
    public function get_users($organization_id, $user_type);
    /**
     * Get a user by its database ID
     * @param $user_id: Integer, the database ID of the user.
     * @return A User object, or null if not found.
     */
    public function get_user_by_id($user_id, $organization_id);
    /**
     * Get a user by its user name
     * @param $email_address: String, the email address of the user.
     * @return A User object, or null if not found.
     */
    public function get_user_by_email_address($email_address, $organization_id);
    /**
     * Create User in database given a  User object
     * @param $user: A User object representing the user to create. Ignores ID attribute.
     * @param $password: The password to use
     * @param $security_question1: The ID of the first security question
     * @param $security_answer1: The answer to the first security question
     * @param $security_question2: The ID of the second security question
     * @param $security_answer2: The answer to the second security question
     * @param $security_question3: The ID of the third security question
     * @param $security_answer3: The answer to the third security question
     * @return An error message, or null if successful
     */
    public function create_user($user, $password, $security_question1, $security_answer1, $security_question2, $security_answer2, $security_question3, $security_answer3);
    /**
     * Update a user's password 
     * @param $id: The ID of the user
     * $param $password: The new Password
     */
    public function update_password($id, $password);
    /**
     * Update a user's password and security questions
     * @param $password: The password to use
     * @param $security_question1: The ID of the first security question
     * @param $security_answer1: The answer to the first security question
     * @param $security_question2: The ID of the second security question
     * @param $security_answer2: The answer to the second security question
     * @param $security_question3: The ID of the third security question
     * @param $security_answer3: The answer to the third security question
     */
    public function update_password_and_security_questions($id, $password, $security_question1, $security_answer1, $security_question2, $security_answer2, $security_question3, $security_answer3);
    /**
     * Update a user's properties
     * $user: The User object to update
     */
    public function modify_user($user);
    /**
     * Delete a user from the system.
     * $user: The User object to delete
     */
    public function delete_user($user);
} //end interface IUserService

/**
 * Class to create, read, update, and delete ("CRUD") Users in the database.
 */
class UserService implements IUserService {
    private $conn;
    /**
     * Constructor: Given a SQLConnectionService object, create a new UserService
     * @param $connection: SQLConnectionService object, connection to use for retrieving Users.
     */
    public function __construct($connection = null) {
        if ($connection == null) {
            $connection = SQLConnectionService::get_instance();
        }
        $this->conn = $connection;
    } //end function __construct
    
    /**
     * Retrieve all users from the database.
     * @param organization_id: The organization of the users. If null, all organizations are returned.
     * @param user_type: The type of user. If null, all user types are returned.
     * @return An array of User objects.
     */
    public function get_users($organization_id = null, $user_type = null) {
        $this->conn->connect();
        $query = "SELECT UserId, Email, UserType, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, OrganizationId FROM Users";
        if ($organization_id == null && $user_type === null) {
            $result = $this->conn->execute_query($query);
        } else if ($organization_id != null && $user_type === null) {
            $result = $this->conn->execute_query($query . " WHERE OrganizationId = ?",'d',$organization_id);
        } else if ($organization_id == null && $user_type !== null) {
            $result = $this->conn->execute_query($query . " WHERE UserType = ?",'d',$user_type);
        } else if ($organization_id != null && $user_type !== null) {
            $result = $this->conn->execute_query($query . " WHERE OrganizationId = ? AND UserType = ?",'dd',$organization_id, $user_type);
        }
        $retval = array();
        foreach($result[1] as $row) {
            array_push($retval, UserService::user_from_row($row));
        }
        return $retval;
    } //end function get_users
    
    /**
     * Get a user by its database ID
     * @param $user_id: Integer, the database ID of the user.
     * @return A User object, or null if not found.
     */
    public function get_user_by_id($user_id, $organization_id) {
        $this->conn->connect();
        $result = $this->conn->execute_query(
        "SELECT Users.UserId as UserId, 
                Users.Email as Email, 
                Users.UserType as UserType, 
                Users.FirstName as FirstName, 
                Users.LastName as LastName, 
                Users.HomePhone as HomePhone, 
                Users.MobilePhone as MobilePhone, 
                Users.Address1 as Address1, 
                Users.Address2 as Address2, 
                Users.City as City, 
                Users.State as State, 
                Users.ZipCode as ZipCode, 
                Users.OrganizationId as OrganizationId,
                Shifts.ShiftId as ShiftID,
                Shifts.Status as Status,
                Shifts.OrganizationID as OrganizationID,
                Shifts.RequiredPosition as RequiredPosition,
                Shifts.PayDifferential as PayDifferential,
                Shifts.StartDate as StartDate,
                Shifts.StartTime as StartTime,
                Shifts.EndDate as EndDate,
                Shifts.EndTime as EndTime,
                Shifts.SpecialRequirements as SpecialRequirements
        FROM Users 
        LEFT JOIN Shifts ON Shifts.UserID = Users.UserId
        WHERE Users.UserId=?  AND Users.OrganizationId=?
        ORDER BY Shifts.StartDate,Shifts.StartTime
        ","dd",$user_id,$organization_id);
        if (sizeof($result[1]) > 0) {
            $user = UserService::user_from_row($result[1][0]);
            foreach ($result[1] as $row) {
                $user->add_shift(ShiftService::shift_from_row($row));
            }
            return $user;
        }
        else {
            return null;
        }
    } //end function get_user_by_id
    
    /**
     * Get a user by its user name
     * @param $email_address: String, the email address of the user.
     * @return A User object, or null if not found.
     */
    public function get_user_by_email_address($email_address, $organization_id=null) {
        $this->conn->connect();
        if ($organization_id == null) {
            $result = $this->conn->execute_query("SELECT UserId, Email, UserType, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, OrganizationId FROM Users WHERE Email=?","s",$email_address);
        }
        else {
            $result = $this->conn->execute_query("SELECT UserId, Email, UserType, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, OrganizationId FROM Users WHERE Email=? AND OrganizationId=?","sd",$email_address,$organization_id);
        }
        
        if (sizeof($result[1]) > 0) {
            return UserService::user_from_row($result[1][0]);
        }
        else {
            return null;
        }
    } //end function get_user_by_name
    
    /**
     * Create User in database given a  User object
     * @param $user: A User object representing the user to create. Ignores ID attribute.
     * @param $password: The password to use
     * @return An error message, or null if successful
     */
    public function create_user($user, $password, 
                                $security_question1, $security_answer1,
                                $security_question2, $security_answer2,
                                $security_question3, $security_answer3) {
        $existing_user = $this->get_user_by_email_address($user->get_email_address());
        if ($existing_user) {
            return "The email address is already in use.";
        }
        $this->conn->connect();
        $result = $this->conn->execute_query("INSERT INTO Users (Email, Password, UserType, OrganizationId, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, SecurityQuestion1, SecurityQuestion1Answer, SecurityQuestion2, SecurityQuestion2Answer, SecurityQuestion3, SecurityQuestion3Answer)
                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);",
                                                "ssddsssssssssdsdsds",
                                                $user->get_email_address(),
                                                password_hash($password, PASSWORD_DEFAULT),
                                                $user->get_user_type(),
                                                $user->get_organization_id(),
                                                $user->get_first_name(),
                                                $user->get_last_name(),
                                                $user->get_home_phone(),
                                                $user->get_mobile_phone(),
                                                $user->get_address_1(),
                                                $user->get_address_2(),
                                                $user->get_city(),
                                                $user->get_state(),
                                                $user->get_zip(),
                                                $security_question1,
                                                $security_answer1 ? password_hash($security_answer1, PASSWORD_DEFAULT) : null,
                                                $security_question2,
                                                $security_answer2 ?password_hash($security_answer2, PASSWORD_DEFAULT) : null,
                                                $security_question3,
                                                $security_answer3 ? password_hash($security_answer3, PASSWORD_DEFAULT) : null
                                                );
    }
    
     
    /**
     * Update a user's password 
     * @param $id: The ID of the user
     * $param $password: The new Password
     */
    public function update_password($id, $password) {
        $this->conn->connect();
        //INSERT INTO Users (Email, Password, UserType, OrganizationId, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, SecurityQuestion1, SecurityQuestion1Answer, SecurityQuestion2, SecurityQuestion2Answer, SecurityQuestion3, SecurityQuestion3Answer)                       
        $result = $this->conn->execute_query("UPDATE Users
                                                SET Password = ?
                                                WHERE UserId = ?;"
                                                ,"sd",
                                                password_hash($password, PASSWORD_DEFAULT),
                                                $id
                                                );
    }
    
    /**
     * Update a user's password and security questions
     * 
     */
    public function update_password_and_security_questions($id, $password, 
                                $security_question1, $security_answer1,
                                $security_question2, $security_answer2,
                                $security_question3, $security_answer3) {
        $this->conn->connect();
        //INSERT INTO Users (Email, Password, UserType, OrganizationId, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, SecurityQuestion1, SecurityQuestion1Answer, SecurityQuestion2, SecurityQuestion2Answer, SecurityQuestion3, SecurityQuestion3Answer)                       
        $result = $this->conn->execute_query("UPDATE Users
                                                SET Password = ?,
                                                    SecurityQuestion1 = ?,
                                                    SecurityQuestion1Answer = ?,
                                                    SecurityQuestion2 = ?,
                                                    SecurityQuestion2Answer = ?,
                                                    SecurityQuestion3 = ?,
                                                    SecurityQuestion3Answer = ?
                                                WHERE UserId = ?;"
                                                ,"sdsdsdsd",
                                                password_hash($password, PASSWORD_DEFAULT),
                                                $security_question1,
                                                password_hash($security_answer1, PASSWORD_DEFAULT),
                                                $security_question2,
                                                password_hash($security_answer2, PASSWORD_DEFAULT),
                                                $security_question3,
                                                password_hash($security_answer3, PASSWORD_DEFAULT),
                                                $id
                                                );
    }
    
    /**
     * Helper method to generate a user from a MySQL row
     */
    public static function user_from_row($row) {
        $result = new User(UserService::get_key($row,"UserId"),UserService::get_key($row,"Email"),UserService::get_key($row,"UserType"));
        $result->set_first_name(UserService::get_key($row,"FirstName"));
        $result->set_last_name(UserService::get_key($row,"LastName"));
        $result->set_home_phone(UserService::get_key($row,"HomePhone"));
        $result->set_mobile_phone(UserService::get_key($row,"MobilePhone"));
        $result->set_address_1(UserService::get_key($row,"Address1"));
        $result->set_address_2(UserService::get_key($row,"Address2"));
        $result->set_city(UserService::get_key($row,"City"));
        $result->set_state(UserService::get_key($row,"State"));
        $result->set_zip(UserService::get_key($row,"ZipCode"));
        $result->set_organization_id(UserService::get_key($row,"OrganizationId"));
        return $result;
    } //end function user_from_row
    /**
     * Update a user's properties
     * $user: The User object to update
     */
    public function modify_user($user) {
        $this->conn->connect();
        $this->conn->execute_query(
            "UPDATE Users 
                SET
                UserType=?,
                FirstName=?,
                LastName=?,
                HomePhone=?,
                MobilePhone=?,
                Address1=?, 
                Address2=?,
                City=?,
                State=?,
                ZipCode=?
                WHERE UserId=?","dsssssssssd",
                $user->get_user_type(),
                $user->get_first_name(),
                $user->get_last_name(),
                $user->get_home_phone(),
                $user->get_mobile_phone(),
                $user->get_address_1(),
                $user->get_address_2(),
                $user->get_city(),
                $user->get_state(),
                $user->get_zip(),
                $user->get_user_id()
                );
    }
    /**
     * Delete a user from the system.
     * $user_id: The User ID to delete
     */
    public function delete_user($user_id) {
        $this->conn->connect();
        if ($user_id != null) {
            $res = $this->conn->execute_query("
            UPDATE Shifts
                SET UserID=NULL
                WHERE UserID=?;","d",$user_id);
            $res = $this->conn->execute_query("
            DELETE FROM Requests
                WHERE UserID=?;","d",$user_id);
            $res = $this->conn->execute_query("
            DELETE FROM Users
                WHERE UserId=?;","d",$user_id);
        }
    }

    private static function get_key($row, $key) {
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
        else {
            return null;
        }
    }
} //end class UserService
