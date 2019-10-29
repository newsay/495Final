<?php

/**
 * This file provides various helper methods for building a web page.
 * Provided methods are:
 * redirect:    Redirect to another URL. If used, should be the only thing on 
 *              the page.
 * authorize:   Determine whether the currently logged in user has access rights
 *              to the page.
 * page_start:  Create the basics of a page, including stylesheets and a header.
 * page_end:    Meant to be called paired with page_start. Output closing HTML elements.
 * @author Andrew Ritchie
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
/** 
 * Redirect to another URL. If used, should be the only thing on the page.
 * @param $url: The URL to redirect to
 */
function redirect($url)
{
    ?><HTML>

    <HEAD>
        <meta http-equiv="refresh" content="0; url=<?php echo $url ?>">
    </HEAD>

    </HTML><?php
        }

        /**
         * Determine whether the currently logged in user has access rights to the page. 
         * Otherwise redirect to index.php to return to their dashboard or the login page.
         * @param $auth_service: The authentication service to use for determining whether the user is logged in
         * @param $organization_service: The organization service to use for determining whether the organization is enabled
         * @param $expected_user_type: The user type to use. Defaults to "all logged in users"
         * @return Whether the logged in user is authorized for this page
         */
        function authorize($auth_service, $organization_service, $expected_user_type = -1)
        {
            $logged_in = $auth_service->is_logged_in() && $auth_service->is_initialized();
            $enabled = true;
            if ($logged_in) {
                $enabled = $auth_service->get_user_organization() == null || $organization_service->get_organization($auth_service->get_user_organization())->get_is_enabled();
                $user_type = $auth_service->get_user_type();
                if ($enabled && ($user_type == $expected_user_type || $expected_user_type == -1)) {
                    return true;
                }
            }
            if ($enabled) {
                redirect("index.php");
            } else {
                redirect("logoff.php");
            }
            return false;
        }

        /**
         * Create the basics of a page, including stylesheets and a header.
         * @param $auth_service: The authentication service to use for determining the header.
         * @param $page_title: The title of the page in the address bar
         * @param $header_name: Optional string. The name of the current page in the header, such as "Dashboard"
         */
        function page_start($auth_service, $page_title, $header_name = null)
        {
            ?>
    <!DOCTYPE html>
    <html lang="en" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- These next three links are required to use Bootstrap, I have Bootstrap installed locally, but wasn't sure how it would behave once in Cloud9-->
        <!-- may remove after we do some testing -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
        <!-- Link to custom css file for some minor formatting, may remove once I understand bootstrap better. -->
        <link rel="stylesheet" type="text/css" href="index.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

        <!-- Title of page -->
        <title><?php echo htmlentities($page_title); ?></title>
    </head>


    <body>
        <?php draw_nav_bar($auth_service, $header_name); ?>
        <div class="container">
        <?php
        }

        /**
         * Meant to be called paired with page_start. Output closing HTML elements.
         */
        function page_end()
        {


            ?>
        </div>

    </body>

    </html>
<?php
}
?>