<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "students";
$students_query = "SELECT * FROM students INNER JOIN departments ON students.stdt_deptid=departments.deptid ORDER BY level ASC, students.stdt_deptid ASC, surname ASC";
$students_result = mysqli_query($hostelcon, $students_query) or die(mysqli_error($hostelcon));
$students_count = mysqli_num_rows($students_result);
$students_rows = mysqli_fetch_assoc($students_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Students - <?= APP_NAME ?></title>
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
</head>
<body>
    <div id="wrapper">
        <?php include_once("assets/includes/header.php"); ?>
        <?php include_once("assets/includes/side-nav.php"); ?>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-lg-12">
                     <h2 style="text-transform: uppercase;">Students</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>List of registered students</strong>
                        </div>
                       
                    </div>
                    </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 500px; overflow: auto;">
                  <?php
                    if ($students_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>NAME</th>
                          <th>DEPARTMENT</th>
                          <th>LEVEL</th>
                        </tr>
                      </thead>
                      <tbody>
                         <?php
                        do{
                        ?>
                        <tr>
                          <td><?= $students_rows['surname']; ?>, <?= $students_rows['other_names']; ?></td>
                          <td><?= $students_rows['dept_name']; ?> (<?= $students_rows['dept_acron']; ?>)</td>
                          <td><?= $students_rows['level']; ?>L</td>
                        </tr>
                        <?php
                        }while($students_rows = mysqli_fetch_assoc($students_result));
                        ?>
                      </tbody>
                    </table>
                    <?php
                    }else{
                      echo "<div>No student found</div>";
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