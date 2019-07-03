<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "bookings";

//process delete
if (isset($_GET['action']) && isset($_GET['bid']) && !empty($_GET['bid'])) {
  $bid = $_GET['bid'];
  
  $update_date = date("Y-m-d H:i:s",time());

  if($_GET['action']=="delete"){
    $delete = mysqli_query($hostelcon, "DELETE FROM bookings WHERE bookid='$bid'") or die(unexpectedError());
    header("Location: bookings.php?act=del&r=1");
    exit();
  }
  elseif ($_GET['action']=="approve") {
    $approve = mysqli_query($hostelcon, "UPDATE bookings SET date_approved='$update_date' WHERE bookid='$bid'") or die(unexpectedError());
    header("Location: bookings.php?act=app&r=1");
  }
  elseif ($_GET['action']=="disapprove") {
    $delete = mysqli_query($hostelcon, "UPDATE bookings SET date_approved = NULL WHERE bookid='$bid'") or die(unexpectedError());
    header("Location: bookings.php?act=disapp&r=1");
  }
  else{
    //nothing is happening
  }
}

//add new hostel
if (isset($_POST['APPROVE']) && $_POST['APPROVE']=="YES") {
  $hostel_id = trim($_POST['hostelid']);
  $book_id = trim($_POST['bookid']);
  $err = 0;
  $errmsg = "";
  //check if hostel name already exists in the database
  $loadhostelrooms = mysqli_query($hostelcon,"SELECT (no_of_rooms * max_stdt_per_room) AS totaltenants, no_of_rooms FROM hostels WHERE hostel_id='$hostel_id'") or die(mysqli_error($loadhostelrooms));
  $hostelrow = mysqli_fetch_assoc($loadhostelrooms);

  $checkQuery = mysqli_query($hostelcon,"SELECT COUNT(bookid) AS alreadybooked FROM bookings WHERE book_hostel_id='$hostel_id'");
  $checkedrow = mysqli_fetch_assoc($checkQuery);

  if ($checkedrow['alreadybooked'] >= $hostelrow['totaltenants']) {
    $err = 1;
    $errmsg = "No available room in the selected hostel.";
  }

  if ($err==0) {//if no error, process the submission
    $inserthostel = mysqli_query($hostelcon,"INSERT INTO hostels (hostel_name,no_of_rooms,max_stdt_per_room) VALUES('$hostel_name','$no_of_rooms','$maxstudents')") or die(unexpectedError());
    header("Location: hostels.php?_rdir=1");
    exit();
  }
}

//load hostel bookings
$hostels_query = "SELECT * FROM bookings INNER JOIN hostels ON hostels.hostel_id=bookings.book_hostel_id INNER JOIN students ON students.stdtid=bookings.book_stdtid ORDER BY session ASC, date_booked ASC";
$hostels_result = mysqli_query($hostelcon, $hostels_query) or die(mysqli_error($hostelcon));
$hostels_count = mysqli_num_rows($hostels_result);
$hostels_rows = mysqli_fetch_assoc($hostels_result);
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bookings - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Bookings</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>View new and confirmed bookings.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                        if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Hostel approved successfully!";
                          echo '</div>';
                        }

                        if (isset($_GET['act']) && $_GET['act']=='del' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Request deleted successfully!";
                          echo '</div>';
                        }
                        elseif (isset($_GET['act']) && $_GET['act']=='app' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Request approved successfully!";
                          echo '</div>';
                        }
                        elseif (isset($_GET['act']) && $_GET['act']=='disapp' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Request disapproved successfully!";
                          echo '</div>';
                        }

                        if (isset($err) && $err==1 && isset($errmsg)) {
                          echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                          echo $errmsg;
                          echo '</div>';
                        }
                        ?>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="height: 230px; overflow: auto;">
                  <?php
                  if ($hostels_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr style="text-transform: uppercase;">
                          <th class="text-center">S/N</th>
                          <th>Student Name</th>
                          <th>Hostel Name</th>
                          <th>Room No</th>
                          <th>Session</th>
                          <th class="text-center">Status</th>
                          <th class="text-center">ACTION</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $i = 1;
                        do{
                           $status = !empty($hostels_rows['date_approved']) ? "Approved on<br>".date("jS M, Y",strtotime($hostels_rows['date_approved'])) : 'Awaiting approval';
                        ?>
                        <tr>
                          <td class="text-center"><?= $i; ?></td>
                          <td><?= strtoupper($hostels_rows['surname']." ".$hostels_rows['other_names']); ?><br><?= $hostels_rows['matric_no'] ?> [<?= $hostels_rows['level'] ?> level]</td>
                          <td><?= $hostels_rows['hostel_name']; ?></td>
                          <td><?= $hostels_rows['room_no']; ?></td>
                          <td><?= $hostels_rows['session']; ?></td>
                          <td align="center" class="text-center"><?= $status; ?></td>
                          <td><?php if(empty($hostels_rows['date_approved'])) { ?> <a href="?action=approve&bid=<?= $hostels_rows['bookid'] ?>" onClick="return confirmAction()" ><i class="fa fa-check-square"></i> Approve</a><br> <?php } else{ ?> <a href="?action=disapprove&bid=<?= $hostels_rows['bookid'] ?>" onClick="return confirmAction()" ><i class="fa fa-close"></i> Disapprove</a><br><?php } ?> <?php if(empty($hostels_rows['date_approved'])) { ?> <a href="?action=delete&bid=<?= $hostels_rows['bookid'] ?>" onClick="return confirmAction()" ><i class="fa fa-trash"></i>  Delete</a> <?php } else{ echo "";} ?></td>
                        </tr>
                        <?php
                        $i++;
                        }while($hostels_rows = mysqli_fetch_assoc($hostels_result));
                        ?>
                      </tbody>
                    </table>
                    <?php
                    }else{
                      echo "<div>No booking found</div>";
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