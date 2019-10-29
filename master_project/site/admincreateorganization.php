<?php

/**
 * @author Andrew Ritchie
 */
include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
if (authorize($auth_service, $organization_service, User::USER_TYPE_ADMINISTRATOR)) {
  page_start($auth_service, "Scheduling On Demand", "New Organization");
  $fields = array(
    array('id' => 'inputOrgName', 'name' => 'orgName', 'display_text' => 'Organization Name', 'placeholder_text' => 'Organization Name', 'width' => 12, 'required' => true), array('id' => 'inputFirstName', 'name' => 'firstName', 'display_text' => 'Manager First Name', 'placeholder_text' => 'First Name', 'width' => 6, 'required' => true), array('id' => 'inputLastName', 'name' => 'lastName', 'display_text' => 'Manager Last Name', 'placeholder_text' => 'First Name', 'width' => 6, 'required' => true), array('id' => 'inputEmail', 'name' => 'email', 'display_text' => 'Manager Email', 'placeholder_text' => 'Email', 'width' => 6, 'required' => true), array('id' => 'inputConfirmEmail', 'name' => 'confirmEmail', 'display_text' => 'Confirm Manager Email', 'placeholder_text' => 'Email', 'width' => 6, 'required' => true), array('id' => 'inputPassword', 'name' => 'password', 'display_text' => 'Manager Password', 'placeholder_text' => 'Password', 'width' => 6, 'required' => true, 'type' => 'password'), array('id' => 'inputConfirmPassword', 'name' => 'confirmPassword', 'display_text' => 'Confirm Manager Password', 'placeholder_text' => 'Password', 'width' => 6, 'required' => true, 'type' => 'password'), array('id' => 'inputHomePhone', 'name' => 'homePhone', 'display_text' => 'Manager Home Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6), array('id' => 'inputMobilePhone', 'name' => 'mobilePhone', 'display_text' => 'Manager Mobile Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6), array('id' => 'inputAddress', 'name' => 'address', 'display_text' => 'Manager Address', 'placeholder_text' => '123 Main St', 'width' => 6), array('id' => 'inputAddress2', 'name' => 'address2', 'display_text' => 'Manager Address 2', 'placeholder_text' => 'Apartment, studio, floor', 'width' => 6), array('id' => 'inputCity', 'name' => 'city', 'display_text' => 'Manager City', 'placeholder_text' => 'City', 'width' => 6), array('id' => 'inputState', 'name' => 'state', 'display_text' => 'Manager State', 'width' => 6, 'type' => 'select', 'options' => get_states(true)), array('id' => 'inputZip', 'name' => 'zip', 'display_text' => 'Manager Zip Code', 'width' => 6)
  );


  ?>

  <body>
    <div class="container">
      <?php
      $errors = null;
      if ($_POST) {
        $errors = required_fields_errors($fields);
        if ($_POST["email"] != $_POST["confirmEmail"]) {
          add_error($errors, 'confirmEmail', "Email does not match");
        }
        if ($_POST["password"] != $_POST["confirmPassword"]) {
          add_error($errors, 'confirmPassword', "Password does not match");
        }
        if (sizeof($errors) == 0) {

          $existing_user = $user_service->get_user_by_email_address($_POST["email"]);
          if ($existing_user) {
            add_error($errors, 'email', 'Email already in use.');
          }
          if (sizeof($errors) == 0) {
            $org_id = $organization_service->create_organization($_POST["orgName"]);
            $user = new User(null, $_POST["email"], 1); //Manager
            $user->set_first_name($_POST["firstName"]);
            $user->set_last_name($_POST["lastName"]);
            $user->set_home_phone($_POST["homePhone"]);
            $user->set_mobile_phone($_POST["mobilePhone"]);
            $user->set_address_1($_POST["address"]);
            $user->set_address_2($_POST["address2"]);
            $user->set_city($_POST["city"]);
            $user->set_state($_POST["state"]);
            $user->set_zip($_POST["zip"]);
            $user->set_organization_id($org_id);
            $user_service->create_user($user, $_POST["password"], null, null, null, null, null, null);
            redirect("admindashboard.php")
            ?>
          <?php
          }
        }
      }
      if (!$_POST || sizeof($errors) > 0) {
        ?>
        <div class="row">
          <div class="col-md-6 center-block" style="float:none">
            <h1 class="text-center">New Organization</h1>
          </div>

          <form method="POST" action="admincreateorganization.php">
            <?php
            build_form($fields, $errors);
            ?>
            <div class="form-group row">
              <div align="center" class="col-sm-12">
                <button type="submit" class="btn btn-default">New Organization</button>
              </div>
            </div>
          </form>

        </div>
      <?php
      }
      page_end();
    }
    ?>