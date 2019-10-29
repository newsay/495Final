<?php

/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

$fields = array(
  array('id' => 'email', 'type' => 'email', 'name' => 'email', 'display_text' => 'Email Address', 'required' => true, 'width' => 12),
  array('type' => 'hidden', 'name' => 'stage', 'default' => 1),
);
page_start($auth_service, "Scheduling OnDemand - Reset Password");
?>

<!-- Top right image on login page (Theme Image)  -->
<div class="header-background-image1">
  <img src="images/Capture.PNG" class="img-responsive">
</div>
<div class="container">
  <div class="row">

    <div class="col-md-8 center-block" style="float:none">
      <h4 class="text-center login-header">Reset Password</h4>
      <?php

      $errors = array();
      if ($_POST) {
        $errors = required_fields_errors($fields);
      }
      if ($_POST && (sizeof($errors) == 0 && $_POST["stage"] == 2)) {
        if ($_POST["pwd"] != $_POST["confirmpwd"]) {
          add_error($errors, 'confirmpwd', 'Password does not match');
        } else {
          $success = $auth_service->validate_security_questions($_POST["email"], $_POST["question1"], $_POST["question2"], $_POST["question3"]);
          if (!$success) {
            echo "Your security question answers were incorrect. Please try again.";
            add_error($errors, '', 'invalid');
          } else {
            $user_service->update_password($_POST["userid"], $_POST["pwd"]);
            echo "Your password has been reset. You may now <a href='index.php'>log in</a>.";
            $fields = array();
          }
        }
      }
      if (
        $_POST && (sizeof($errors) == 0 && $_POST["stage"] == 1) || (sizeof($errors) > 0  && $_POST["stage"] == 2)
      ) {

        $user = $user_service->get_user_by_email_address($_POST["email"]);
        if ($user == null) {
          add_error($errors, "email", "User Not Found");
        } else {
          $secquestions = $auth_service->get_security_questions($_POST["email"]);
          $secquestions_reference = get_security_questions(false);
          $fields = array(
            array('id' => 'question1', 'type' => 'text', 'name' => 'question1', 'display_text' => $secquestions_reference[$secquestions[0]][1], 'required' => true, 'width' => 12),
            array('id' => 'question2', 'type' => 'text', 'name' => 'question2', 'display_text' => $secquestions_reference[$secquestions[1]][1], 'required' => true, 'width' => 12),
            array('id' => 'question3', 'type' => 'text', 'name' => 'question3', 'display_text' => $secquestions_reference[$secquestions[2]][1], 'required' => true, 'width' => 12),
            array('id' => 'pwd', 'type' => 'password', 'name' => 'pwd', 'display_text' => "New Password", 'required' => true, 'width' => 12),
            array('id' => 'confirmpwd', 'type' => 'password', 'name' => 'confirmpwd', 'display_text' => "Confirm New Password", 'required' => true, 'width' => 12),
            array('type' => 'hidden', 'name' => 'email', 'default' => $_POST["email"]),
            array('type' => 'hidden', 'name' => 'userid', 'default' => $user->get_user_id()),
            array('type' => 'hidden', 'name' => 'stage', 'default' => 2)
          );
        }
      }


      ?>
      <p></p>
      <?php
      if (sizeof($fields) > 0) {
        ?>
        <form action="resetpassword.php" method="POST"></a>

          <?php build_form($fields, $errors); ?>

          <button type="submit" class="btn btn-default">Continue</button>
        </form>
      <?php
      } ?>

    </div>

  </div>
</div>
<div class="footer-background-image1">
  <img src="images/footer1.PNG" class="img-responsive">
</div>
<?php
page_end();
?>