<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';
/**
 * @author Andrew Ritchie
 */
if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
    $requests = $request_service->get_requests($auth_service->get_user_organization(), Request::STATUS_PENDING);
    page_start($auth_service, "Scheduling OnDemand - Manage Requests","Manage Requests");
    ?>
    <div class="container">
        <div class="row">
            <?php
            if ($_POST) {
                if ($_POST["type"] == "approve") {
                    $requests_to_approve_assignment = array();
                    $requests_to_approve_cancellation = array();
                    $error = false;
                    foreach ($requests as $request) {
                        if (array_key_exists("request_" . $request->get_request_id(), $_POST)) {
                            if ($request->get_type() == Request::REQUEST_ASSIGNMENT) {
                                //Check to make sure two "Assignment" requests don't affect the same Shift.
                                if (array_key_exists($request->get_shift_id(), $requests_to_approve_assignment)) {
                                    $error = true;
                                    ?>
                                    <div class="col-md-8 center-block" style="float:none">
                                        <h4 class="text-center login-header">Cannot approve two Assignment requests to the same Shift. <a href='managerequests.php'>Go back</a></h4>

                                    </div>
                                <?php
                                } else {
                                    $requests_to_approve_assignment[$request->get_shift_id()] = $request;
                                }
                            } else {
                                $requests_to_approve_cancellation[$request->get_shift_id()] = $request;
                            }
                        }
                    }
                    if (!$error) {
                        foreach ($requests_to_approve_cancellation as $request) {
                            $request_service->approve_request($request->get_request_id());
                            $shift = $shift_service->get_shift($request->get_shift_id());
                            $user = $user_service->get_user_by_id($request->get_user_id(), $auth_service->get_user_organization());
                            $audit_service->add_audit_log($request->get_shift_id(), new AuditTrail($request->get_shift_id(), time(), "Assigned User changed from " . $user->get_first_name() . " " . $user->get_last_name() . " to {none}"));
                        }
                        foreach ($requests_to_approve_assignment as $request) {
                            $request_service->approve_request($request->get_request_id());
                            $shift = $shift_service->get_shift($request->get_shift_id());
                            $user = $user_service->get_user_by_id($request->get_user_id(), $auth_service->get_user_organization());
                            $audit_service->add_audit_log($request->get_shift_id(), new AuditTrail($request->get_shift_id(), time(), "Assigned User changed from {none} to " . $user->get_first_name() . " " . $user->get_last_name()));
                        }
                        ?>
                        <div class="col-md-8 center-block" style="float:none">
                            <h4 class="text-center login-header">Requests have been approved. <a href='index.php'>Return to Dashboard</a></h4>
                        </div>
                    <?php
                    }
                } else if ($_POST["type"] == "deny") {
                    $requests_to_deny = array();
                    foreach ($requests as $request) {
                        if (array_key_exists("request_" . $request->get_request_id(), $_POST) && $_POST["request_" . $request->get_request_id()]) {
                            $request_service->deny_request($request->get_request_id());
                        }
                    }
                    ?>
                    <div class="col-md-8 center-block" style="float:none">
                        <h4 class="text-center login-header">Requests have been denied. <a href='index.php'>Return to Dashboard</a></h4>
                    </div>
                <?php
                }
            } else {
                ?>
                <div class="col-md-12 left-block" style="float:none">
                    <!--Shift requests table-->
                    <h2>Requested Shifts</h2>
                    <form action="managerequests.php" method="POST">
                        <button name="type" type="submit" value="deny" class="btn btn-secondary">Deny Request</button>
                        <button name="type" type="submit" value="approve" class="btn btn-primary">Approve Request</button>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Selected</th>
                                    <th>Shift ID</th>
                                    <th>Location</th>
                                    <th>Employee Name</th>
                                    <th>Date/Time Start</th>
                                    <th>Date/Time End</th>
                                    <th>Type</th>
                                    <th>Shift Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($requests as $request) {
                                    $shift = $request->get_shift();
                                    $user = $request->get_user();
                                    echo "<tr>" .
                                        "<td><input type='checkbox' name='request_" . htmlentities($request->get_request_id()) . "'></td>" .
                                        "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                                        "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                                        "<td>" . htmlentities($user->get_first_name() . " " . $user->get_last_name()) . "</td>" .
                                        "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                                        "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                                        "<td>" . ($request->get_type() == Request::REQUEST_ASSIGNMENT ? "Assignment" : "Cancellation") . "</td>" .
                                        "<td><a href='shiftdetails.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                                        "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            <?php
            }
            ?>
            </tbody>
            </table>
        </div>
    </div>
    </div>

    <?php
    page_end();
}
?>