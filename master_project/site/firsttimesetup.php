<html lang="en" dir="ltr">
<?php
/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/misc/form_helpers.php";
$users = $user_service->get_users();
?>
<?php
$fields = array(
  array('id' => 'email', 'type' => 'email', 'name' => 'email', 'display_text' => 'Email Address', 'required' => true),
  array('id' => 'confirmemail', 'type' => 'email', 'name' => 'confirmemail', 'display_text' => 'Confirm Email Address', 'required' => true),
  array('id' => 'pwd', 'type' => 'password', 'name' => 'pwd', 'display_text' => 'Password',  'required' => true),
  array('id' => 'confirmpwd', 'type' => 'password', 'name' => 'confirmpwd', 'display_text' => 'Confirm Password',  'required' => true),
  array('id' => 'secquest1', 'type' => 'select', 'name' => 'secquest1', 'display_text' => 'Security Question #1', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 1),
  array('id' => 'secquest1answer', 'name' => 'secquest1answer', 'display_text' => 'Security Question #1 Answer', 'required' => true, 'width' => 12),
  array('id' => 'secquest2', 'type' => 'select', 'name' => 'secquest2', 'display_text' => 'Security Question #2', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 2),
  array('id' => 'secquest2answer', 'name' => 'secquest2answer', 'display_text' => 'Security Question #2 Answer', 'required' => true, 'width' => 12),
  array('id' => 'secquest3', 'type' => 'select', 'name' => 'secquest3', 'display_text' => 'Security Question #3', 'required' => true, 'options' => get_security_questions(), 'width' => 12, 'default' => 3),
  array('id' => 'secquest3answer', 'name' => 'secquest3answer', 'display_text' => 'Security Question #3 Answer', 'required' => true, 'width' => 12),
  array('id' => 'createSampleData', 'type' => 'checkbox', 'name' => 'createSampleData', 'display_text' => 'Create Sample Data',  'width' => 12)
);
?>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
  <!-- Link to custom css file for some minor formatting, may remove once I understand bootstrap better. -->
  <link rel="stylesheet" type="text/css" href="index.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

  <!-- Title of page -->
  <title>Scheduling OnDemand Installation</title>
</head>
</nav>

<body>

  <!-- Top right image on login page (Theme Image)  -->
  <div class="header-background-image1">
    <img src="images/Capture.PNG" class="img-responsive">
  </div>
  <div class="container">
    <div class="row">

      <div class="col-md-8 center-block" style="float:none">
        <h4 class="text-center login-header">First Time Setup</h4>
        <?php
        if (sizeof($users) == 0) {
          $errors = array();
          if ($_POST) {
            $errors = required_fields_errors($fields);
            if (sizeof($errors) == 0) {
              if ($_POST["email"] != $_POST["confirmemail"]) {
                add_error($errors, 'confirmemail', "Email does not match");
              }
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
            }
          }
          if ($_POST && sizeof($errors) == 0) {
            //Create the databases
            $query = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/database/create_tables.sql');
            $sql_service->execute_multiple($query);
            $user = new User(null, $_POST["email"], 2);
            $user_service->create_user(
              $user,
              $_POST["pwd"],
              $_POST["secquest1"],
              $_POST["secquest1answer"],
              $_POST["secquest2"],
              $_POST["secquest2answer"],
              $_POST["secquest3"],
              $_POST["secquest3answer"]
            );

            ?>
            Your account has been created. Please <a href="index.php">return to the login screen</a> to log in.
            <?php
            if ($_POST["createSampleData"]) {
              $organization_service->create_organization("Test Organization");
              $manager = new User(null, "manager1@test.com", User::USER_TYPE_MANAGER);
              $manager->set_first_name("Manager");
              $manager->set_last_name("One");
              $manager->set_home_phone("000-000-0000");
              $manager->set_mobile_phone("111-111-1111");
              $manager->set_address_1("Address 1");
              $manager->set_address_2("Address 2");
              $manager->set_city("Seattle");
              $manager->set_state("WA");
              $manager->set_zip("12345");
              $manager->set_organization_id(1);
              $user_service->create_user($manager, "Password1", null, null, null, null, null, null);

              $employee = new User(null, "employee1@test.com", User::USER_TYPE_EMPLOYEE);
              $employee->set_first_name("employee");
              $employee->set_last_name("One");
              $employee->set_home_phone("000-000-0000");
              $employee->set_mobile_phone("111-111-1111");
              $employee->set_address_1("Address 1");
              $employee->set_address_2("Address 2");
              $employee->set_city("Seattle");
              $employee->set_state("WA");
              $employee->set_zip("12345");
              $employee->set_organization_id(1);
              $user_service->create_user($employee, "Password1", null, null, null, null, null, null);

              echo "A manager has been created with the user name manager1@test.com and password Password1<br>";
              echo "An employee has been created with the user name employee1@test.com and password Password1<br>";
            }
          } else {
            ?>
            <!-- Admin account creation form -->
            <form action="firsttimesetup.php" method="POST"></a>

              <?php build_form($fields, $errors); ?>

              <button type="submit" class="btn btn-default">Initialize</button>
            </form>
          <?php
          }
        } else {
          ?>
          First time setup has already been performed. Please return to the <a href="index.php">home page</a> to log in.
        <?php

        }
        ?>
      </div>

    </div>
  </div>
  <div class="footer-background-image1">
    <img src="images/footer1.PNG" class="img-responsive">
  </div>
</body>

</html>