<?php

/**
 * @author Andrew Ritchie
 */
include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/form_helpers.php';


if ($auth_service->is_logged_in() && !$auth_service->is_initialized()) {
  $fields = array(
    array('id' => 'pwd', 'type' => 'password', 'name' => 'pwd', 'display_text' => 'Password',  'required' => true),
    array('id' => 'confirmpwd', 'type' => 'password', 'name' => 'confirmpwd', 'display_text' => 'Confirm Password',  'required' => true),
    array('id' => 'secquest1', 'type' => 'select', 'name' => 'secquest1', 'display_text' => 'Security Question #1', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 1),
    array('id' => 'secquest1answer', 'name' => 'secquest1answer', 'display_text' => 'Security Question #1 Answer', 'required' => true, 'width' => 12),
    array('id' => 'secquest2', 'type' => 'select', 'name' => 'secquest2', 'display_text' => 'Security Question #2', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 2),
    array('id' => 'secquest2answer', 'name' => 'secquest2answer', 'display_text' => 'Security Question #2 Answer', 'required' => true, 'width' => 12),
    array('id' => 'secquest3', 'type' => 'select', 'name' => 'secquest3', 'display_text' => 'Security Question #3', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 3),
    array('id' => 'secquest3answer', 'name' => 'secquest3answer', 'display_text' => 'Security Question #3 Answer', 'required' => true, 'width' => 12),
  );

  page_start($auth_service, "Scheduling OnDemand - First Time Login")
  ?>
  
  <!-- Security Questions Validation -->
  <div class="container">
    <?php
    $errors = array();
    if ($_POST) {
      $errors = required_fields_errors($fields);
      if ($_POST["pwd"] != $_POST["confirmpwd"]) {
        add_error($errors, 'confirmpwd', "Password does not match");
      }
      if ($_POST["secquest1"] == $_POST["secquest2"]) {
        add_error($errors, 'secquest1', "Security questions cannot be the same");
        add_error($errors, 'secquest2', "Security questions cannot be the same");
      }
      if ($_POST["secquest2"] == $_POST["secquest3"]) {
        add_error($errors, 'secquest2', "Security questions cannot be the same");
        add_error($errors, 'secquest3', "Security questions cannot be the same");
      }
      if ($_POST["secquest1"] == $_POST["secquest3"]) {
        add_error($errors, 'secquest1', "Security questions cannot be the same");
        add_error($errors, 'secquest3', "Security questions cannot be the same");
      }
      if (sizeof($errors) == 0) {
        $user_service->update_password_and_security_questions(
          $auth_service->get_user_id(),
          $_POST["pwd"],
          $_POST["secquest1"],
          $_POST["secquest1answer"],
          $_POST["secquest2"],
          $_POST["secquest2answer"],
          $_POST["secquest3"],
          $_POST["secquest3answer"]
        );
        redirect("index.php");
      }
    }
    if (!$_POST || sizeof($errors) > 0) {
      ?>
      <div class="row">
        <div class="col-md-6 center-block" style="float:none">
          <h1 class="text-center">First Time User Setup</h1>
        </div>
        <form method="POST" action="setupsecquestions.php">
          <?php
          build_form($fields, $errors);
          ?>
          <div class="form-group row">
            <div align="center" class="col-sm-12">
              <button type="submit" class="btn btn-default">Submit</button>
            </div>
          </div>
        </form>

      </div>
    <?php
    } else { }
    ?>
  </div>
  <?php
  page_end();
} else {
  redirect("index.php");
}
?>