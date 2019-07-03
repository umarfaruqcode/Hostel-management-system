<?php
include("../includes/config.php");
require_once("assets/includes/admin_session_controller.php");
$pgname = "settings";
//process update
if (isset($_POST['upDate']) && $_POST['upDate']=="YES") {
  $dName = $_POST['displayName'];
  $email = $_POST['email'];
  $session = $_POST['current_session'];
  $semester = $_POST['current_semester'];
  $err = 0;
  $errmsg = array();
  if(!isset($_POST['current_session']) || (isset($_POST['current_session']) && empty($_POST['current_session']))) {
      $err = 1;
      $errmsg[] = "Choose a session";
  }
  $updateSuffix = "";
  if (isset($_POST['newpassword'])) {
    if(empty($_POST['newpassword'])){
        //do nothing. The user is not updating
    }
    elseif(strlen($_POST['newpassword'])>1 && strlen($_POST['newpassword'])<6){
      $err = 1;
      $errmsg[] = "New password cannot be less than 6 real characters!";
    }
    else{
      $pass = md5($_POST['newpassword']);/*encrypting password; sanitizing password to prevent SQL injection*/
      $updateSuffix = ", password='".$pass."'";//new password will be updated if not empty
    }
  }

  if ($err==0) {
    $updateQuery = mysqli_query($hostelcon,"UPDATE admin SET displayName='$dName', admin_email='$email',current_session='$session',current_semester='$semester'".$updateSuffix) or die(unexpectedError().mysqli_error($hostelcon));
    header("Location: settings.php?_rdir=1");
    exit();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Profile - <?= APP_NAME ?></title>
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
                     <h2 style="text-transform: uppercase;">Settings</h2>   
                    </div>
                </div>              
                 <!-- /. ROW  -->
                  <hr />
                <div class="row">
                    <div class="col-lg-12 ">
                        <div class="alert alert-info">
                             <strong>Edit general and admin login settings. Leave 'New Password' field blank if you don't want to change your password.</strong>
                        </div>
                       <div class="text-center">
                       <?php
                       if ((isset($err) && $err == 1) && isset($errmsg)) {
                          echo '<div class="alert alert-danger" style="margin-bottom: 30px;">';
                          foreach ($errmsg as $msg) {
                            echo $msg.'<br>';
                          }
                          echo '</div>';
                        }
                        if (isset($_GET['_rdir']) && $_GET['_rdir']==1) {
                          echo '<div class="alert alert-success text-center" style="margin-bottom: 30px;">';
                          echo "Profile updated!";
                          echo '</div>';
                        }
                        ?>
                         <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                              <h5 style="font-weight: 700;">Edit Settings</h5>
                              <div class="input-group">
                                <span class="input-group-addon">Username</span>
                                <input type="text" readonly="readonly" disabled="disabled" class="form-control" value="<?= $adminuname; ?>" />
                              </div>
                              <br>
                              <div class="input-group">
                                <span class="input-group-addon">Display Name</span>
                                <input type="text" class="form-control" name="displayName" required="required" maxlength="50" placeholder="Display Name" value="<?= $displayName; ?>" />
                              </div>
                              <br />
                              <div class="input-group">
                                <span class="input-group-addon">Email Address</span>
                                <input type="text" required="required" maxlength="80" name="email" class="form-control" placeholder="Email Address" value="<?= $admin_email; ?>" />
                              </div>
                              <br />
                            </div>
                           
                            <div class="col-lg-6 col-md-6">
                              <div class="input-group">
                                <h5>&nbsp;</h5>
                              </div>
                              <div class="input-group">
                                <span class="input-group-addon">Current Session</span>
                                <select name="current_session" class="form-control" required="required" style="padding:8px;">
                                  <option selected="selected" value="<?= $current_session; ?>"><?= $current_session; ?></option>
                                  <option value="<?= date("Y")-1 ?>/<?= date("Y") ?>"><?= date("Y")-1 ?>/<?= date("Y") ?></option>
                                  <option value="<?= date("Y") ?>/<?= date("Y")+1 ?>"><?= date("Y") ?>/<?= date("Y")+1 ?></option>
                                </select>
                              </div>
                              <br />
                              <div class="input-group">
                                <span class="input-group-addon">Current Semester</span>
                                <select name="current_semester" required="required" class="form-control">
                                  <option disabled="disabled">Select Semester</option>
                                  <option value="1" <?php if($current_semester==1){ echo "selected"; } ?>>Harmatan</option>
                                  <option value="2" <?php if($current_semester==2){ echo "selected"; } ?>>Rain</option>
                                </select>
                              </div>
                              <br />
                              <div class="input-group">
                                <span class="input-group-addon">New Password</span>
                                <input type="password" name="newpassword" class="form-control" placeholder="New Password" />
                              </div>
                              <em style="color: #FF5500;">Leave password field blank if not changing</em>
                              <br />
                              <br>
                              <div>
                                <input type="hidden" name="upDate" value="YES">
                                <button type="submit" class="btn btn-primary">Save</button>
                              </div>
                            </div>
                          </div>
                         </form>
                       </div>
                    </div>
                  </div>
                  <!-- /. ROW  -->
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