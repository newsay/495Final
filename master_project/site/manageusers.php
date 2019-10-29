<?php

/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
  page_start($auth_service, "Scheduling OnDemand - Manage Users", "Manage Users");
  ?>
  
  <!--View existing users table-->
  <div class="refresh-button" onclick="location.reload();">
    <a href="newuser.php" role="button" class="btn btn-default btn-sm">
      <span class="glyphicon glyphicon-plus"></span> New User
    </a>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <h2>User Directory</h2>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Zip Code</th>
              <th>Type</th>
              <th>User Details</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $users = $user_service->get_users($auth_service->get_user_organization());
            foreach ($users as $user) {
              echo "<tr>";
              echo "<td>" . htmlentities($user->get_user_id()) . "</td>";
              echo "<td>" . htmlentities($user->get_first_name()) . "</td>";
              echo "<td>" . htmlentities($user->get_last_name()) . "</td>";
              echo "<td>" . htmlentities($user->get_zip()) . "</td>";
              echo "<td>" . ($user->get_user_type() == User::USER_TYPE_EMPLOYEE ? 'Employee' : 'Manager') . "</td>";
              echo "<td><a href='userdetails.php?id=" . htmlentities($user->get_user_id()) . "'>Details</a></td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php
    page_end();
  }
  ?>