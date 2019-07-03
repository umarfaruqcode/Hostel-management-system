<?php
require_once("functions.php");

//define constant variables
define("APP_ROOT", "http://localhost/hostel-booking/");
define("APP_SCHL_WEBSITE", "http://www.unilorin.edu.ng/");
define("APP_NAME", "University of Ilorin Nigeria");
define("APP_SCHOOL", "Unilorin");
define("APP_FACULTY", "Faculty of Computer and Information Science");
define("APP_ADMIN_DIR", "admin/");
define("APP_STDT_DIR", "student/");


//database variables
define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "hostelbooking");

//STOP EDITING HERE

if (!isset($_SESSION)) {
	session_start();
}

require_once('db_config.php');
?>