<?php

/**
 * @author John Leavitt, Robert Martinez, Andrew Ritchie
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
  page_start($auth_service, "Scheduling OnDemand - Available Shifts", "Available Shifts");

  $shiftid = $_GET["id"];

  $shift = $shift_service->get_shift($shiftid);

  ?>
  <!--Return to available shifts button -->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <div class="btn-group">
          <div onclick="managerdashboard.php">
            <a class="btn btn-default" href="managerdashboard.php">Return To Dashboard</a>
          </div>
        </div>
        <div class="btn-group">
          <div onclick="modifyshift.php?id=<?php echo $shiftid ?>">
            <a class="btn btn-default" href="modifyshift.php?id=<?php echo $shiftid ?>">Modify Shift</a>
          </div>
        </div>
        <div class="btn-group">
          <div onclick="deleteshift.php?id=<?php echo $shiftid ?>">
            <a class="btn btn-default" href="deleteshift.php?id=<?php echo $shiftid ?>">Delete Shift</a>
          </div>
        </div>
        <div class="btn-group">
          <div onclick="shiftchanges.php?id=<?php echo $shiftid ?>">
            <a class="btn btn-default" href="shiftchanges.php?id=<?php echo $shiftid ?>">View Shift Changes</a>
          </div>
        </div>
      </div>
    </div>
  </div>



  <?php

  $shift = $shift_service->get_shift($_GET["id"]);

  if ($auth_service->get_user_organization() != $shift->get_organization_id()) {
    header("Location: managerdashboard.php");
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