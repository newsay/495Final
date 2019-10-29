<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';

/**
 * @author Shoshana Yaswen
 */
 
if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
  page_start($auth_service, "Scheduling OnDemand - New User");
  ?>
  
  <!--Create new user -->
  <div class="container">
    <div class="row">
      <div class="col-md-6 center-block" style="float:none">
        <h1 class="text-center">New User</h1>
      </div>
    </div>
    <?php
    $fields = array(
      array('id' => 'inputFirstName', 'name' => 'firstName', 'display_text' => 'First Name', 'placeholder_text' => 'First Name', 'width' => 6, 'required' => true), array('id' => 'inputLastName', 'name' => 'lastName', 'display_text' => 'Last Name', 'placeholder_text' => 'First Name', 'width' => 6, 'required' => true), array('id' => 'inputEmail', 'name' => 'email', 'display_text' => 'Email', 'placeholder_text' => 'Email', 'width' => 6, 'required' => true), array('id' => 'inputConfirmEmail', 'name' => 'confirmEmail', 'display_text' => 'Confirm Email', 'placeholder_text' => 'Email', 'width' => 6, 'required' => true), array('id' => 'inputPassword', 'name' => 'password', 'display_text' => 'Password', 'placeholder_text' => 'Password', 'width' => 6, 'required' => true, 'type' => 'password'), array('id' => 'inputConfirmPassword', 'name' => 'confirmPassword', 'display_text' => 'Confirm Password', 'placeholder_text' => 'Password', 'width' => 6, 'required' => true, 'type' => 'password'), array('id' => 'inputHomePhone', 'name' => 'homePhone', 'display_text' => 'Home Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6), array('id' => 'inputMobilePhone', 'name' => 'mobilePhone', 'display_text' => 'Mobile Phone', 'placeholder_text' => '(000) 000-0000', 'width' => 6), array('id' => 'inputAddress', 'name' => 'address', 'display_text' => 'Address', 'placeholder_text' => '123 Main St', 'width' => 6), array('id' => 'inputAddress2', 'name' => 'address2', 'display_text' => 'Address 2', 'placeholder_text' => 'Apartment, studio, floor', 'width' => 6), array('id' => 'inputCity', 'name' => 'city', 'display_text' => 'City', 'placeholder_text' => 'City', 'width' => 6), array('id' => 'inputState', 'name' => 'state', 'display_text' => 'State', 'width' => 6, 'type' => 'select', 'options' => get_states(true)), array('id' => 'inputZip', 'name' => 'zip', 'display_text' => 'Zip Code', 'width' => 6), array('id' => 'selectType', 'name' => 'type', 'display_text' => 'User Type', 'width' => 6, 'type' => 'select', 'options' => get_user_types(), 'default' => User::USER_TYPE_EMPLOYEE)
    );
    $errors = array();
    
    // Check for entry conficts
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
          $org_id = $auth_service->get_user_organization();
          $user = new User(null, $_POST["email"], $_POST['type']);
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
          ?>
          <div class="col-md-8 center-block" style="float:none">
            <h4 class="text-center login-header">The user has been created in the system. <a href="managerdashboard.php">Return to dashboard</a></h4>

          </div>
        <?php
        }
      }
    }
    if (!$_POST || sizeof($errors) > 0) {
      //If the form hasn't yet been submitted, display it.
      ?>
      <form method="POST" action="newuser.php">
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