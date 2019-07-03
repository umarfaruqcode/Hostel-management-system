<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "halls";

//process delete
if (isset($_GET['action']) && isset($_GET['hid']) && $_GET['action']=="delete" && !empty($_GET['hid'])) {
  $hid = $_GET['hid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM lecture_halls WHERE lhid='$hid'") or die(unexpectedError());
  header("Location: halls.php?act=del&r=1");
  exit();
}

//add new hall
if (isset($_POST['ADD']) && $_POST['ADD']=="YES") {
  $hallname = $_POST['hallname'];
  $capacity = $_POST['capacity'];
  $err = 0;
  $errmsg = "";
  //check if hall name already exists in the database
  $checkQuery = mysqli_query($hostelcon,"SELECT * FROM lecture_halls WHERE hall_name='$hallname'");
  $checkCount = mysqli_num_rows($checkQuery);
  if ($checkCount > 0) {
    $err = 1;
    $errmsg = "Hall name already exists.";
  }
  if ($err==0) {//if no error, process the submission
    $insertHall = mysqli_query($hostelcon,"INSERT INTO lecture_halls (hall_name,capacity) VALUES('$hallname','$capacity')") or die(unexpectedError());
    header("Location: halls.php?_rdir=1");
    exit();
  }
}

//load halls
$halls_query = "SELECT * FROM lecture_halls ORDER BY hall_name ASC";
$halls_result = mysqli_query($hostelcon, $halls_query) or die(mysqli_error($hostelcon));
$halls_count = mysqli_num_rows($halls_result);
$halls_rows = mysqli_fetch_assoc($halls_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halls - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Halls</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>View list of lecture halls and add new hall.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                        if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Lecture hall added successfully!";
                          echo '</div>';
                        }
                        if (isset($_GET['act']) && $_GET['act']=='del' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Hall deleted successfully!";
                          echo '</div>';
                        }
                        if (isset($err) && $err==1 && isset($errmsg)) {
                          echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                          echo $errmsg;
                          echo '</div>';
                        }
                        ?>
                         <form action="halls.php" method="POST">
                           <input type="text" name="hallname" placeholder="Type new hall name" required="required" maxlength="100" /> 
                           <input type="number" min="10" max="20000" name="capacity" placeholder="Hall capacity" style="width: 120px;" required="required" />
                           <input type="hidden" name="ADD" value="YES" />
                           <button type="submit">Add Hall</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 500px; overflow: auto;">
                  <?php
                  if ($halls_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>S/N</th>
                          <th>HALL NAME</th>
                          <th>CAPACITY</th>
                          <th>ACTION</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $i = 1;
                        do{
                        ?>
                        <tr>
                          <td><?= $i; ?></td>
                          <td><?= $halls_rows['hall_name']; ?></td>
                          <td><?= $halls_rows['capacity']; ?></td>
                          <td><a href="?action=delete&hid=<?= $halls_rows['lhid']; ?>" onClick="return confirmAction()">Delete</a></td>
                        </tr>
                        <?php
                        $i++;
                        }while($halls_rows = mysqli_fetch_assoc($halls_result));
                        ?>
                      </tbody>
                    </table>
                    <?php
                    }else{
                      echo "<div>No hall found</div>";
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