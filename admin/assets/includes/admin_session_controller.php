<?php
if (!isset($_SESSION['adminid']) || !isset($_SESSION['log_token'])) {
    header("Location: ".APP_ROOT."adminlogin.php?view=login&_rdir=nolog");
    exit;
}
$adminuname = $_SESSION['adminid'];
$token = $_SESSION['log_token'];
$adminquery = "SELECT * FROM admin WHERE username='$adminuname'";
$admin_result = mysqli_query($hostelcon,$adminquery) or die(unexpectedError());
$admin_count = mysqli_num_rows($admin_result);
if ($admin_count==0) {
    header("Location: ".APP_ROOT."?view=login&_rdir=notfound");
    exit;
}
$admindata = mysqli_fetch_assoc($admin_result);
$adminid = $admindata['adminid'];
$displayName = $admindata['displayName'];
$current_session = $admindata['current_session'];
$current_semester = $admindata['current_semester'];
$admin_email = $admindata['admin_email'];
//load settings
$settings_query = "SELECT current_session, current_semester FROM admin";
$settings_result = mysqli_query($hostelcon, $settings_query) or die(unexpectedError());
$settings_data = mysqli_fetch_assoc($settings_result);
$current_session = $settings_data['current_session'];
$current_semester = $settings_data['current_semester'];
?>