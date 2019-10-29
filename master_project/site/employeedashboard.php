<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_EMPLOYEE)) {
  page_start($auth_service, "Scheduling OnDemand", "Dashboard");

  ?>
  <!-- Refresh button -->
  <div class="refresh-button" onclick="location.reload();">
    <button type="button" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-refresh"></span> Refresh Lists
    </button>
  </div>

  <!-- Available Shifts Count -->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <h2>Available Shifts</h2>
        <?php
        $availableshifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), 0, time(), time() + 180 * 24 * 60 * 60);
        foreach ($availableshifts as $availableshift) {
          $count = $count + 1;
        }
        echo "<h1><a href='availableshifts.php'>" . $count . "</a>"
        ?>
      </div>

      <!-- Pending Requests Table-->
      <div class="container">
        <div class="row">

          <div class="col-md-12 left-block" style="float:none">
            <h2>Pending Requests</h2>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Shift ID</th>
                  <th>Location</th>
                  <th>Required Credentials</th>
                  <th>Hourly Rate</th>
                  <th>Date/Time Start</th>
                  <th>Date/Time End</th>
                  <th>Type</th>
                  <th>Shift Details</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $requests = $request_service->get_requests($auth_service->get_user_organization(), Request::STATUS_PENDING, $auth_service->get_user_id());
                foreach ($requests as $request) {
                  $shift = $request->get_shift();
                  echo "<tr>" .
                    "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                    "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                    "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                    "<td>$" . htmlentities($shift->get_pay_differential()) . "</td>" .
                    "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                    "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                    "<td>" . ($request->get_type() == Request::REQUEST_ASSIGNMENT ? "Assignment" : "Cancellation") . "</td>" .
                    "<td><a href='viewshift.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                    "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>

          <!-- Approved Requests Table-->
          <div class="container">
            <div class="row">
              <div class="col-md-12 left-block" style="float:none">
                <h2>Approved Requests</h2>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Shift ID</th>
                      <th>Location</th>
                      <th>Required Credentials</th>
                      <th>Hourly Rate</th>
                      <th>Date/Time Start</th>
                      <th>Date/Time End</th>
                      <th>Type</th>
                      <th>Shift Details</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $requests = $request_service->get_requests($auth_service->get_user_organization(), Request::STATUS_APPROVED, $auth_service->get_user_id());
                    foreach ($requests as $request) {
                      $shift = $request->get_shift();
                      echo "<tr>" .
                        "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                        "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                        "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                        "<td>$" . htmlentities($shift->get_pay_differential()) . "</td>" .
                        "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                        "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                        "<td>" . ($request->get_type() == Request::REQUEST_ASSIGNMENT ? "Assignment" : "Cancellation") . "</td>" .
                        "<td><a href='viewshift.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
                        "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>


              <!-- Denied Requests Table-->
              <div class="container">
                <div class="row">
                  <div class="col-md-12 left-block" style="float:none">
                    <h2>Denied Requests</h2>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Shift ID</th>
                          <th>Location</th>
                          <th>Required Credentials</th>
                          <th>Hourly Rate</th>
                          <th>Date/Time Start</th>
                          <th>Date/Time End</th>
                          <th>Type</th>
                          <th>Shift Details</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $requests = $request_service->get_requests($auth_service->get_user_organization(), Request::STATUS_DENIED, $auth_service->get_user_id());
                        foreach ($requests as $request) {
                          $shift = $request->get_shift();
                          echo "<tr>" .
                            "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                            "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                            "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                            "<td>$" . htmlentities($shift->get_pay_differential()) . "</td>" .
                            "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                            "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
                            "<td>" . ($request->get_type() == Request::REQUEST_ASSIGNMENT ? "Assignment" : "Cancellation") . "</td>" .
                            "<td><a href='viewshift.php?id=" . htmlentities($shift->get_shift_id())  . "'>Details</a></td>" .
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