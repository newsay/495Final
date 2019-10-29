<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */
include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

if (authorize($auth_service, $organization_service, User::USER_TYPE_ADMINISTRATOR)) {
  page_start($auth_service, "Scheduling OnDemand", "Dashboard");
  ?>
  <div class="container">
    <div class="row">
      <div class="container">
        <div class="row">
          <div class="col-md-12 left-block" style="float:none">
            <h2>Organizations</h2>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Organization ID</th>
                  <th>Organization Name</th>
                  <th>Status</th>
                  <th>Organization Details</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $orgs = $organization_service->get_organizations();
                foreach ($orgs as $org) {
                  echo "<tr>";
                  echo "<td>" . $org->get_id() . "</td>";
                  echo "<td>" . htmlentities($org->get_name()) . "</td>";
                  echo "<td>" . ($org->get_is_enabled() ? "Enabled" : "Disabled") . "</td>";
                  echo "<td><a href='orgdetails.php?id=" . $org->get_id() . "'>Details</a></td>";
                  echo "</tr>";
                }
                ?>

              </tbody>
            </table>
          </div>

        </div>
      </div>


      </body>

      </html>
      <?php
      page_end();
    }
    ?>