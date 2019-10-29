<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_EMPLOYEE)) {
    page_start($auth_service, "Scheduling OnDemand", "Dashboard");

    $requestedshift = $_GET["requestshiftid"];

    $shift = $shift_service->get_shift($_GET["requestshiftid"]);

    // if statement that ensure the user and the shift are parto f the same organization
    //before attempting to sign them up for the shift  
    if ($auth_service->get_user_organization() != $shift->get_organization_id()) {
        header("Location: employeedashboard.php");
        die();
    } else {
        if ($shift->get_status() == Shift::STATUS_ASSIGNED && $auth_service->get_user_id() == $shift->get_assigned_user_id()) {
            echo '<h2 class="text-center">--- Cancelation Request Submitted ---</h2>';
            echo '<h3 class="text-center">--- You Can Track Your Request On Your Dashboard ---</h3>';

            // Submits a cancelation                  
            $request_service->submit_request(Request::REQUEST_CANCELLATION, $auth_service->get_user_id(), $_GET["requestshiftid"]);
        } else {
            echo '<h2 class="text-center">--- Failed to Submit Request ---</h2>';
            echo '<h3 class="text-center">--- This shift has already been assigned ---</h3>';
        }
        ?>


        <!-- Return to Dashboard button on confirmation page -->
        <div class="container">
            <div class="row">
                <div class="col-md-12 left-block" style="float:none" align="center">
                    <div class="btn-group">
                        <div onclick="employeedashboard.php">
                            <a class="btn btn-default" href="employeedashboard.php">Return To Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php

        page_end();
    }
}
?>