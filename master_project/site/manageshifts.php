<?php

/**
 * @author Andrew Ritchie, Robert Martinez
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
    page_start($auth_service, "Scheduling OnDemand - Manage Shifts", "Manage Shifts");
    ?>
    <div class="refresh-button" onclick="location.reload();">
        <a href="newshift.php" role="button" class="btn btn-default btn-sm">
            <span class="glyphicon glyphicon-plus"></span> Create Shift
        </a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-12 left-block" style="float:none">
                <!--View upcoming shifts table-->
                <h2>Upcoming Shifts in the Next 180 Days</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Shift ID</th>
                            <th>Location</th>
                            <th>Required License</th>
                            <th>Date/Time Start</th>
                            <th>Date/Time End</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Shift Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $shifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), null, time(), time() + 180 * 24 * 60 * 60);
                        foreach ($shifts as $shift) {
                            $assigned_user = $shift->get_assigned_user();
                            echo "<tr" . ($shift->get_status() == SHIFT::STATUS_UNASSIGNED ? " class='danger'" : "") . ">" .
                                "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                                "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                                "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                                "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                                "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                                "<td>" . ($shift->get_status() == Shift::STATUS_ASSIGNED ? "Assigned" : "Unassigned") . "</td>" .
                                "<td>" . ($assigned_user != null ? $assigned_user->get_first_name() . ' ' . $assigned_user->get_last_name() : '') . "</td>" .
                                "<td><a href='shiftdetails.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                                "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 left-block" style="float:none">
                <!--View past shifts table-->
                <h2>Completed Shifts in the Last 180 Days</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Shift ID</th>
                            <th>Location</th>
                            <th>Required License</th>
                            <th>Date/Time Start</th>
                            <th>Date/Time End</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Shift Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $shifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), null, time() - 180 * 24 * 60 * 60, time());
                        for ($i = sizeof($shifts) - 1; $i >= 0; $i--) {
                            $shift = $shifts[$i];
                            $assigned_user = $shift->get_assigned_user();
                            echo "<tr>" .
                                "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                                "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                                "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                                "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                                "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                                "<td>" . ($shift->get_status() == Shift::STATUS_ASSIGNED ? "Assigned" : "Unassigned") . "</td>" .
                                "<td>" . ($assigned_user != null ? $assigned_user->get_first_name() . ' ' . $assigned_user->get_last_name() : '') . "</td>" .
                                "<td><a href='shiftdetails.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                                "</tr>";
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