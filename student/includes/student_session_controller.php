<?php
if (!isset($_SESSION['stdtid']) || !isset($_SESSION['log_token'])) {
    header("Location: ".APP_ROOT."?view=login&_rdir=nolog");
    exit;
}
$studentid = $_SESSION['stdtid'];
$token = $_SESSION['log_token'];
$userquery = "SELECT * FROM students INNER JOIN departments ON students.stdt_deptid=departments.deptid WHERE matric_no='$studentid'";
$user_result = mysqli_query($hostelcon,$userquery) or die(unexpectedError());
$user_count = mysqli_num_rows($user_result);
if ($user_count==0) {
    header("Location: ".APP_ROOT."?view=login&_rdir=notfound");
    exit;
}
$userdata = mysqli_fetch_assoc($user_result);
$student_numeric_id = $userdata['stdtid'];
$level = $userdata['level'];
$surname = $userdata['surname'];
$othernames = $userdata['other_names'];
$gender = $userdata['gender'];
$email = $userdata['email'];
$phone = $userdata['phone'];
$departmentid = $userdata['deptid'];
$department = $userdata['dept_name'];
//load settings
$settings_query = "SELECT current_session, current_semester FROM admin";
$settings_result = mysqli_query($hostelcon, $settings_query) or die(unexpectedError());
$settings_data = mysqli_fetch_assoc($settings_result);
$current_session = $settings_data['current_session'];
$current_semester = $settings_data['current_semester'];
?>