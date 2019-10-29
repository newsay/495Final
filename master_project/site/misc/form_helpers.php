<?php

/**
 * This file contains helper methods that can be used to build a form quickly.
 * 
 * Provided methods are:
 * required_fields_errors: Given a list of fields, return an array of errors for
 *                         fields that are required. This array can be provided
 *                         to build_form.
 * add_error:              Add a custom error an a pre-existing array of form 
 *                         errors.
 * field_label:            Do not call this directly; instead, call build_form.
 *                         Creates a label for a field on a form.
 * build_form:             Display a form on the page with the given fields.
 *                         See documentation of method for details.
 * get_states:             Get a list of states to be used as options for a 
 *                         "select" dropdown.
 * get_security_questions: Get a list of security questions to be used as 
 *                         options for a "select" dropdown.
 * @author Andrew Ritchie
 */


/**
 * Given a list of fields, return an array of errors for
 * fields that are required. This array can be provided
 * to build_form.
 * @param $required_fields: A list of fields that are required.
 * @return A list of fields that have errors
 */
function required_fields_errors($fields)
{
    $res = array();
    foreach ($fields as $field) {
        if (array_key_exists('required', $field) && !$_POST[$field['name']]) {
            $res[$field['name']] = $field['display_text'] . ' is required.';
        }
    }
    return $res;
}

function add_error(&$errors, $field_name, $error_text)
{
    $errors[$field_name] = $error_text;
}
/**
 * Given a list of errors, a list of required fields, the form name of a field, 
 * the HTML ID of a field, and the display text of the field, output the label 
 * of the field. Required fields will show * next to them, and will display
 * in red if entered but not filled in.
 * @param $errors: A list of all errors, built with required_fields_errors
 * @param $field: The field element, as passed into build_form
 * @param $field_name: The form name of the field
 * @param $field_id: The HTML ID of the field
 * @param $field_text: The display name of the field
 */
function field_label($errors, $field, $field_name, $field_id, $field_text)
{
    echo '<label for="' . htmlentities($field_id)  . '"';
    if ($errors != null && array_key_exists($field_name, $errors)) {
        echo " class='error-message'";
    }
    echo '>' . htmlentities($field_text);
    if (array_key_exists('required', $field)) {
        echo "<span class='error-message'> *</span>";
    }
    echo '</label>';
    if (array_key_exists($field_name, $errors)) {
        echo "<span class='error-message-text pull-right'>" . htmlentities($errors[$field_name]) . '</span>';
    }
}


/**
 * Given an array of fields and an array of errors, build a form.
 * @param $fields: An array of Field objects. Each object is expected to be an array with at least some of these values: 
 *                  id: HTML ID of the field
 *                  name: Form name of the field
 *                  display_text: Display text of the field
 *                  required: Defaults to false.
 *                  placeholder_text: Placeholder text of the field
 *                  type: Possible values are text, select, checkbox, or any other HTML form . Defaults to text.
 *                  width: Defaults to 6
 *                  options: If type is select, options is the list of options as an array of two-element arrays. 
 *                           The first element of the array is the value, the second is the text.
 *                  default: A default value
 * @param $errors: A list of errors of required fields built with required_field_errors and add_error
 */
function build_form($fields, $errors)
{
    $current_col = 12;
    $first_row = true;
    $required_fields = array();
    foreach ($fields as $field) {
        $type = null;
        if (array_key_exists('type', $field)) {
            $type = $field['type'];
        } else {
            $type = 'text';
        }
        if ($type != 'hidden') {
            $width = array_key_exists('width', $field) ? $field['width'] : 6;
            $new_row = false;
            if ($current_col + $width > 12) {
                $new_row = true;
                if ($first_row) {
                    $first_row = false;
                } else {
                    ?>
                    </div>
                <?php
                }
                ?>
                <div class="form-row">
                <?php
                }
                echo '<div class="form-group col-md-' . htmlentities($width) . '">';
                if ($type != "checkbox") {
                    field_label($errors, $field, $field['name'], $field['id'], $field['display_text']);
                }
                if ($type == 'select') {
                    $selected = null;
                    echo '<select class="form-control" id="' . htmlentities($field['id']) . '" name="' . htmlentities($field['name']) . '">';
                    if (array_key_exists($field['name'], $_POST)) {
                        $selected = $_POST[$field['name']];
                    } else if (array_key_exists('default', $field)) {
                        $selected = $field['default'];
                    }
                    foreach ($field['options'] as $option) {
                        echo '<option value="' . htmlentities($option[0]) . '"';
                        if ($selected == $option[0]) {
                            echo ' selected';
                        }
                        echo '>' . htmlentities($option[1]) . '</option>';
                    }
                    echo '</select>';
                } else if ($type == 'checkbox') {
                    echo '<div class="checkbox"><label><input type="' . htmlentities($type) . '" class="form-control" id="' . htmlentities($field['id']) . '" name="' . htmlentities($field['name']) . '"';
                    if (array_key_exists($field['name'], $_POST) && $_POST[$field['name']]) {
                        echo ' checked';
                    } else if (array_key_exists('default', $field) && $field['default']) {
                        echo ' checked';
                    }
                    echo '>' . $field['display_text'] . "</label></div>";
                } else {
                    echo '<input type="' . htmlentities($type) . '" class="form-control" id="' . htmlentities($field['id']) . '" name="' . htmlentities($field['name']) . '"';
                    if (array_key_exists('placeholder', $field)) {
                        echo ' placeholder="' . htmlentities($field['placeholder']) . '"';
                    }
                    if (array_key_exists('required', $field)) {
                        echo ' required';
                    }
                    if (array_key_exists('step', $field)) {
                        echo ' step="' . htmlentities($field['step']) . '"';
                    }
                    if (array_key_exists('default', $field) && !$_POST) {
                        echo ' value="' . htmlentities($field['default']) . '"';
                    }
                    if ($_POST) {
                        echo ' value="' . htmlentities($_POST[$field['name']]) . '"';
                    }
                    echo '>';
                }
                echo "</div>";
                if ($new_row) {

                    $current_col = 0;
                }
                $current_col += $width;
            } else {
                echo "<input type='hidden' name='" . htmlentities($field['name']) . "' value='" . htmlentities($field['default']) . "'>";
            }
        }
    }

    /**
     * Return a list of states as an array of two-element arrays of abbreviations and names
     * i.e. (('AL', 'Alabama'), ('AK', 'Alaska'), ... )
     * @param $include_placeholder: Set to true if the text 'Choose...' should be at the top.
     * @return An array of two-element arrays of abbreviations and names
     */
    function get_states($include_placeholder)
    {
        $res = array();
        if ($include_placeholder) {
            array_push($res, array('', 'Choose...'));
        }
        array_push(
            $res,
            array("AL", "Alabama"),
            array("AK", "Alaska"),
            array("AZ", "Arizona"),
            array("AR", "Arkansas"),
            array("CA", "California"),
            array("CO", "Colorado"),
            array("CT", "Connecticut"),
            array("DE", "Delaware"),
            array("DC", "District Of Columbia"),
            array("FL", "Florida"),
            array("GA", "Georgia"),
            array("HI", "Hawaii"),
            array("ID", "Idaho"),
            array("IL", "Illinois"),
            array("IN", "Indiana"),
            array("IA", "Iowa"),
            array("KS", "Kansas"),
            array("KY", "Kentucky"),
            array("LA", "Louisiana"),
            array("ME", "Maine"),
            array("MD", "Maryland"),
            array("MA", "Massachusetts"),
            array("MI", "Michigan"),
            array("MN", "Minnesota"),
            array("MS", "Mississippi"),
            array("MO", "Missouri"),
            array("MT", "Montana"),
            array("NE", "Nebraska"),
            array("NV", "Nevada"),
            array("NH", "New Hampshire"),
            array("NJ", "New Jersey"),
            array("NM", "New Mexico"),
            array("NY", "New York"),
            array("NC", "North Carolina"),
            array("ND", "North Dakota"),
            array("OH", "Ohio"),
            array("OK", "Oklahoma"),
            array("OR", "Oregon"),
            array("PA", "Pennsylvania"),
            array("RI", "Rhode Island"),
            array("SC", "South Carolina"),
            array("SD", "South Dakota"),
            array("TN", "Tennessee"),
            array("TX", "Texas"),
            array("UT", "Utah"),
            array("VT", "Vermont"),
            array("VA", "Virginia"),
            array("WA", "Washington"),
            array("WV", "West Virginia"),
            array("WI", "Wisconsin"),
            array("WY", "Wyoming")
        );
        return $res;
    }
    /**
     * Return a list of security questions as an array of two-element arrays of ids and names
     * i.e. ((1, "What is your mother's maiden name?"), (2, "Where did you go to high school?), ... )
     * @param $include_placeholder: Set to true if the text 'Choose...' should be at the top.
     * @return An array of two-element arrays of abbreviations and names
     */
    function get_security_questions($include_placeholder = false)
    {
        $res = array();
        if ($include_placeholder) {
            array_push($res, array('', 'Choose...'));
        }
        $res[1] = array(1, "What is your mother's maiden name?");
        $res[2] = array(2, "Where did you go to high school?");
        $res[3] = array(3, "What is your favorite color?");
        return $res;
    }

    /**
     * Return a list of user types
     * Contains manager and employee
     * @param $include_placeholder: Set to true if the text 'Choose...' should be at the top
     * @return An array of two-element arrays of abbreviations and names
     */
    function get_user_types($include_placeholder = false)
    {
        $res = array();
        if ($include_placeholder) {
            array_push($res, array('', 'Choose...'));
        }
        include_once $_SERVER['DOCUMENT_ROOT'] . "/models/user.php";
        $res[1] = array(User::USER_TYPE_EMPLOYEE, "Employee");
        $res[2] = array(User::USER_TYPE_MANAGER, "Manager");
        return $res;
    }
    ?>