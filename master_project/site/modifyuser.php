<?php

/**
 * @author Andrew Ritchie, Robert Martinez, Shoshana Yaswen
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
    page_start($auth_service, "Scheduling OnDemand - Modify User");
    ?>
    <!--Modify user form-->
    <div class="container">
        <div class="row">
            <div class="col-md-6 center-block" style="float:none">
                <h1 class="text-center">Modify User</h1>
            </div>
        </div>
        
        <?php
        $user = $user_service->get_user_by_id($_GET["id"], $auth_service->get_user_organization());

        if ($user == null || $user->get_organization_id() != $auth_service->get_user_organization()) {
            redirect("index.php");
            die();
        }
        if ($user->get_user_id() == $auth_service->get_user_id()) {
            redirect("editprofile.php");
            die();
        }
        // Fill with some existing data
        $fields = array(
            array('id' => 'inputFirstName', 'name' => 'firstName', 'display_text' => 'First Name', 'placeholder_text' => 'First Name', 'width' => 6, 'default' => $user->get_first_name()), array('id' => 'inputLastName', 'name' => 'lastName', 'display_text' => 'Last Name', 'placeholder_text' => 'First Name', 'width' => 6, 'default' => $user->get_last_name()), array('id' => 'inputNewPassword', 'name' => 'newPassword', 'display_text' => 'New Password', 'placeholder_text' => 'Password', 'width' => 6, 'type' => 'password'), array('id' => 'inputConfirmPassword', 'name' => 'confirmPassword', 'display_text' => 'Confirm New Password', 'placeholder_text' => 'Password', 'width' => 6, 'type' => 'password'), array('id' => 'inputHomePhone', 'name' => 'homePhone', 'display_text' => 'Home Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6, 'default' => $user->get_home_phone()), array('id' => 'inputAddress', 'name' => 'address', 'display_text' => 'Address 1', 'placeholder_text' => '123 Main St', 'width' => 6, 'default' => $user->get_address_1()), array('id' => 'inputMobilePhone', 'name' => 'mobilePhone', 'display_text' => 'Mobile Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6, 'default' => $user->get_mobile_phone()), array('id' => 'inputAddress2', 'name' => 'address2', 'display_text' => 'Address 2', 'placeholder_text' => 'Apartment, studio, floor', 'width' => 6, 'default' => $user->get_address_2()), array('id' => 'inputCity', 'name' => 'city', 'display_text' => 'City', 'placeholder_text' => 'City', 'width' => 6, 'default' => $user->get_city()), array('id' => 'inputState', 'name' => 'state', 'display_text' => 'State', 'width' => 6, 'type' => 'select', 'options' => get_states(true), 'default' => $user->get_state()), array('id' => 'inputZip', 'name' => 'zip', 'display_text' => 'Zip Code', 'width' => 6, 'default' => $user->get_zip())
        );
        $errors = array();
        //Check to see if the form has posted
        if ($_POST) {
            //Check if required fields have been populated
            $errors = required_fields_errors($fields);

            $existing_user = $user_service->get_user_by_id($_GET["id"], $auth_service->get_user_organization());
            if ($_POST["newPassword"] != $_POST["confirmPassword"]) {
                add_error($errors, 'confirmPassword', "Password does not match");
            }
            if (sizeof($errors) == 0) {
                if (sizeof($errors) == 0) {
                    $updateduser = new User($_GET["id"], $user->get_email_address(), $user->get_user_type());
                    $updateduser->set_first_name($_POST["firstName"]);
                    $updateduser->set_last_name($_POST["lastName"]);
                    $updateduser->set_home_phone($_POST["homePhone"]);
                    $updateduser->set_mobile_phone($_POST["mobilePhone"]);
                    $updateduser->set_address_1($_POST["address"]);
                    $updateduser->set_address_2($_POST["address2"]);
                    $updateduser->set_city($_POST["city"]);
                    $updateduser->set_state($_POST["state"]);
                    $updateduser->set_zip($_POST["zip"]);
                    $updateduser->set_organization_id($auth_service->get_user_organization());
                    $user_service->modify_user($updateduser);
                    if (strlen($_POST["newPassword"]) > 0) {
                        $user_service->update_password($_GET["id"], $_POST["newPassword"]);
                    }
                    ?>
                    <div class="col-md-8 center-block" style="float:none">
                        <h4 class="text-center login-header">User changes saved. <a href="managerdashboard.php">Return to dashboard</a></h4>

                    </div>
                <?php
                }
            }
        }
        if (!$_POST || sizeof($errors) > 0) {
            //If the form hasn't yet been submitted, display it.
            ?>
            <form method="POST" action="modifyuser.php?id=<?php echo $_GET["id"]; ?>">
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