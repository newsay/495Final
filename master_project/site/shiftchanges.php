<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';
/**
 * @author Andrew Ritchie
 */
if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {

  $shift = $shift_service->get_shift($_GET["id"]);
  if ($shift == null || $auth_service->get_user_organization() != $shift->get_organization_id()) {
    redirect("index.php");
    die();
  }

  page_start($auth_service, "Scheduling OnDemand - Shift Changes");
  ?>
  <!-- Shift Changes Section -->
  <div class="container">
    <div class="row">
      <div class="col-md-12 left-block" style="float:none">
        <div class="btn-group">
          <div onclick="shiftdetails.php?id=<?php echo $_GET["id"] ?>">
            <a class="btn btn-default" href="managerdashboard.php">Return To Shift</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="container">

    <div class="row">
      <div class="col-md-6 center-block" style="float:none">
        <h1 class="text-center">Shift Changes</h1>
      </div>
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th scope="col">Date</th>
          <th scope="col">Details</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $audit_trails = $audit_service->get_audit_history($_GET["id"]);
        for ($i = sizeof($audit_trails) - 1; $i >= 0; $i--) { //Go in reverse order for descending dates
          $audit_trail = $audit_trails[$i];
          echo "<tr>" .
            "<td>" . date("Y-m-d H:i:s", $audit_trail->get_modification_date()) . "</td>" .
            "<td>" . $audit_trail->get_details() . "</td>" .
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