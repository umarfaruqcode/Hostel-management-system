<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "courses";

//process delete
if (isset($_GET['action']) && isset($_GET['cid']) && $_GET['action']=="delete" && !empty($_GET['cid'])) {
  $cid = $_GET['cid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM courses WHERE cid='$cid'") or die(unexpectedError()."<p>Possible error may be that the course you are trying to delete is already associated with an item in the time table, which is preventing you from deleting it.</p>");
  header("Location: courses.php?act=del&r=1");
  exit();
}
//add new course
if (isset($_POST['ADD']) && $_POST['ADD']=="YES") {
  $coursecode = $_POST['code'];
  $coursetitle = $_POST['title'];
  $department = $_POST['department'];
  $course_unit = $_POST['unit'];
  $err = 0;
  $errmsg = "";
  //check if course code already exists in the database
  $checkQuery = mysqli_query($hostelcon,"SELECT * FROM courses WHERE course_code='$coursecode' OR course_title='$coursetitle'");
  $checkCount = mysqli_num_rows($checkQuery);
  if ($checkCount > 0) {
    $err = 1;
    $errmsg = "A course with the Course Code or Title already exists. Please check and try again.";
  }
  if ($err==0) {//if no error, process the submission
    $insertCourse = mysqli_query($hostelcon,"INSERT INTO courses (course_code,course_title,credit_unit,department_id) VALUES('$coursecode','$coursetitle',$course_unit,'$department')") or die(unexpectedError());
    header("Location: courses.php?_rdir=1");
    exit();
  }
}

//load courses
$course_query = "SELECT * FROM courses INNER JOIN departments ON courses.department_id=departments.deptid ORDER BY department_id ASC, course_code ASC";
$course_result = mysqli_query($hostelcon, $course_query);
$course_count = mysqli_num_rows($course_result);
$course_rows = mysqli_fetch_assoc($course_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Courses</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>View list of departmental courses and add new course.</strong>
                        </div>
                       <div class="text-center">
                        <?php
                       	if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
          				   			echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
          				   			echo "Course added successfully!";
          				   			echo '</div>';
          				   		}
                        if (isset($_GET['act']) && $_GET['act']=='del' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Course deleted successfully!";
                          echo '</div>';
                        }
                        if (isset($err) && $err==1 && isset($errmsg)) {
                          echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                          echo $errmsg;
                          echo '</div>';
                        }
          				   		?>
                         <form action="" method="POST">
                           <input type="text" name="code" placeholder="Code" required="required" maxlength="6" size="6" />
                           <input type="text" size="15" maxlength="50" name="title" placeholder="Course Title" /> 
                           <select name="unit" required="required" style="padding:8px;">
                              <option disabled="disabled" selected="selected">Choose Unit</option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                              <option value="6">6</option>
                            </select>
                           <select name="department" required="required" style="padding:8px;">
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
                            <!-- <div style="margin-top: 10px;"></div> -->
                            <input type='hidden' name='ADD' value='YES' />
                           <button type="submit" class="btn btn-primary">Add New Course</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 500px; overflow: hidden;">
                   <?php
                    if ($course_count > 0) {
                  ?>
                     <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>CODE</th>
                          <th>COURSE TITLE</th>
                          <th>DEPARTMENT</th>
                          <th>UNIT</th>
                          <th>ACTION</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        do{
                        ?>
                          <tr>
                            <td><?= $course_rows['course_code']; ?></td>
                            <td><?= $course_rows['course_title']; ?></td>
                            <td><?= $course_rows['dept_name']; ?> (<?= $course_rows['dept_acron']; ?>)</td>
                            <td align="center"><?= $course_rows['credit_unit']; ?></td>
                            <td><a href="?action=delete&cid=<?= $course_rows['cid']; ?>" onClick="return confirmAction()">Delete</a></td>
                          </tr>
                        <?php
                        }while($course_rows = mysqli_fetch_assoc($course_result));
                        ?>
                      </tbody>
                    </table>
                  <?php
                    }else{
                      echo "<div>No course found</div>";
                    }
                   ?>
                  </div>
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