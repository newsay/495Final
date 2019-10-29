<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_EMPLOYEE)) {
  page_start($auth_service, "Scheduling OnDemand - Shift Details", "");

  $shiftid = $_GET["id"];


  $shift = $shift_service->get_shift($shiftid);
  ?>
  <!--  Return to available shifts button and request shift button -->
  <div class="container">

    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <?php
        if ($shift->get_assigned_user() == null) {
          ?>
          <div class="btn-group">
            <form action="requestconfirmation.php" method="get">
              <button class=" btn btn-default" name="requestshiftid" type="submit" value="<?php echo $shiftid; ?>">Request Shift Assignment</button>
            </form>
          </div>
        <?php
        }
        ?>


        <?php



        if ($auth_service->get_user_id() == $shift->get_assigned_user_id()) {
          ?>
          <!-- This button will appear only if the shift that is being viewed is assigned to the user the is currently logged in -->

          <div class="btn-group">
            <form action="requestcancelation.php" method="get">
              <button class=" btn btn-default" name="requestshiftid" type="submit" value="<?php echo $shiftid; ?>">Request Shift Cancellation</button>
            </form>
          </div>
        </div>
      </div>
    </div>


  <?php

  }
  if ($auth_service->get_user_organization() != $shift->get_organization_id()) {
    header("Location: employeedashboard.php");
    die();
  } else {

    $timestart = date("H:i:s", $shift->get_start_datetime());
    $datestart = date("Y-m-d", $shift->get_start_datetime());
    $timeend = date("H:i:s", $shift->get_end_datetime());
    $dateend = date("Y-m-d", $shift->get_end_datetime());
    ?>

    <!-- Shift Details -->
    <div class="container">
      <div class="row">
        <div class="col-md-12 left-block" style="float:none">
          <h2>Shift Details</h2>
          <div class="container">
            <div class="row">
              <div class="panel-group">
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Shift ID</b></div>
                    <div class="panel-body"> <?php echo htmlentities($shift->get_shift_id()); ?> </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Zip Code</b></div>
                    <div class="panel-body"><?php echo htmlentities($shift->get_zip_code()); ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="panel-group">
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Start Time</b></div>
                    <div class="panel-body"><?php echo $timestart; ?></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Start Date</b></div>
                    <div class="panel-body"><?php echo $datestart; ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="panel-group">
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>End Time</b></div>
                    <div class="panel-body"><?php echo $timeend; ?></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>End Date</b></div>
                    <div class="panel-body"><?php echo $dateend; ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
              <div class="panel-group">
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Required Credentials</b></div>
                    <div class="panel-body"><?php echo htmlentities($shift->get_required_position()); ?></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Hourly Rate</b></div>
                    <div class="panel-body">$<?php echo htmlentities($shift->get_pay_differential()); ?></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Special Requirements Section -->
          <div class="container">
            <h3 align="center">Special Requirements</h3>
            <div class="row">
              <div class="panel-group">
                <div class="col-md-12">
                  <div class="panel panel-default">
                    <div class="panel-heading"><b>Shift Specific</b></div>
                    <div class="panel-body"><?php echo htmlentities($shift->get_special_requirements()); ?></div>
                  </div>
                </div>


              </div>

              <?php
              page_end();
            }
          }
          ?>