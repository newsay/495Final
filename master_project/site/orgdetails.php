<?php

/**
 * @author Andrew Ritchie
 */
include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
if (authorize($auth_service, $organization_service, User::USER_TYPE_ADMINISTRATOR)) {

    page_start($auth_service, "Scheduling OnDemand - Organization Details");
    $org = $organization_service->get_organization($_GET["id"]);
    if ($org) {
        $users = $user_service->get_users($org->get_id());
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 left-block" style="float:none">
                    <div class="btn-group">
                        <div onclick="admindashboard.php">
                            <a class="btn btn-default" href="admindashboard.php">Return To Dashboard</a>
                        </div>
                    </div>
                    <?php
                    if ($org->get_is_enabled()) {
                        ?>
                        <div class="btn-group">
                            <div onclick="disableorganization.php?id=<?php echo $org->get_id() ?>">
                                <a class="btn btn-default" href="disableorganization.php?id=<?php echo $org->get_id() ?>">Disable Organization</a>
                            </div>
                        </div>
                    <?php
                    } else {
                        ?>
                        <div class="btn-group">
                            <div onclick="enableorganization.php?id=<?php echo $org->get_id() ?>">
                                <a class="btn btn-default" href="enableorganization.php?id=<?php echo $org->get_id() ?>">Enable Organization</a>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <h3>Organization Details</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Organization ID</b></div>
                        <div class="panel-body"><?php echo $org->get_id(); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Organization Name</b></div>
                        <div class="panel-body"><?php echo htmlentities($org->get_name()); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Status</b></div>
                        <div class="panel-body"><?php echo ($org->get_is_enabled() ? "Enabled" : "Disabled"); ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b>Users</b></div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Email</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($users as $user) {
                                    echo "<tr>";
                                    echo "<td>" . $user->get_user_id() . "</td>";
                                    echo "<td>" . htmlentities($user->get_email_address()) . "</td>";
                                    echo "<td>" . htmlentities($user->get_first_name() . " " . $user->get_last_name()) . "</td>";
                                    echo "<td>" . ($user->get_user_type() == User::USER_TYPE_MANAGER ? "Manager" : "Employee")  . "</td>";
                                    echo "<td><a href='adminresetpassword.php?id=" . $user->get_user_id() . "'>Reset Password</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php
        } else {
            ?>
            <h2 class="text-center">Organization does not exist.</h2>
            <h4 class="text-center"><a href="index.php">Return to Dashboard</a></h4>
        <?php
        }
        page_end();
    } ?>