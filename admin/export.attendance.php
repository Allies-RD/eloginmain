<?php
// Database connection
require_once 'auth.php';

if(isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];

    // Get employee data
    $employee_qry = $conn->query("SELECT concat(firstname,' ',middlename,' ',lastname) as name FROM employee WHERE id = $employee_id");
    $employee = $employee_qry->fetch_assoc();

    // Get attendance data for the employee
    $attendance_qry = $conn->query("SELECT a.*, concat(e.firstname, ' ', e.middlename, ' ', e.lastname) as name, e.employee_no 
                                    FROM attendance a 
                                    INNER JOIN employee e ON a.employee_id = e.id 
                                    WHERE a.employee_id = $employee_id") or die(mysqli_error());

    // Generate CSV file
    $filename = "attendance_" . strtolower(str_replace(' ', '_', $employee['name'])) . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Employee Number', 'Name', 'Date', 'Log Type', 'Time'));

    while($row = $attendance_qry->fetch_assoc()) {
        // Determine log type
        if($row['log_type'] == 1) {
            $log = "TIME IN AM";
        } elseif($row['log_type'] == 2) {
            $log = "TIME OUT AM";
        } elseif($row['log_type'] == 3) {
            $log = "TIME IN PM";
        } elseif($row['log_type'] == 4) {
            $log = "TIME OUT PM";
        }

        // Write attendance data to CSV
        fputcsv($output, array(
            $row['employee_no'],
            $row['name'],
            date("F d, Y", strtotime($row['datetime_log'])),
            $log,
            date("h:i a", strtotime($row['datetime_log']))
        ));
    }
    
    fclose($output);
}
exit();
