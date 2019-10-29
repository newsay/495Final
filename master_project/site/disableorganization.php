<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';
/**
 * @author Andrew Ritchie
 */

if (authorize($auth_service, $organization_service, User::USER_TYPE_ADMINISTRATOR)) {

    if ($_POST) {
        if ($_POST["confirm"]) {
            $organization_service->disable_organization($_GET["id"]);
            redirect("managerdashboard.php");
        }
    } else {
        page_start($auth_service, "Scheduling OnDemand - Disable Organization");
        ?>

        <div class="container">
            <div class="row">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Disable Organization</h1>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 center-block" style="float:none">
                    <h1 class="text-center">Are you sure you want to disable this organization?</h1>

                </div>
            </div>
            <div class="row ">
                <div class="col-md-12 text-center" style="float: none">
                    <div class="btn-group ">
                        <form method="post" action="disableorganization.php?id=<?php echo $_GET["id"] ?>">
                            <input class="btn btn-danger" name="confirm" type="submit" value="Yes">
                            <a class="btn btn-default" href="orgdetails.php?id=<?php echo $_GET["id"] ?>">No</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        </div>


        <?php
        page_end();
    }
}
?>