<?php
include 'db_connect.php';

// Retrieve POST data
extract($_POST);
$data = array();

// Check if the employee exists
$qry = $conn->query("SELECT * FROM employee WHERE employee_no ='$eno'");
if ($qry->num_rows > 0) {
    $emp = $qry->fetch_array();
    
    // Determine the log type based on the button clicked
    switch ($type) {
        case 1:
            $log = 'time in';
            break;
        case 2:
            $log = 'time out';
            break;
        default:
            $data['status'] = 2; // Invalid log type
            $data['msg'] = 'Invalid log type';
            echo json_encode($data);
            $conn->close();
            exit();
    }
    
    // Insert the log into the database
    $save_log = $conn->query("INSERT INTO attendance (log_type, employee_id) VALUES ('$type', '".$emp['id']."')");
    
    if ($save_log) {
        $employee = ucwords($emp['firstname'].' '.$emp['lastname']); // Correct field for last name
        $data['status'] = 1; // Success
        $data['msg'] = $employee . ', your ' . $log . ' has been recorded. <br/>';
    } else {
        $data['status'] = 2; // Database operation failure
        $data['msg'] = 'Failed to record your log. Please try again.';
    }
} else {
    $data['status'] = 2; // Employee not found
    $data['msg'] = 'Unknown Employee Number';
}

echo json_encode($data);
$conn->close();
?>
