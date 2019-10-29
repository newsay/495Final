<?php

/**
 * @author  Andrew Ritchie, Robert Martinez, John Leavitt
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {

    $shiftid = $_GET["id"];
    $shift = $shift_service->get_shift($shiftid);
    if ($auth_service->get_user_organization() != $shift->get_organization_id()) {
        redirect("managerdashboard.php");
    } else if ($_POST) {
        if ($_POST["confirm"]) {
            $shift_service->delete_shift($_GET["id"]);
            redirect("managerdashboard.php");
        }
    } else {
        page_start($auth_service, "Scheduling OnDemand - Delete Shift");

        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Delete Shift</h1>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Are you sure you want to delete this shift?</h1>

                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 text-center" style="float: none">
                    <div class="btn-group ">
                        <form method="post" action="deleteshift.php?id=<?php echo $shiftid ?>">
                            <input class="btn btn-danger" name="confirm" type="submit" value="Yes">
                            <a class="btn btn-default" href="shiftdetails.php?id=<?php echo $shiftid ?>">No</a>
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