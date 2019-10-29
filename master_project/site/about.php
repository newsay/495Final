<?php

/**
 * @author Andrew Ritchie, John Leavitt, Robert Martinez
 */

include_once  $_SERVER['DOCUMENT_ROOT'] . "/misc/init.php";

/* The if statement below allows all users that are currently logged in to view this page*/
if (authorize($auth_service, $organization_service)) {
    page_start($auth_service, "Scheduling OnDemand", "About");
    ?>

    <div class="container" align="center" style="margin-top:100px;">
        <h1>About Scheduling OnDemand</h1>
    </div>
    <div class="container">
        <h4 class="paragraphspacing" style="margin-top:25px; margin-bottom:25px; margin-left:50px; margin-right:50px; text-indent: 40px;">
            Scheduling OnDemand is a scheduling web application that allows users to view and self-assignhifts that are currently available
            to be covered. Management can create, modify, and delete shifts as business demand increases or decreases to
            maximize business profit by ensuring all fillable schedules are covered. Scheduling OnDemand is unique in
            that it’s an efficient and low overhead solution to publishing schedules that are available to be covered.
            With some employees only working on an as needed basis it can be very costly to purchase and provide business
            services like Office 365 in order to create a shared schedule. Scheduling OnDemand instead provides a low-cost
            solution by allowing management to create users that can access specific schedules and see what shifts are
            available and can be covered.</h4>
    </div>



    <div class="container" align="center" style="margin-top:100px;">
        <h1>Technical Support</h1>
    </div>
    <div class="container">
        <h4 class="paragraphspacing" style="margin-top:25px; margin-bottom:25px; margin-left:50px; margin-right:50px; text-indent: 40px;">
            Email Customer Support:
            Email us at <u><b>support@schedulingondemand.com</b></u> if you have any questions. Please be advised all help desk tickets submitted to <u><b>support@schedulingondemand.com</b></u> will be answered in the order they are received <u><b>Monday – Friday from 8AM – 5PM</b></u>.
            <br><br>Contact Customer Support:
            For assistance please contact us at <b><u>1-800-000-0000</u></b>. Please be advised that all calls to Scheduling OnDemand at <b><u>1-800-000-0000</u></b> will be answered <b><u>24/7</u></b> in the order they are received.
        </h4>
    </div>



    <?php
    page_end();
}
?>