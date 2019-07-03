<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "timetable";

if (isset($_POST['addItem']) && $_POST['addItem']=="YES") {
  $departmnt = $_POST['department'];
  $semestr = $_POST['semester'];
  $course = $_POST['course'];
  $levl = $_POST['level'];
  $sessn = $_POST['session'];
  $lecturer = $_POST['lecturer'];
  $start = $_POST['starttime'];
  $end = $_POST['endtime'];
  $lecture_day = $_POST['lecture_day'];
  $hall = $_POST['hall'];
  $classtype = $_POST['classtype'];
  $err = 0;//initial
  $errmsg = array();//initial

  //check if there will be class
  //one lecturer having a class when already in a class
  $lecturerCheck = mysqli_query($hostelcon,"SELECT * FROM timetable WHERE lecturer_in_charge_id='$lecturer' AND lecture_day='$lecture_day' AND semester='$semestr' AND session='$sessn' AND ((start_time='$start' AND end_time='$end') OR (start_time<='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time>'$start' AND start_time<'$end'))) OR (start_time>='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time<='$end' AND start_time<='$end'))))") or die(unexpectedError().mysqli_error($hostelcon));
  $lecturerCheckCount = mysqli_num_rows($lecturerCheck);
  if ($lecturerCheckCount > 0) {//if clash found
    $err = 1;
    $errmsg[] = "The lecturer already has a lecture within the time duration!";
  }

  //Check if lecture hall is in use
  $hallCheck = mysqli_query($hostelcon,"SELECT * FROM timetable WHERE lecture_hall_id='$hall' AND lecture_day='$lecture_day' AND semester='$semestr' AND session='$sessn' AND ((start_time='$start' AND end_time='$end')
    OR (start_time<='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time>'$start' AND start_time<'$end'))) OR (start_time>='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time<='$end' AND start_time<='$end'))))") or die(unexpectedError().mysqli_error($hostelcon));
  $hallCheckCount = mysqli_num_rows($hallCheck);
  if ($hallCheckCount > 0) {//if clash found
    $err = 1;
    $errmsg[] = "The lecture hall already has a lecture within the time duration!";
  }

  //Check if a level of students in a department already have lecture
  $deptCheck = mysqli_query($hostelcon,"SELECT * FROM timetable WHERE deptid='$departmnt' AND level='$levl' AND semester='$semestr' AND session='$sessn' AND lecture_day='$lecture_day' AND class_type='$classtype'
    AND ((start_time='$start' AND end_time='$end') OR (start_time<='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time>'$start' AND start_time<'$end'))) OR (start_time>='$start' AND ((end_time>='$end' AND start_time<='$end') OR (end_time<='$end' AND start_time<='$end'))))") or die(unexpectedError().mysqli_error($hostelcon));
  $deptCheckCount = mysqli_num_rows($deptCheck);
  if ($deptCheckCount > 0) {//if clash found
    $err = 1;
    $errmsg[] = "The selected class of students already have a lecture within the time duration!";
  }

  //Check if the course is a one credit unit course so can exist once
  $courseStatusCheck = mysqli_query($hostelcon,"SELECT * FROM timetable INNER JOIN courses ON timetable.courseid=courses.cid WHERE courseid='$course' AND credit_unit='1' AND deptid='$departmnt' AND level='$levl' AND session='$sessn' AND semester='$semestr' AND class_type='$classtype'") or die(unexpectedError().mysqli_error($hostelcon));
  $courseStatusCheckCount = mysqli_num_rows($courseStatusCheck);
  if ($courseStatusCheckCount > 0) {//if clash found
    $err = 1;
    $errmsg[] = "You have already added the 1-unit course to the timetable for the same department and level. A 1-unit course can only be done once in a week.";
  }
  
  if($err==0){
      //insert data into the db
      $insertQuery = "INSERT INTO timetable (`deptid`, `courseid`, `level`, `lecture_day`, `start_time`, `end_time`, `session`, `semester`, `lecture_hall_id`, `lecturer_in_charge_id`, `class_type`) VALUES('$departmnt','$course','$levl','$lecture_day','$start','$end','$sessn','$semestr','$hall','$lecturer','$classtype')";
      $result = mysqli_query($hostelcon,$insertQuery) or die(unexpectedError());
      header("Location: timetable.php?pgid=add&_rdir=success");
      exit();
  }
}

if (isset($_GET['action']) && isset($_GET['ttid']) && $_GET['action']=="delete" && !empty($_GET['ttid'])) {
  $ttid = $_GET['ttid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM timetable WHERE ttid='$ttid'") or die(unexpectedError());
}

$query_suffix = "";
if (isset($_GET['view']) && isset($_GET['deptid']) && isset($_GET['level'])) {
  $departmentid = $_GET['deptid'];
  $department_q = mysqli_query($hostelcon,"SELECT dept_name FROM departments WHERE deptid='$departmentid'");
  $department_row = mysqli_fetch_assoc($department_q);
  $department = $department_row['dept_name'];
  $level = $_GET['level'];
  if ($level=="all") {
    $query_suffix = "AND timetable.deptid='".$departmentid."'";
  }
  else{
    $query_suffix = "AND level='".$level."' AND timetable.deptid='".$departmentid."'";
  }
}
else{
  $department = "All Departments";
  $level = "All";
}
$tt_query = "SELECT * FROM timetable WHERE (session='$current_session' AND semester='$current_semester' $query_suffix) GROUP BY lecture_day ORDER BY lecture_day ASC";// GROUP BY lecture_day
$tt_result = mysqli_query($hostelcon,$tt_query) or die(mysqli_error($hostelcon));// die(unexpectedError());
$tt_count = mysqli_num_rows($tt_result);
$tt_data = mysqli_fetch_assoc($tt_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Timetable - <?= APP_NAME ?></title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
     <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
        <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
     <!-- GOOGLE FONTS-->
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
   <style type="text/css">
    .list {
      width: 99%;
      margin: auto;
    }
    .list tr th, .list tr td {
        padding: 5px;
        border: solid 1px #999;
     }
     .list tr th {
      text-align: center;
     }
   </style>
   <script type="text/javascript">
     function confirmAction() {
       var x = confirm("Are you sure you want to proceed with the action?");
       if (x==true) {
        return true;
       }
       else{
        return false;
       }
     }
   </script>
</head>
<body>
    <div id="wrapper">
        <?php include_once("assets/includes/header.php"); ?>
        <?php include_once("assets/includes/side-nav.php"); ?>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-lg-12">
                     <h2 style="text-transform: uppercase;">Time Table</h2>   
                    </div>
                </div> 
                <div class="alert alert-info" style="margin-bottom: 10px;">
                     <strong>View current time table; add new items to the timetable.</strong>
                </div>
                <div class="col-lg-12" style="background: #f1f1f1; padding: 5px 10px; margin-bottom: 20px; border-bottom: solid 1px #d1d1d1;"><a href="?pgid=view">View Timetable</a> | <a href="?pgid=add">Add to time table</a></div>
                 <!-- /. ROW  -->
                  <hr />
                <?php
                  if ((isset($err) && $err == 1) && isset($errmsg)) {
                      echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                      foreach ($errmsg as $msg) {
                          echo $msg.'<br>';
                      }
                      echo '</div>';
                  }
                  if (isset($_GET['pgid']) && $_GET['pgid']=='add') {
                    if(isset($_GET['_rdir']) && $_GET['_rdir']=="success"){
                ?>
                      <div class="alert alert-success text-align" style="margin-bottom: 10px;">Item successfully added to time table!</div>
                <?php
                }
                ?>
                <div class="row">
                  <h3 style="margin-left: 30px;">Add to Time Table</h3>
                    <div class="col-lg-12 ">
                       <div class="text-center">
                         <form action="" method="POST">
                         <div class="col-lg-6 col-md-6">
                            <div class="input-group">
                              <span class="input-group-addon">Department</span>
                             <select name="department" class="form-control" required="required" style="padding:8px;">
                                <option disabled="disabled" selected="selected">Choose Department</option>
                                <?php
                                  $query = "SELECT * FROM departments";
                                  $result = mysqli_query($hostelcon,$query) or die("An unexpected error occured. Please contact the administrator if it persists.");
                                  while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                    <option value="<?= $row['deptid'] ?>"><?= $row['dept_name'] ?> (<?= $row['dept_acron'] ?>)</option>
                                <?php
                                  }
                                ?>
                              </select>
                            </div>
                            <br>
                            <div class="input-group">
                              <span class="input-group-addon">Course</span>
                              <select name="course" class="form-control" required="required" style="padding:8px;">
                                <option disabled="disabled" selected="selected">Choose Course</option>
                                <?php
                                  $cquery = "SELECT * FROM courses ORDER BY department_id";
                                  $cresult = mysqli_query($hostelcon,$cquery) or die("An unexpected error occured. Please contact the administrator if it persists.");
                                  while ($row = mysqli_fetch_assoc($cresult)) {
                                ?>
                                    <option value="<?= $row['cid'] ?>"><?= $row['course_code'] ?> - <?= $row['course_title'] ?></option>
                                <?php
                                  }
                                ?>
                              </select>
                            </div>
                            <br>
                            <div class="input-group">
                              <span class="input-group-addon">Level</span>
                                <select name="level" class="form-control" required="required" style="padding:8px;">
                                  <option disabled="disabled" selected="selected">Choose Level</option>
                                  <option value="100">100</option>
                                  <option value="200">200</option>
                                  <option value="300">300</option>
                                  <option value="400">400</option>
                                </select>
                            </div>
                            <br>
                            <div class="input-group">
                              <span class="input-group-addon">Session</span>
                              <select name="session" class="form-control" required="required" style="padding:8px;">
                                <option selected="selected" value="<?= $current_session; ?>"><?= $current_session; ?></option>
                                <option value="<?= date("Y")-1 ?>/<?= date("Y") ?>"><?= date("Y")-1 ?>/<?= date("Y") ?></option>
                                <option value="<?= date("Y") ?>/<?= date("Y")+1 ?>"><?= date("Y") ?>/<?= date("Y")+1 ?></option>
                              </select>
                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon">Semester</span>
                                <select name="semester" required="required" class="form-control" style="padding:8px;">
                                  <option disabled="disabled">Select Semester</option>
                                  <option value="1" <?php if($current_semester==1){ echo "selected"; } ?>>Harmatan</option>
                                  <option value="2" <?php if($current_semester==2){ echo "selected"; } ?>>Rain</option>
                                </select>
                              </div>
                              <br>
                            <div class="input-group">
                                <span class="input-group-addon">Lecturer</span>
                                <select name="lecturer" class="form-control" required="required" style="padding:8px;">
                                  <option disabled="disabled" selected="selected">Choose Lecturer</option>
                                  <?php
                                    $lquery = "SELECT * FROM lecturers INNER JOIN departments ON lecturers.deptid=departments.deptid ORDER BY lecturer_name";
                                    $lresult = mysqli_query($hostelcon,$lquery) or die("An unexpected error occured. Please contact the administrator if it persists.");
                                    while ($row = mysqli_fetch_assoc($lresult)) {
                                  ?>
                                      <option value="<?= $row['lid'] ?>"><?= $row['lecturer_name'] ?> <?php if($row['lecturer_name']!=="Other"){ echo "from " . $row['dept_name']; } ?></option>
                                  <?php
                                    }
                                  ?>
                                </select>
                              </div>
                              <br>
                          </div>
                          <div class="col-lg-6 col-md-6">
                            <div class="input-group">
                              <span class="input-group-addon">Class Type</span>
                              <select name="classtype" class="form-control" style="padding:8px;" required="">
                                <!-- <option disabled="disabled" selected="selected">Choose Type</option> -->
                                <option selected="selected" value="Regular">Regular</option>
                                <option value="Supplementary">Supplementary</option>
                                <option value="Tutorial">Tutorial</option>
                              </select>
                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon">Lecture Hall</span>
                                <select name="hall" required="required" class="form-control" style="padding:8px;">
                                <option selected="selected" disabled="disabled">Choose Hall</option>
                                  <?php
                                    $hquery = "SELECT * FROM lecture_halls ORDER BY hall_name ASC";
                                    $hresult = mysqli_query($hostelcon,$hquery) or die("An unexpected error occured. Please contact the administrator if it persists.");
                                    while ($hrow = mysqli_fetch_assoc($hresult)) {
                                  ?>
                                      <option value="<?= $hrow['lhid'] ?>"><?= $hrow['hall_name'] ?></option>
                                  <?php
                                    }
                                  ?>
                                </select>
                              </div>
                              <br>
                            <div class="input-group">
                                <span class="input-group-addon">Lecture Day</span>
                                <select name="lecture_day" class="form-control" required="required" style="padding:8px;">
                                  <option disabled="disabled" selected="selected">Choose Day</option>
                                  <option value="1">Monday</option>
                                  <option value="2">Tuesday</option>
                                  <option value="3">Wednesday</option>
                                  <option value="4">Thursday</option>
                                  <option value="5">Friday</option>
                                  <option value="6">Saturday</option>
                                  <option value="7">Sunday</option>
                                </select>
                              </div>
                              <br>
                              <div class="input-group">
                                <span class="input-group-addon">Start Time</span>
                                <select name="starttime" class="form-control" required="required" style="padding:8px;">
                                  <option disabled="disabled" selected="selected">HH:mm</option>
                                  <?php showTimes(5); ?>
                                </select>
                              </div>
                              <br>
                              <div class="input-group">
                                <span class="input-group-addon">End Time</span>
                                <select name="endtime" class="form-control" required="required" style="padding:8px;">
                                  <option disabled="disabled" selected="selected">HH:mm</option>
                                  <?php showTimes(5); ?>
                                </select>
                              </div>
                              <br>
                          </div>
                            <input type="hidden" name="addItem" value="YES" />
                           <button type="submit" class="btn btn-primary">Save Timetable</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <?php
                  } elseif (!isset($_GET['pgid']) || (isset($_GET['pgid']) && $_GET['pgid']=='view' || ($_GET['pgid']=='view' && isset($_GET['deptid'])))) {
                  ?>
                  <div style="background: #f1f1f1;padding: 5px 10px;">
                    <form method="GET" action="">
                      <select name="deptid" required="required" style="padding:5px;">
                        <option selected="selected" disabled="disabled">Choose Department</option>
                        <?php
                          $query = "SELECT * FROM departments";
                          $result = mysqli_query($hostelcon,$query) or die("An unexpected error occured. Please contact the administrator if it persists.");
                          while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <option value="<?= $row['deptid'] ?>"><?= $row['dept_name'] ?> (<?= $row['dept_acron'] ?>)</option>
                        <?php
                          }
                        ?>
                      </select>
                      <select name="level" style="padding:5px;" required="required">
                        <option selected="selected" disabled="disabled">Choose Level</option>
                        <option value="all">All Levels</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                        <option value="300">300</option>
                        <option value="400">400</option>
                      </select>
                      <input type="hidden" name="view" value="view" />
                      <button class="btn btn-primary">View Timetable</button>
                    </form>
                  </div>
                  <br>
                  <div id="services-section" style="overflow: hidden; padding: 10px 0px;" class="btn-danger">
                      <div class="col-md-3 col-sm-6 color-green">
                          <strong>DEPARTMENT</strong><br>
                          <?= check_dataset($department); ?>
                      </div>
                      <div class="col-md-3 col-sm-6 color-pinterest">
                          <strong>LEVEL</strong><br>
                          <?= ucfirst(check_dataset($level)); ?>
                      </div>
                      <div class="col-md-3 col-sm-6 color-linkedin">
                          <strong>SESSION</strong><br>
                          <?= check_dataset($current_session); ?>
                      </div>
                      <div class="col-md-3 col-sm-6 color-red">
                          <strong>SEMESTER</strong><br>
                          <?= ucfirst(semesterInterpreter($current_semester)); ?>
                      </div>
                  </div>
                  <div class="pad-top" style="height: 500px; overflow: auto;">
                      <?php
                      if($tt_count > 0){//show if there is at least one row
                          $i = 1;
                      do{
                      ?>
                      <!-- ./ Heading div-->
                      <?php $lecture_day = $tt_data['lecture_day']; ?>
                      <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1 margin-top-100">
                              <div class="text-center">
                                  <h3><i class="fa fa-calendar small-icons bk-color-brown"></i><?= dayInterpreter($lecture_day); ?></h3>
                              </div>
                              <hr />
                      </div>
                      <?php
                      $day_query = mysqli_query($hostelcon,"SELECT * FROM timetable INNER JOIN departments ON timetable.deptid=departments.deptid WHERE lecture_day='$lecture_day' GROUP BY timetable.deptid");
                      $day_row = mysqli_fetch_assoc($day_query);

                      do{
                        $department_name = $day_row['dept_name'];
                      ?>
                          <div class="col-md-12 col-md-offset-0 col-sm-12 col-sm-offset-0 margin-top-80">
                            <h4><?= $department_name; ?></h4>
                            <?php
                              $dept_query = mysqli_query($hostelcon,"SELECT * FROM timetable INNER JOIN lecture_halls ON timetable.lecture_hall_id=lecture_halls.lhid INNER JOIN lecturers ON timetable.lecturer_in_charge_id=lecturers.lid INNER JOIN courses ON timetable.courseid=courses.cid INNER JOIN departments ON timetable.deptid=departments.deptid  WHERE (lecture_day='$lecture_day' $query_suffix) ORDER BY level, start_time") or die(unexpectedError());
                              //$dept_row = mysqli_fetch_assoc($day_query);
                            ?>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Level</th>
                                        <th>Course</th>
                                        <th>Venue</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Lecturer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                  while($dept_row = mysqli_fetch_assoc($dept_query)){
                                ?>
                                    <tr>
                                        <td><?= $dept_row['level']; ?>L</td>
                                        <td><?= $dept_row['course_code']; ?> - <?= $dept_row['course_title']; ?></td>
                                        <td><?= $dept_row['hall_name']; ?></td>
                                        <td><?= $dept_row['start_time']; ?></td>
                                        <td><?= $dept_row['end_time']; ?></td>
                                        <td><?= $dept_row['lecturer_name']; ?></td>
                                        <td><a href="?action=delete&ttid=<?= $dept_row['ttid']; ?>" onClick="return confirmAction()">Delete</a></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                          </div>
                      <?php
                      }while($day_row = mysqli_fetch_assoc($day_query));
                      ?>
                      <!-- ./ Content div-->
                      <?php
                          $i++;
                      } while ($tt_data = mysqli_fetch_assoc($tt_result));
                  }else{
                  ?>
                      <div class="col-md-10 col-md-offset-1 col-sm-10 col-sm-offset-1">
                              <div class="text-center">
                                  <h4 class="color-red">No timetable found!</h4>
                              </div>
                              <hr />
                      </div>
                  <?php
                  }
                  ?>
                  </div>
                <?php
                }
                ?>
          </div>
             <!-- /. PAGE INNER  -->
        </div>
         <!-- /. PAGE WRAPPER  -->
      </div>
      <?php include_once("assets/includes/footer.php"); ?>

     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
      <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
      <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>