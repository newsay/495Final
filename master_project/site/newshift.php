<?php

/**
 * @author Andrew Ritchie, Robert Martinez
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';
if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {

    page_start($auth_service, "Scheduling OnDemand - New Shift");
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6 center-block" style="float:none">
                <h1 class="text-center">New Shift</h1>
            </div>
        </div>
        <?php
        //The following is an example of how to create a simple form.
        $users = $user_service->get_users($auth_service->get_user_organization(), User::USER_TYPE_EMPLOYEE);
        $userList = array(array('', 'Choose...'));
        foreach ($users as $user) {
            array_push($userList, array($user->get_user_id(), $user->get_first_name() . " " . $user->get_last_name()));
        }
        $fields = array(
            array('id' => 'inputStartDate', 'type' => 'date', 'name' => 'startDate', 'display_text' => "Start Date", 'width' => 6, 'required' => true),
            array('id' => 'inputStartTime', 'type' => 'time', 'name' => 'startTime', 'display_text' => "Start Time", 'width' => 6, 'required' => true),
            array('id' => 'inputEndDate', 'type' => 'date', 'name' => 'endDate', 'display_text' => "End Date", 'width' => 6, 'required' => true),
            array('id' => 'inputEndTime', 'type' => 'time', 'name' => 'endTime', 'display_text' => "End Time", 'width' => 6, 'required' => true),
            array('id' => 'inputRequiredPosition', 'type' => 'text', 'name' => 'requiredPosition', 'placeholder_text' => 'RN', 'display_text' => "Required License", 'width' => 6),
            array('id' => 'inputZipCode', 'type' => 'text', 'name' => 'zipCode', 'display_text' => "Zip Code", 'placeholder_text' => 90210, 'width' => 6, 'required' => true),
            array('id' => 'inputHourlyRate', 'type' => 'number', 'step' => 0.01, 'name' => 'hourlyRate', 'display_text' => "Hourly Rate", 'placeholder_text' => '23.15', 'width' => 6, 'required' => true),
            array('id' => 'inputAssignedEmployee', 'type' => 'select', 'name' => 'assignedEmployee', 'display_text' => "Assigned Employee", 'width' => 6, 'options' => $userList),
            array('id' => 'inputSpecialRequirements', 'type' => 'text', 'name' => 'specialRequirements', 'placeholder_text' => 'Language: Spanish Speaking, Experience: Children under 2 yrs old', 'display_text' => "Special Requirements", 'width' => 12, 'required'),
    
        );
        $errors = array();
        //Check to see if the form has posted
        if ($_POST) {
            //Check if required fields have been populated
            $errors = required_fields_errors($fields);
            if (sizeof($errors) == 0) {
                //Do additional validation
                //$startDateTime = time($_POST["startDate"]);
                $startDateTime = strtotime($_POST["startDate"] . ' ' . $_POST["startTime"]);
                $endDateTime = strtotime($_POST["endDate"] . ' ' . $_POST["endTime"]);
                if ($endDateTime < $startDateTime) {
                    add_error($errors, "startDate", "Start Date/Time must be before End Date/Time");
                    add_error($errors, "startTime", "");
                    add_error($errors, "endDate", "");
                    add_error($errors, "endTime", "");
                }
                if ($_POST["assignedEmployee"] != '') {
                    $found_in_users = false;
                    foreach ($users as $user) {
                        if ($user->get_user_id() == $_POST["assignedEmployee"]) {
                            $found_in_users = true;
                        }
                    }
    
                    if (!$found_in_users) {
                        add_error($errors, "assignedEmployee", "Employee not found in Organization");
                    }
                }
                if (sizeof($errors) == 0) {
                    $shift = new Shift();
                    $shift->set_assigned_user_id($_POST["assignedEmployee"] == '' ? null : $_POST["assignedEmployee"]);
                    $shift->set_start_datetime($startDateTime);
                    $shift->set_end_datetime($endDateTime);
                    $shift->set_required_position($_POST["requiredPosition"]);
                    $shift->set_zip_code($_POST["zipCode"]);
                    $shift->set_special_requirements($_POST["specialRequirements"]);
                    $shift->set_pay_differential($_POST["hourlyRate"]);
                    $shift->set_organization_id($auth_service->get_user_organization());
                    $shift->set_status($_POST["assignedEmployee"] == '' ? Shift::STATUS_UNASSIGNED : Shift::STATUS_ASSIGNED);
                    $shift_service->add_shift($shift);
                    ?>
                    <div class="col-md-8 center-block" style="float:none">
                        <h4 class="text-center login-header">The shift has been created in the system. <a href="managerdashboard.php">Return to dashboard</a></h4>
    
                    </div>
                <?php
                }
            }
        }
        if (!$_POST || sizeof($errors) != 0) {
            //If the form hasn't yet been submitted, display it.
            ?>
            <form method="POST" action="newshift.php">
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