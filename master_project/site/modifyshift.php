<?php

/**
 * @author Andrew Ritchie, Robert Martinez, Shoshana Yaswen
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/misc/init.php';
if (authorize($auth_service, $organization_service, User::USER_TYPE_MANAGER)) {
    $shiftid = $_GET["id"];
    $shift = $shift_service->get_shift($shiftid);
    $timestart = date("H:i:s", $shift->get_start_datetime());
    $datestart = date("Y-m-d", $shift->get_start_datetime());
    $timeend = date("H:i:s", $shift->get_end_datetime());
    $dateend = date("Y-m-d", $shift->get_end_datetime());
    if ($auth_service->get_user_organization() != $shift->get_organization_id()) {
        redirect("managerdashboard.php");
    } else {
        page_start($auth_service, "Scheduling OnDemand - Modify Shift");
        ?>
        
        <!--Modify shift form-->
        <div class="container">
            <div class="row">
                <div class="col-md-6 center-block" style="float:none">
                    <h1 class="text-center">Modify Shift</h1>
                </div>
            </div>
            <?php
            $users = $user_service->get_users($auth_service->get_user_organization(), User::USER_TYPE_EMPLOYEE);
            $userList = array(array('', 'Choose...'));
            $selectedUser = '';
            foreach ($users as $user) {
                array_push($userList, array($user->get_user_id(), $user->get_first_name() . " " . $user->get_last_name()));
                if ($user->get_user_id() == $shift->get_assigned_user_id()) {
                    $selectedUser = $user->get_user_id();
                }
            }
            //Display existing form data
            $fields = array(
                array('id' => 'inputStartDate', 'type' => 'date', 'name' => 'startDate', 'display_text' => "Start Date", 'width' => 6, 'default' => $datestart, 'required' => true),
                array('id' => 'inputStartTime', 'type' => 'time', 'name' => 'startTime', 'display_text' => "Start Time", 'width' => 6, 'default' => $timestart, 'required' => true),
                array('id' => 'inputEndDate', 'type' => 'date', 'name' => 'endDate', 'display_text' => "End Date", 'width' => 6, 'default' => $dateend, 'required' => true),
                array('id' => 'inputEndTime', 'type' => 'time', 'name' => 'endTime', 'display_text' => "End Time", 'width' => 6,  'default' => $timeend, 'required' => true),
                array('id' => 'inputRequiredPosition', 'type' => 'text', 'name' => 'requiredPosition', 'placeholder_text' => 'RN', 'display_text' => "Required License", 'width' => 6, 'default' => $shift->get_required_position()),
                array('id' => 'inputZipCode', 'type' => 'text', 'name' => 'zipCode', 'display_text' => "Zip Code", 'placeholder_text' => 90210, 'width' => 6, 'required' => true, 'default' => $shift->get_zip_code()),
                array('id' => 'inputHourlyRate', 'type' => 'number', 'step' => 0.01, 'name' => 'hourlyRate', 'display_text' => "Hourly Rate", 'placeholder_text' => '23.15', 'width' => 6, 'required' => true, 'default' => $shift->get_pay_differential()),
                array('id' => 'inputAssignedEmployee', 'type' => 'select', 'name' => 'assignedEmployee', 'display_text' => "Assigned Employee", 'width' => 6, 'options' => $userList, 'default' => $selectedUser),
                array('id' => 'inputSpecialRequirements', 'type' => 'text', 'name' => 'specialRequirements', 'placeholder_text' => 'Language: Spanish Speaking, Experience: Children under 2 yrs old', 'display_text' => "Special Requirements", 'width' => 12, 'required', 'default' => $shift->get_special_requirements()),
    
            );
            $errors = array();
            //Check to see if the form has posted
            if ($_POST) {
                //Check if required fields have been populated
                $errors = required_fields_errors($fields);
                if (sizeof($errors) == 0) {
                    //Do additional validation
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
                        $updatedshift = new Shift();
                        $updatedshift->set_shift_id($_GET["id"]);
                        $updatedshift->set_assigned_user_id($_POST["assignedEmployee"] == '' ? null : $_POST["assignedEmployee"]);
                        $updatedshift->set_start_datetime($startDateTime);
                        $updatedshift->set_end_datetime($endDateTime);
                        $updatedshift->set_required_position($_POST["requiredPosition"]);
                        $updatedshift->set_zip_code($_POST["zipCode"]);
                        $updatedshift->set_special_requirements($_POST["specialRequirements"]);
                        $updatedshift->set_pay_differential($_POST["hourlyRate"]);
                        $updatedshift->set_organization_id($auth_service->get_user_organization());
                        $updatedshift->set_status($_POST["assignedEmployee"] == '' ? Shift::STATUS_UNASSIGNED : Shift::STATUS_ASSIGNED);
                        $shift_service->modify_shift($updatedshift);
    
                        $shift_changes = array();
                        //Calculate changes to the shift for audit trail updates
    
                        if ($shift->get_assigned_user_id() != $updatedshift->get_assigned_user_id()) {
                            $olduser = $user_service->get_user_by_id($shift->get_assigned_user_id(), $auth_service->get_user_organization());
                            $newuser = $user_service->get_user_by_id($updatedshift->get_assigned_user_id(), $auth_service->get_user_organization());
                            if ($olduser == null) {
                                $olduser = "{none}";
                            } else {
                                $olduser = $olduser->get_first_name() . ' ' . $olduser->get_last_name();
                            }
                            if ($newuser == null) {
                                $newuser = "{none}";
                            } else {
                                $newuser = $newuser->get_first_name() . ' ' . $newuser->get_last_name();
                            }
                            array_push($shift_changes, "Assigned User changed from " . $olduser . ' to ' . $newuser);
                        }
                        if ($shift->get_start_datetime() != $updatedshift->get_start_datetime()) {
                            array_push($shift_changes, "Start Date/Time changed from "
                                . date("Y-m-d H:i:s", $shift->get_start_datetime())
                                . ' to '
                                . date("Y-m-d H:i:s", $updatedshift->get_start_datetime()));
                        }
                        if ($shift->get_end_datetime() != $updatedshift->get_end_datetime()) {
                            array_push($shift_changes, "End Date/Time changed from "
                                . date("Y-m-d H:i:s", $shift->get_end_datetime())
                                . ' to '
                                . date("Y-m-d H:i:s", $updatedshift->get_end_datetime()));
                        }
                        if ($shift->get_required_position() != $updatedshift->get_required_position()) {
                            array_push($shift_changes, "Required Position changed from " . $shift->get_required_position() . " to " . $updatedshift->get_required_position());
                        }
                        if ($shift->get_zip_code() != $updatedshift->get_zip_code()) {
                            array_push($shift_changes, "Zip Code changed from " . $shift->get_zip_code() . " to " . $updatedshift->get_zip_code());
                        }
                        if ($shift->get_special_requirements() != $updatedshift->get_special_requirements()) {
                            array_push($shift_changes, "Special Requirements changed from " . $shift->get_special_requirements() . " to " . $updatedshift->get_special_requirements());
                        }
                        if ($shift->get_pay_differential() != $updatedshift->get_pay_differential()) {
                            array_push($shift_changes, "Hourly Rate changed from " . $shift->get_pay_differential() . " to " . $updatedshift->get_pay_differential());
                        }
    
                        foreach ($shift_changes as $shift_change) {
                            $audit_service->add_audit_log($_GET["id"], new AuditTrail($_GET["id"], time(), $shift_change));
                        }
                        ?>
                        <div class="col-md-8 center-block" style="float:none">
                            <h4 class="text-center login-header">The shift has been updated. <a href="managerdashboard.php">Return to dashboard</a></h4>
    
                        </div>
                    <?php
                    }
                }
            }
            if (!$_POST || sizeof($errors) != 0) {
                //If the form hasn't yet been submitted, display it.
                ?>
                <form method="POST" action="modifyshift.php?id=<?php echo $_GET["id"]; ?>">
                    <?php
                    build_form($fields, $errors);
                    ?>
                    <div class="form-group row">
                        <div align="center" class="col-sm-12">
                            <button type="submit" class="btn btn-default">Confirm Changes</button>
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
}
?>