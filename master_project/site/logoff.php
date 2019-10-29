<?php

/**
 * @author Andrew Ritchie
 */
include_once $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";
if (!$auth_service->is_logged_in()) {
    ?>
    <HTML>

    <HEAD>
        <meta http-equiv="refresh" content="0; url=index.php">
        <!--Auto redirect to index-->
    </HEAD>

    </HTML>
<?php
} else {
    $auth_service->logoff();
    $logoff = true;
    include $_SERVER['DOCUMENT_ROOT'] . '/index.php';
}
?>