<?php
include("../includes/config.php");
require_once("assets/includes/student_session_controller.php");
$pgname = "bookings";

//process delete
if (isset($_GET['action']) && isset($_GET['bid']) && $_GET['action']=="delete" && !empty($_GET['bid'])) {
  $bid = $_GET['bid'];
  $delete = mysqli_query($hostelcon, "DELETE FROM bookings WHERE bookid='$bid' AND date_approved IS NULL") or die(unexpectedError());
  header("Location: book.php?act=del&r=1");
  exit();
}

//add new hostel
if (isset($_POST['ADD']) && $_POST['ADD']=="YES") {
  $hostel_id = trim($_POST['hostelid']);
  $session = trim($_POST['session']);
  $err = 0;
  $errmsg = "";

  $checkExistence = mysqli_query($hostelcon,"SELECT * FROM bookings WHERE book_stdtid='$student_numeric_id' AND session='$session'");
  if(mysqli_num_rows($checkExistence) > 0){
    $err = 1;
    $errmsg = "You already booked a hostel room this session. If yet to be approved, please wait or delete the previously booked and re-apply, or visit the admin room.";
  }
  
  $loadhostelrooms = mysqli_query($hostelcon,"SELECT (no_of_rooms * max_stdt_per_room) AS totaltenants, no_of_rooms FROM hostels WHERE hostel_id='$hostel_id'") or die(mysqli_error($loadhostelrooms));
  $hostelrow = mysqli_fetch_assoc($loadhostelrooms);

  $checkQuery = mysqli_query($hostelcon,"SELECT COUNT(bookid) AS alreadybooked FROM bookings WHERE book_hostel_id='$hostel_id'");
  $checkedrow = mysqli_fetch_assoc($checkQuery);

  if ($checkedrow['alreadybooked'] >= $hostelrow['totaltenants']) {
    $err = 1;
    $errmsg = "No available room in the selected hostel.";
  }

  if ($err==0) {//if no error, process the submission
    $roomno = rand(1,$hostelrow['no_of_rooms']);
    $insertbook = mysqli_query($hostelcon,"INSERT INTO bookings (book_hostel_id,book_stdtid,room_no,session) VALUES('$hostel_id','$student_numeric_id','$roomno','$session')") or die(unexpectedError());
    header("Location: book.php?_rdir=1");
    exit();
  }
}

//load hostels bookings
$hostels_query = "SELECT * FROM bookings INNER JOIN hostels ON hostels.hostel_id=bookings.book_hostel_id WHERE book_stdtid='$student_numeric_id' ORDER BY session DESC, hostel_name ASC";
$hostels_result = mysqli_query($hostelcon, $hostels_query) or die(mysqli_error($hostelcon));
$hostels_count = mysqli_num_rows($hostels_result);
$hostels_rows = mysqli_fetch_assoc($hostels_result);

//load booking
/*$book_query = "SELECT * FROM bookings WHERE book_stdtid='$student_numeric_id' ORDER BY date_booked DESC";
$book_result = mysqli_query($hostelcon, $book_query) or die(mysqli_error($hostelcon));
$book_count = mysqli_num_rows($book_result);
$book_rows = mysqli_fetch_assoc($book_result);*/
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Book Hostel - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Book Hostel</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>Book hostel and wait for approval.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                        if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Booking was successfully! Please check back for approval status in 3 days.";
                          echo '</div>';
                        }
                        if (isset($_GET['act']) && $_GET['act']=='del' && isset($_GET['r']) && $_GET['r']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Booking deleted successfully!";
                          echo '</div>';
                        }
                        if (isset($err) && $err==1 && isset($errmsg)) {
                          echo '<div class="alert alert-danger text-center" style="margin-bottom: 30px;">';
                          echo $errmsg;
                          echo '</div>';
                        }
                        ?>
                         <form action="book.php" method="POST">
                          <div class="input-group">
                            <span class="input-group-addon">Hostel</span>
                            <select name="hostelid" required="required" class="form-control">
                              <option disabled="disabled" selected="selected">Choose Hostel</option>
                              <?php
                                $queryhostels = mysqli_query($hostelcon,"SELECT * FROM hostels ORDER BY hostel_name ASC")/* or die("<span style='color: red;'>".mysqli_error($hostelcon))*/;
                                while ($hostelsrow = mysqli_fetch_assoc($queryhostels)) {
                                  $hostelid = $hostelsrow['hostel_id'];
                                  $hostelcapacity = ($hostelsrow['no_of_rooms'] * $hostelsrow['max_stdt_per_room']);
                                  
                                  $checkbooked = mysqli_query($hostelcon,"SELECT COUNT(bookid) AS alreadybooked FROM bookings WHERE book_hostel_id='$hostelid'");
                                  $checkbookedrow = mysqli_fetch_assoc($checkbooked);

                                  $availability = $hostelcapacity - $checkbookedrow['alreadybooked'];
                              ?>
                                    <option value="<?= $hostelid ?>"><?= $hostelsrow['hostel_name']; ?> (<?= pluralize($availability,"space"); ?> left)</option>

                              <?php
                                }
                              ?>
                            </select>
                          </div>

                          <div class="input-group" style="margin-top: 10px; margin-bottom: 10px;">
                            <span class="input-group-addon">Session</span>
                            <select name="session" class="form-control" required="required" style="padding:8px;">
                              <option value="<?= $current_session; ?>"><?= $current_session; ?></option>
                            </select>
                          </div>
                           
                           <input type="hidden" name="ADD" value="YES" />
                           <button type="submit">Book Hostel</button>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  --> 
                  <div class="pad-top" style="max-height: 230px; overflow: auto;">
                  <?php
                  if ($hostels_count > 0) {
                  ?>
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr style="text-transform: uppercase;">
                          <th>S/N</th>
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
                          <td><?= $i; ?></td>
                          <td><?= $hostels_rows['hostel_name']; ?></td>
                          <td><?= $hostels_rows['room_no']; ?></td>
                          <td><?= $hostels_rows['session']; ?></td>
                          <td align="center" class="text-center"><?= $status; ?></td>
                          <td align="center" class="text-center"><?php if(empty($hostels_rows['date_approved'])) { ?> <a href="?action=delete&bid=<?= $hostels_rows['bookid'] ?>" onClick="return confirmAction()" >Delete</a> <?php } else{ echo " - ";} ?></td>
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