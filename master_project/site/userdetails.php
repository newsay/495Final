<?php

/**
 * @author John Leavitt, Robert Martinez, Andrew Ritchie, Shoshana Yaswen
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
  page_start($auth_service, "Scheduling OnDemand - View User", "View User");

  $userid = $_GET["id"];

  $user = $user_service->get_user_by_id($userid, $auth_service->get_user_organization());

  ?>
  <!--Return to available users button -->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <div class="btn-group">
          <div onclick="managerdashboard.php">
            <a class="btn btn-default" href="managerdashboard.php">Return To Dashboard</a>
          </div>
        </div>
        <div class="btn-group">
          <div onclick="modifyuser.php?id=<?php echo $userid ?>">
            <a class="btn btn-default" href="modifyuser.php?id=<?php echo $userid ?>">Modify User</a>
          </div>
        </div>
        <div class="btn-group">
          <div onclick="deleteuser.php?id=<?php echo $userid ?>">
            <a class="btn btn-default" href="deleteemployee.php?id=<?php echo $userid ?>">Delete User</a>
          </div>
        </div>
      </div>
    </div>
  </div>



  <!-- User Details -->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <h2>User Details</h2>
        <div class="container">
          <div class="row">
            <div class="panel-group">
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>First Name</b></div>
                  <div class="panel-body"> <?php echo htmlentities($user->get_first_name()); ?> </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Last Name</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_last_name()); ?></div>
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
                  <div class="panel-heading"><b>Email</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_email_address()); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Home Phone</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_home_phone()); ?></div>
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
                  <div class="panel-heading"><b>Address 1</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_address_1()); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Mobile Phone</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_mobile_phone()); ?></div>
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
                  <div class="panel-heading"><b>Address 2</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_address_2()); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>City</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_city()); ?></div>
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
                  <div class="panel-heading"><b>State</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_state()); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="panel panel-default">
                  <div class="panel-heading"><b>Zip Code</b></div>
                  <div class="panel-body"><?php echo htmlentities($user->get_zip()); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        page_end();
      }
      ?>