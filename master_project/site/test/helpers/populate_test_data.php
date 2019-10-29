<?php
/**
 * Provide a means to populate some quick test data for test cases. Wipes all data from existing tables in the provided SQL connection
 * and creates new data.
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/services/sql_connection_service.php';
/**
 * Given a SQL connection, populate test data in a SQL server by deleting all current values and adding test values.
 * @param $conn: A SQLConnectionService object.
 */
function test_data_setup($conn) {
    
    $conn->connect();
    $conn->execute_query("DELETE FROM ShiftAuditTrail");
    $conn->execute_query("DELETE FROM Requests");
    $conn->execute_query("DELETE FROM Shifts");
    $conn->execute_query("DELETE FROM Users");
    $conn->execute_query("DELETE FROM Organizations");
    $conn->execute_query("INSERT INTO Organizations (OrganizationID, OrganizationName, IsEnabled) VALUES (1, 'Test Organization', 1)");
    $conn->execute_query("INSERT INTO Users (UserId, Email, Password, UserType, OrganizationId, FirstName, LastName, HomePhone, MobilePhone, Address1, Address2, City, State, ZipCode, SecurityQuestion1, SecurityQuestion1Answer, SecurityQuestion2, SecurityQuestion2Answer, SecurityQuestion3, SecurityQuestion3Answer)
        VALUES (1,'user1@gmail.com', ?, 0, 1, 'Employee', 'One', '123-456-7890', '098-765-4321', '123 Main Street', 'Apt 1', 'Washington', 'District of Columbia', '00000-0000', 1, ?, 2, ?, 3, ?),
               (2,'user2@gmail.com', ?, 0, 1, 'Employee', 'Two', '234-567-8901', '987-654-3210', '345 1st Street', 'Unit 300', 'Columbia', 'Maryland', '12345-6789', 1, ?, 2, ?, 3, ?),
               (3,'manager1@gmail.com', ?, 1, 1, 'Manager', 'One', '555-555-5555', '333-333-3333', '800 Freedom Blvd', 'Apt A', 'Columbia', 'Maryland', '88888', 1, ?, 2, ?, 3, ?)
               ;"
               ,"ssssssssssss",password_hash('password1', PASSWORD_DEFAULT),password_hash('test1', PASSWORD_DEFAULT),password_hash('test2', PASSWORD_DEFAULT),password_hash('test3', PASSWORD_DEFAULT),
                                       password_hash('password2', PASSWORD_DEFAULT),password_hash('test1', PASSWORD_DEFAULT),password_hash('test2', PASSWORD_DEFAULT),password_hash('test3', PASSWORD_DEFAULT),
                                       password_hash('password3', PASSWORD_DEFAULT),password_hash('test1', PASSWORD_DEFAULT),password_hash('test2', PASSWORD_DEFAULT),password_hash('test3', PASSWORD_DEFAULT));

    $conn->execute_query(
        "INSERT INTO Shifts (ShiftId, OrganizationID, StartDate, StartTime, EndDate, EndTime, UserID, ZipCode, PayDifferential, RequiredPosition, SpecialRequirements, Status)
        VALUES  (1, 1, '2019-05-26', ' 06:00:00', '2019-05-26', ' 02:00:00', 2, '12345-6789', 12.2, 'A', 'spec reqs',1),
                (2, 1, '2019-05-26', ' 02:00:00', '2019-05-26', ' 10:00:00', 3, '12345-6789', 12.3, 'B','spec reqs',1),
                (3, 1, '2019-05-27', ' 06:00:00', '2019-05-27', ' 02:00:00', 2, '12345-6789', 12.4, 'C','spec reqs',1),
                (4, 1, '2019-05-27', ' 02:00:00', '2019-05-27', ' 10:00:00', 3, '12345-6789', 12.5, 'D','spec reqs',1),
                (5, 1, '2019-05-28', ' 06:00:00', '2019-05-28', ' 02:00:00', 2, '12345-6789', 12.6, 'E','spec reqs',1),
                (6, 1, '2019-05-28', ' 02:00:00', '2019-05-28', ' 10:00:00', 3, '12345-6789', 12.7, 'F','spec reqs',1),
                (7, 1, '2019-05-29', ' 06:00:00', '2019-05-29', ' 02:00:00', 2, '12345-6789', 12.8, 'G','spec reqs',1),
                (8, 1, '2019-05-29', ' 02:00:00', '2019-05-29', ' 10:00:00', 3, '12345-6789', 12.9, 'H','spec reqs',1),
                (9, 1, '2019-05-30', ' 06:00:00', '2019-05-30', ' 02:00:00', 2, '12345-6789', 12.0, 'I','spec reqs',1),
                (10, 1, '2019-05-30', ' 02:00:00', '2019-05-30', ' 10:00:00', 3, '12345-6789', 12.1, 'J','spec reqs',1),
                (11, 1, '2019-05-31', ' 06:00:00', '2019-05-31', ' 02:00:00', 2, '12345-6789', 12.2, 'K','spec reqs',1),
                (12, 1, '2019-05-31', ' 02:00:00', '2019-05-31', ' 10:00:00', null, '12345-6789', 12.3, 'L','spec reqs',0),
                (13, 1, '2019-06-01', ' 06:00:00', '2019-06-01', ' 02:00:00', null, '12345-6789', 12.4, 'M','spec reqs',0),
                (14, 1, '2019-06-01', ' 02:00:00', '2019-06-01', ' 10:00:00', null, '12345-6789', 12.5, 'N','spec reqs',0);");

    $conn->execute_query("INSERT INTO Requests (RequestID, ShiftID, UserID, Status, Type)
        VALUES  (1, 12, 2, 0, 0), -- User 2 Requesting assignment into 12
                (2, 13, 2, 0, 1),  -- User 2 Requesting cancellation from 1
                (3, 12, 3, 0, 0) -- User 3 Requesting assignment into 12
    ;");

    $conn->execute_query("INSERT INTO ShiftAuditTrail (ModificationDate, ShiftID, Details)
                          VALUES ('2019-06-10 01:23:45',2,'Test Details'),
                                 ('2019-06-10 01:23:45',2,'Test Details 2'),
                                 ('2019-06-10 02:34:56',2,'Test Details 3'),
                                 ('2019-06-10 03:45:01',3,'Test Details 4')");
} //end function test_data_setup
