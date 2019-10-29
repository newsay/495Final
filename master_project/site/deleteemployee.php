<?php

/**
 * @author  Andrew Ritchie, Robert Martinez, John Leavitt
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {

    $userid = $_GET["id"];
    $user = $user_service->get_user_by_id($userid, $auth_service->get_user_organization());
    if ($auth_service->get_user_organization() != $user->get_organization_id()) {
        redirect("managerdashboard.php");
    } else if ($user->get_user_id() == $auth_service->get_user_id()) {
        page_start($auth_service, "Scheduling OnDemand - Delete Employee");
        ?>
        <div class="row">
            <div class="col-md-12 center-block text-center" style="float:none">
                <h1>
                    Cannot delete the currently logged in user.
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 center-block text-center" style="float:none">
                <h2>
                    <a href="manageusers.php">Return to Manage Users</a>
                </h2>
            </div>
        </div>
        <?php
        page_end();
    } else if ($_POST) {
        if ($_POST["confirm"]) {
            $user_service->delete_user($_GET["id"]);
            redirect("managerdashboard.php");
        }
    } else {
        page_start($auth_service, "Scheduling OnDemand - Delete Employee");

        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Delete Employee</h1>
                </div>
            </div>

            <div class="row ">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Are you sure you want to delete this employee?</h1>

                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 text-center" style="float: none">
                    <div class="btn-group ">
                        <form method="post" action="deleteemployee.php?id=<?php echo $user->get_user_id() ?>">
                            <input class="btn btn-danger" name="confirm" type="submit" value="Yes">
                            <a class="btn btn-default" href="userdetails.php?id=<?php echo $user->get_user_id() ?>">No</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <?php

        page_end();
    }
}
?>