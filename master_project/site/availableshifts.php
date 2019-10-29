<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_EMPLOYEE)) {
  page_start($auth_service, "Scheduling OnDemand - Available Shifts", "Available Shifts");

  ?>
  <!-- Refresh Button -->
  <div class="refresh-button" onclick="location.reload();">
    <button type="button" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-refresh"></span> Refresh Lists
    </button>
  </div>

  <!-- All Available Shifts Table-->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <h2>Available Shifts</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Shift ID</th>
              <th>Location</th>
              <th>Required Credentials</th>
              <th>Hourly Rate</th>
              <th>Date/Time Start</th>
              <th>Date/Time End</th>
              <th>Shift Details</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $shifts = $shift_service->get_shifts_between($auth_service->get_user_organization(), false, time(), time() + 180 * 24 * 60 * 60);
            foreach ($shifts as $shift) {
              echo "<tr>" .
                "<td>" . htmlentities($shift->get_shift_id()) . "</td>" .
                "<td>" . htmlentities($shift->get_zip_code()) . "</td>" .
                "<td>" . htmlentities($shift->get_required_position()) . "</td>" .
                "<td>$" . htmlentities($shift->get_pay_differential()) . "</td>" .
                "<td>" . date("Y-m-d h:i A", $shift->get_start_datetime()) . "</td>" .
                "<td>" . date("Y-m-d h:i A", $shift->get_end_datetime()) . "</td>" .
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