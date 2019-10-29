<?php
//Services
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/audit_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/authentication_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/organization_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/session_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/sql_connection_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/request_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/shift_service.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/services/user_service.php";

//Models
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/audit_trail.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/organization.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/request.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/shift.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";

//Helpful View functions
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/navbar.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/form_helpers.php';

//Local configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/config/localconfig.php';
$localconfig = null;
//UNCOMMENT THE FOLLOWING LINE AND MODIFY config/localconfig.php TO TEST ON A LOCAL MySQL SERVER
//$localconfig = LocalConfiguration::get_configuration();


$sql_service = SQLConnectionService::get_instance($localconfig);
$session_service = SessionService::get_service();
$user_service = new UserService($sql_service);
$auth_service = new AuthenticationService($session_service, $sql_service);
$organization_service = new OrganizationService($sql_service);
$shift_service = new ShiftService($sql_service, $user_service);
$request_service = new RequestService($sql_service, $shift_service, $user_service);
$audit_service = new AuditService($sql_service);

//Bookkeeping
date_default_timezone_set("America/New_York");
