<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */
include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
  page_start($auth_service, "Scheduling OnDemand", "Dashboard");

  ?>

  <div class="refresh-button" onclick="location.reload();">
    <button type="button" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-refresh"></span> Refresh Lists
    </button>
  </div>

  <!-- Assigned Shifts Table-->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <h2>Requested Shifts</h2>
        <table class="table table-striped">
          <thead>
            <tr>
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
            $requests = $request_service->get_requests($auth_service->get_user_organization(), Request::STATUS_PENDING);
            foreach ($requests as $request) {
              $shift = $request->get_shift();
              $user = $request->get_user();
              echo "<tr>" .
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
      </div>
      <!-- Pending Approval Table-->
      <div class="container">
        <div class="row">

          <div class="col-md-12 left-block" style="float:none">
            <h2>Uncovered Shifts < 5 Days</h2> <table class="table table-striped">
                <thead>
                  <tr class="danger">
                    <th>Shift ID</th>
                    <th>Location</th>
                    <th>Required License</th>
                    <th>Date/Time Start</th>
                    <th>Date/Time End</th>
                    <th>Shift Details</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $shifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), false, time(), time() + 5 * 24 * 60 * 60);
                  foreach ($shifts as $shift) {
                    echo "<tr>" .
                      "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                      "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                      "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                      "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                      "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                      "<td><a href='shiftdetails.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                      "</tr>";
                  }
                  ?>
                </tbody>
                </table>
          </div>

          <!-- Pending Cancelation Table-->
          <div class="container">
            <div class="row">
              <div class="col-md-12 left-block" style="float:none">
                <h2>Uncovered Shifts (Last 30 Days)</h2>
                <table class="table table-striped">
                  <thead>
                    <tr class="danger">
                      <th>Shift ID</th>
                      <th>Location</th>
                      <th>Required License</th>
                      <th>Date/Time Start</th>
                      <th>Date/Time End</th>
                      <th>Shift Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $shifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), false, time() - (30 * 24 * 60 * 60), time());
                    foreach ($shifts as $shift) {
                      echo "<tr>" .
                        "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                        "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                        "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                        "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                        "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                        "<td><a href='shiftdetails.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                        "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>

              <?php
              page_end();
            }
            ?>