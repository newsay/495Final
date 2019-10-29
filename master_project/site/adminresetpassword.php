<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_ADMINISTRATOR)) {
    page_start($auth_service, "Scheduling OnDemand - My Page");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 center-block" style="float:none">
                <h1 class="text-center">Reset User Password</h1>
            </div>
        </div>
        <?php
        //The following is an example of how to create a simple form.
        $fields = array(
            array('id' => 'inputNewPassword', 'name' => 'newPassword', 'display_text' => "New Password", 'type' => 'password', 'width' => 12, 'required' => true),
            array('id' => 'inputConfirmNewPassword', 'name' => 'confirmNewPassword', 'display_text' => "Confirm New Password", 'type' => 'password', 'width' => 12, 'required' => true),
        );
        $errors = array();
        //Check to see if the form has posted
        if ($_POST) {
            //Check if required fields have been populated
            $errors = required_fields_errors($fields);
            //Do additional validation
            if ($_POST["newPassword"] != $_POST["confirmNewPassword"]) {
                add_error($errors, "newPassword", "Passwords do not match");
                add_error($errors, "confirmNewPassword", "");
            }
        }
        //Check if the form is ready for processing
        if ($_POST && sizeof($errors) == 0) {
            $user_service->update_password($_GET["id"], $_POST["newPassword"]);
            redirect("index.php");
        } else {
            //If the form hasn't yet been submitted, display it.
            ?>
            <form method="POST" action="adminresetpassword.php?id=<?php echo $_GET["id"] ?>">
                <?php
                build_form($fields, $errors);
                ?>
                <div class="form-group row">
                    <div align="center" class="col-sm-12">
                        <button type="submit" class="btn btn-default">Submit</button>
                    </div>
                </div>
            </form>
        <?php
        }
        ?>
    </div>


    <?php
    page_end();
}
?>