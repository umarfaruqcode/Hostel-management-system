<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "lecturers";

//process delete
if (isset($_GET['action']) && isset($_GET['lid']) && $_GET['action']=="delete" && !empty($_GET['lid'])) {
  $lid = $_GET['lid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM lecturers WHERE lid='$lid'") or die(unexpectedError()."<p>Possible error may be that the lecturer you are trying to delete is already associated with an item that is preventing you from deleting it.</p>");
}

//add new lecture
if (isset($_POST['ADD']) && $_POST['ADD']=="YES") {
  $lecturername = $_POST['lecturername'];
  $department = $_POST['department'];
  $insertLecturer = mysqli_query($hostelcon,"INSERT INTO lecturers (lecturer_name,deptid) VALUES('$lecturername','$department')") or die(unexpectedError());
  header("Location: lecturers.php?_rdir=1");
  exit();
}

//load lecturers
$lecturer_query = "SELECT * FROM lecturers INNER JOIN departments ON lecturers.deptid=departments.deptid WHERE lecturer_name !='Other' ORDER BY lecturers.deptid ASC, lecturer_name ASC";
$lecturer_result = mysqli_query($hostelcon, $lecturer_query) or die(mysqli_error($hostelcon));
$lecturer_count = mysqli_num_rows($lecturer_result);
$lecturer_rows = mysqli_fetch_assoc($lecturer_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lecturers - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Lecturers</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>View lecturers and their ratings, and add new lecturer.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                       	if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
          				   			echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
          				   			echo "Lecturer added successfully!";
          				   			echo '</div>';
          				   		}
          				   		?>
                         <form action="lecturers.php" method="POST">
                           <input type="text" name="lecturername" placeholder="Type lecturer's name" required="required" maxlength="60" /> 
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
                            <input type="hidden" name="ADD" value="YES" />
                           <button type="submit" class="btn btn-primary">Add New Lecturer</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 500px; overflow: auto;">
                   <?php
                    if ($lecturer_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>NAME</th>
                          <th>DEPARTMENT</th>
                          <th>RATING</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        do{
                        ?>
                        <tr>
                          <td><?= $lecturer_rows['lecturer_name']; ?></td>
                          <td><?= $lecturer_rows['dept_name']; ?> (<?= $lecturer_rows['dept_acron']; ?>)</td>
                          <td><a href="ratings.php?action=view&lid=<?= $lecturer_rows['lid']; ?>">View Rating</a> | <a href="?action=delete&lid=<?= $lecturer_rows['lid']; ?>" onClick="return confirmAction()">Delete</a></td>
                        </tr>
                        <?php
                        }while($lecturer_rows = mysqli_fetch_assoc($lecturer_result));
                        ?>
                      </tbody>
                    </table>
                    <?php
                    }else{
                      echo "<div>No lecturer found</div>";
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