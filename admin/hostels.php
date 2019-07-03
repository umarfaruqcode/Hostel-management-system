<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "hostels";

//process delete
if (isset($_GET['action']) && isset($_GET['hid']) && $_GET['action']=="delete" && !empty($_GET['hid'])) {
  $hid = $_GET['hid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM hostels WHERE hostel_id='$hid'") or die(unexpectedError());
  header("Location: hostels.php?act=del&r=1");
  exit();
}

//add new hostel
if (isset($_POST['ADD']) && $_POST['ADD']=="YES") {
  $hostel_name = trim($_POST['hostel_name']);
  $no_of_rooms = trim($_POST['noofrooms']);
  $maxstudents = trim($_POST['stdtperroom']);
  $err = 0;
  $errmsg = "";
  //check if hostel name already exists in the database
  $checkQuery = mysqli_query($hostelcon,"SELECT * FROM hostels WHERE hostel_name='$hostel_name'");
  $checkCount = mysqli_num_rows($checkQuery);
  if ($checkCount > 0) {
    $err = 1;
    $errmsg = "Hostel name already exists.";
  }
  if ($err==0) {//if no error, process the submission
    $inserthostel = mysqli_query($hostelcon,"INSERT INTO hostels (hostel_name,no_of_rooms,max_stdt_per_room) VALUES('$hostel_name','$no_of_rooms','$maxstudents')") or die(unexpectedError());
    header("Location: hostels.php?_rdir=1");
    exit();
  }
}

//load hostels
$hostels_query = "SELECT * FROM hostels ORDER BY hostel_name ASC";
$hostels_result = mysqli_query($hostelcon, $hostels_query) or die(mysqli_error($hostelcon));
$hostels_count = mysqli_num_rows($hostels_result);
$hostels_rows = mysqli_fetch_assoc($hostels_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hostels - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Hostels</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>View list of hostels and add new hostel.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                        if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Hostel added successfully!";
                          echo '</div>';
                        }
                        if (isset($_GET['act']) && $_GET['act']=='del' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Hostel deleted successfully!";
                          echo '</div>';
                        }
                        if (isset($err) && $err==1 && isset($errmsg)) {
                          echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                          echo $errmsg;
                          echo '</div>';
                        }
                        ?>
                         <form action="hostels.php" method="POST">
                           <input type="text" name="hostel_name" placeholder="Type new hostel name" required="required" maxlength="100" /> 
                           <input type="number" min="1" max="10000" name="noofrooms" placeholder="No of Rooms" style="width: 120px;" required="required" />
                           <input type="number" min="1" max="10" name="stdtperroom" placeholder="Max Student Per Room" style="width: 170px;" required="required" />
                           <input type="hidden" name="ADD" value="YES" />
                           <button type="submit">Add Hostel</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 500px; overflow: auto;">
                  <?php
                  if ($hostels_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr style="text-transform: uppercase;">
                          <th>S/N</th>
                          <th>Hostel Name</th>
                          <th>Rooms</th>
                          <th>Max Student</th>
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
                          <td><?= $hostels_rows['hostel_name']; ?></td>
                          <td><?= $hostels_rows['no_of_rooms']; ?></td>
                          <td><?= $hostels_rows['max_stdt_per_room']; ?></td>
                          <td><a href="?action=delete&hid=<?= $hostels_rows['hostel_id']; ?>" onClick="return confirmAction()">Delete</a></td>
                        </tr>
                        <?php
                        $i++;
                        }while($hostels_rows = mysqli_fetch_assoc($hostels_result));
                        ?>
                      </tbody>
                    </table>
                    <?php
                    }else{
                      echo "<div>No hostel found</div>";
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