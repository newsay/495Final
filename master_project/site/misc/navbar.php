<?php

/**
 * This file provides a means to draw the navigation bar at the top. The
 * navigation bar is user type specific.
 * Provided methods:
 * draw_nav_bar:  Draw the navigation bar, with user type-specific links.
 * @author Andrew Ritchie
 */

/**
 * Draw the navigation bar, with user type-specific links.
 * @param $auth_service: IAuthorizationService. Used to determine the user type.
 * @param $active_link: String. Which link is currently active, such as 
 *                      "Manage Shifts" or "Dashboard"
 */
function draw_nav_bar($auth_service, $active_link)
{
    if (!$auth_service->is_logged_in()) {
        return;
    }
    $links = array(array("Dashboard", "index.php"));
    $user_type = $auth_service->get_user_type();
    if ($user_type == 0) {
        //Add employee links
        array_push($links, array("Available Shifts", "availableshifts.php"));
        array_push($links, array("My Shifts", "myshifts.php"));
    } else if ($user_type == 1) {
        array_push($links, array("Manage Shifts", "manageshifts.php"));
        array_push($links, array("Manage Users", "manageusers.php"));
        array_push($links, array("Manage Requests", "managerequests.php"));
    } else if ($user_type == 2) {
        //Add admin links
        array_push($links, array("New Organization", "admincreateorganization.php"));
    }
    array_push($links, array("About", "about.php"));
    if ($user_type != 2) {
        array_push($links, array("Profile", "profile.php"));
    }
    ?>
    <nav class="navbar navbar-inverse navbar-custom">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">Scheduling OnDemand</a>
            </div>
            <ul class="nav navbar-nav">
                <?php
                foreach ($links as $link) {
                    echo '<li ';
                    if ($link[0] == $active_link) {
                        echo 'class="active"';
                    }
                    echo '><a href="' . $link[1] . '">' . $link[0] . '</a></li>';
                }
                ?>
            </ul>


            <ul class="nav navbar-nav navbar-right">
                <li>
                    <span class="navbar-text navbar-right" style='margin-right: 20px;'>
                        Logged in as <?php
                                        if ($user_type != 2) {
                                            echo $auth_service->get_user_full_name();
                                        } else {
                                            echo "Administrator";
                                        }
                                        ?>
                    </span>
                </li>
                <li><a href="logoff.php"><span class="glyphicon glyphicon-log-in"></span> Log Off</a></li>
            </ul>
        </div>
    </nav>
<?php
}

?>