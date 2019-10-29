<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
//If the user is logged in, display their respective dashboard. Otherwise, display the log in page.
function display_dashboard($auth_service)
{
  $url = null;
  if (!$auth_service->is_initialized()) {
    redirect('setupsecquestions.php');
  } else if ($auth_service->get_user_type() == 0) {
    redirect('employeedashboard.php');
  } else if ($auth_service->get_user_type() == 1) {
    redirect('managerdashboard.php');
  } else if ($auth_service->get_user_type() == 2) {
    redirect('admindashboard.php');
  }
  ?>

<?php
}
if ($auth_service->is_logged_in()) {
  display_dashboard($auth_service);
} else {
  $login_success = false;
  if ($_POST["email"] && $_POST["pwd"]) {
    if ($auth_service->login($_POST["email"], $_POST["pwd"])) {
      $login_success = true;
      display_dashboard($auth_service);
    }
  }
  if (!$login_success) {
    page_start($auth_service, "Scheduling OnDemand");
    ?>

    <!-- Top right image on login page (Theme Image)  -->
    <div class="header-background-image1">
      <img src="images/Capture.PNG" class="img-responsive">
    </div>

    <!-- Start of login form -->
    <div class="container">
      <div class="row">
        <div class="col-md-6 center-block" style="float:none">
          <?php if ($logoff) { ?> <h2 class="text-center">--- Logged Off Successfully ---</h2> <?php } ?>
          <h4 class="text-center login-header">Login</h4>
          <?php
          if ($_POST["email"] && $_POST["pwd"]) {
            ?>
            <div class="error-message">Invalid email and password combination. Please try again.</div>
          <?php
          }
          ?>
          <!-- Login form, need to create php code and figure out backend SQL -->
          <form action='index.php' method="POST">

            <div class="form-group">
              <label for="email">Email address:</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="form-group">
              <label for="pwd">Password:</label>
              <input type="password" class="form-control" id="pwd" name="pwd">
            </div>

            <!-- Was thinking of adding this function, we will see how that ends up working out -->
            <!-- <div class="checkbox">
                  <label><input type="checkbox"> Remember me</label>
                </div> -->
            <button type="submit" class="btn btn-default">Login</button>

            <!-- Forgot password hyperlink  -->
            <a href="resetpassword.php">Forgot Password</a>
          </form>
        </div>
      </div>
    </div>

    <!-- Bottom left image on login page (Theme Image)  -->
    <div class="footer-background-image1">
      <img src="images/footer1.PNG" class="img-responsive float-left">
    </div>
    <?php
    page_end();
  }
}
?>