<?php
require_once("includes/config.php");
session_unset();
//GLOBAL initials
$err = 0;
$errmsg = array();

//process admin or student login
if (isset($_POST['Login']) && $_POST['Login']=="Process") {
	if (!isset($_POST['matricno']) || empty($_POST['matricno'])) {
		$err = 1;
		$errmsg[] = "Matric Number cannot be empty!";
	}
	if (!isset($_POST['password']) || empty($_POST['password'])) {
		$err = 1;
		$errmsg[] = "Password cannot be empty!";
	}

	if ($err == 0) {
		$userid = sanitize_data(mysqli_real_escape_string($hostelcon, $_POST['matricno']));/*sanitizing username to prevent SQL injection*/
		$pass = md5(mysqli_real_escape_string($hostelcon, $_POST['password']));/*encrypting password;*/
		$query = "SELECT * FROM students WHERE matric_no='$userid' AND password='$pass'";

		$result = mysqli_query($hostelcon,$query);
		if($result) {
			$foundUser = mysqli_num_rows($result);
			if ($foundUser == 1) {//if the user is found in the database
				//the user will be redirected to the corresponding dashboard
				$_SESSION['stdtid'] = $userid;
				$_SESSION['log_token'] = sha1(time());
				header('Location: '.APP_STDT_DIR."?token=".$_SESSION['log_token']);
				exit;
			}
			else{//if user not found
				$err = 1;
				$errmsg[] = "Wrong login credentials!";
			}
		}else{
			$err = 1;
			$errmsg[] = "Oops! We encountered an unexpected problem, so we are unable to log you in.";
		}
	}
}
?>
<?php
//process student registration
if (isset($_POST['Register']) && $_POST['Register']=="Process") {
	//data validation
	if (!isset($_POST['password']) || strlen($_POST['password'])<6) {
		$err = 1;
		$errmsg[] = "Password cannot be less than 6 real characters!";
	}
	if (!isset($_POST['department'])) {
		$err = 1;
		$errmsg[] = "Please choose a department.";
	}
	if (!isset($_POST['gender'])) {
		$err = 1;
		$errmsg[] = "Please choose a gender.";
	}
	if (!isset($_POST['level'])) {
		$err = 1;
		$errmsg[] = "Please choose your level.";
	}
	if (!isset($_POST['email'])) {
        $err = 1;
        $errmsg[] = "Email address must be filled out.";
    }
	if (strlen(sanitize_data($_POST['phone']))<11) {
		$err = 1;
		$errmsg[] = "Phone number must not be less than 11 characters.";
	}
	//check if matric number already exists in the database
	$matricno = sanitize_data($_POST['matricno']);
	$checkuserid = mysqli_query($hostelcon,"SELECT * FROM students WHERE matric_no='$matricno'");
	$checkuserid = mysqli_num_rows($checkuserid);
	if ($checkuserid > 0) {
		$err = 1;
		$errmsg[] = "Matric Number already registered. Try <a href='index.php?view=login'>log in</a> if you're sure the Matric Number is yours.<br>";
	}
	//check if email already used
	$email = sanitize_data($_POST['email']);
	$checkemail = mysqli_query($hostelcon,"SELECT * FROM students WHERE email='$email'");
	$checkemail = mysqli_num_rows($checkemail);
	if ($checkemail > 0) {
		$err = 1;
		$errmsg[] = "Email address is already being used.";
	}
	//check if phone number already used
	$phone = sanitize_data($_POST['phone']);
	$checkphone = mysqli_query($hostelcon,"SELECT * FROM students WHERE phone='$phone'");
	$checkphone = mysqli_num_rows($checkphone);
	if ($checkphone > 0) {
		$err = 1;
		$errmsg[] = "Phone number is already being used.";
	}

	if ($err == 0) {
		$sname = sanitize_data($_POST['sname']);
		$onames = sanitize_data($_POST['onames']);
		$gender = sanitize_data($_POST['gender']);
		$department = sanitize_data($_POST['department']);
		$level = $_POST['level'];
		$pass = md5($_POST['password']);/*encrypting password; sanitizing password to prevent SQL injection*/
		$regdate = date('Y-m-d H:i:s');
		//process registration
		$insertquery = "
			INSERT INTO students(
				matric_no,
				level,
				password,
				surname,
				other_names,
				gender,
				email,
				phone,
				stdt_deptid,
				reg_date
			) 
			VALUES(
				'$matricno',
				'$level',
				'$pass',
				'$sname',
				'$onames',
				'$gender',
				'$email',
				'$phone',
				'$department',
				'$regdate'
			)";
		$result = mysqli_query($hostelcon, $insertquery) or die("An unexpected problem occured while trying to process your request.");
		$gotoURL_afterInsert = "index.php?view=register&_rdir=1";	
		header("Location: ".$gotoURL_afterInsert);
		exit;
	}
}
//capture error links
if (isset($_GET['_rdir']) && $_GET['_rdir']=="nolog") {
	$err = 1;
	$errmsg[] = "Log in to proceed!";
}
if (isset($_GET['_rdir']) && $_GET['_rdir']=="notfound") {
	$err = 1;
	$errmsg[] = "Could not find your details. Please log in again.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Login & Registration</title>
<!-- custom-theme -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //custom-theme -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- js -->
<script src="js/jquery-1.9.1.min.js"></script>
<!--// js -->
<link rel="stylesheet" type="text/css" href="css/easy-responsive-tabs.css " />
 <link href="//fonts.googleapis.com/css?family=Questrial" rel="stylesheet">
 <style type="text/css">
 	.alert{
 		text-align: center;
 	}
 </style>
</head>
<body class="bg agileinfo">
   <h1 class="agile_head text-center">Student<br><span style="font-size: 60%;">Login & Registration</span></h1>
   <div class="w3layouts_main wrap">
    <!--Horizontal Tab-->
        <div id="parentHorizontalTab_agile">
            <ul class="resp-tabs-list hor_1">
                <li>LogIn</li>
                <li>Register</li>
            </ul>
            <div class="resp-tabs-container hor_1">
               <div class="w3_agile_login">
               		<?php
					if ((isset($err) && $err == 1) && isset($errmsg)) {
						echo '<div class="alert alert-danger" style="margin-bottom: 30px;">';
						foreach ($errmsg as $msg) {
							echo $msg.'<br>';
						}
						echo '</div>';
					}
					if (isset($_GET['_rdir']) && $_GET['_rdir']=="logout") {
						echo '<div class="alert alert-info" style="margin-bottom: 30px;">';
							echo "Logout successful!";
						echo '</div>';
					}
					?>
                    <form action="index.php?view=login" method="post" autocomplete="OFF" class="agile_form">
					  <p>Matric Number</p>
					  <input type="text" name="matricno" autocomplete="OFF" required="required" />
					  <p>Password</p>
					  <input type="password" name="password" autocomplete="OFF" required="required" class="password" />
					  <input type="hidden" name="Login" value="Process" />
					  <input type="submit" value="LogIn" class="agileinfo" />
					</form>
					 <div class="login_w3ls">
				        <a href="adminlogin.php">Login as admin</a>
					 </div>
                    
                </div>
                <div class="agile_its_registration">
                	<?php
			   		if ((isset($err) && $err == 1) && isset($errmsg) && isset($_GET['view']) && $_GET['view']=="register") {
			   			echo '<div class="alert alert-danger" style="margin-bottom: 30px;">';
			   			foreach ($errmsg as $msg) {
			   				echo $msg.'<br>';
			   			}
			   			echo '</div>';
			   		}
			   		if (isset($_GET['view']) && $_GET['view']=="register" && isset($_GET['_rdir']) && $_GET['_rdir']==1) {
			   			echo '<div class="alert alert-success" style="margin-bottom: 30px;">';
			   				echo "Registration successful!<br>You can now <a href='index.php?view=login'>log in</a> to your account.";
			   			echo '</div>';
			   		}
			   		?>
                    <form autocomplete="OFF" action="index.php?view=register#parentHorizontalTab_agile2" method="post" class="agile_form">
					  <div style="text-align: left;">
					  	<p>Department</p><br>
						<select name="department" style="color: #666; background: transparent; padding: 8px; width: 99%;" required="required" style="background: transparent; border: none;color: #d1d1d1; padding:5px; margin: 5px 0px; width: 99%; border-radius: 8px;">
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

					<div>
					  <p>Current Level</p><br>
					  	<select name="level" required="required" style="color: #666; background: transparent; padding: 8px; width: 99%;">
							<option disabled="disabled" selected="selected">Choose Level</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="300">300</option>
							<option value="400">400</option>
							<option value="400">500</option>
						</select>
					</div>
					<br>
					  <p>Matric No</p>
					  <input type="text" name="matricno" required="required" value="<?= get_data('matricno') ?>" />
					  <p>Surname</p>
					  <input type="text" name="sname" maxlength="20" class="email" placeholder="Surname" required="required" value="<?= get_data('sname') ?>">
					  <p>Other Name(s)</p>
					  <input type="text" name="onames" class="email" maxlength="40" required="required" placeholder="Firstname Middlename" value="<?= get_data('onames') ?>">
					  <div>
					  <p>Gender</p><br>
					  <select name="gender" style="color: #666; background: transparent; padding: 8px; width: 99%;" required="required">
							<option disabled="disabled" selected="selected">I am...</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div><br>
					  <p>Email</p>
					  <input type="email" name="email" required="required" value="<?= get_data('email') ?>" />
					  <p>Mobile Phone</p>
					  <input type="tel" name="phone" required="required" value="<?= get_data('phone') ?>" />
					  <p>Password</p>
					  <input type="password" placeholder="Password (Min: 6 chars)" name="password" id="password1"  required="required">
					  <!-- <p>Confirm Password</p>
					  <input type="password" name="Confirm Password" id="password2"  required="required"> -->
					  <input type="hidden" name="Register" value="Process" />
					   <input type="submit" value="Signup"/>
					</form> 
				</div>
            </div>
        </div>
		 <!-- //Horizontal Tab -->
    </div>
	<div class="agileits_w3layouts_copyright text-center">
			<p>© <?= date('Y') ?> Unilorin</p>
	</div>
<!--tabs-->
<script src="js/easyResponsiveTabs.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	//Horizontal Tab
	$('#parentHorizontalTab_agile').easyResponsiveTabs({
		type: 'default', //Types: default, vertical, accordion
		width: 'auto', //auto or any width like 600px
		fit: true, // 100% fit in a container
		tabidentify: 'hor_1', // The tab groups identifier
		activate: function(event) { // Callback function if tab is switched
			var $tab = $(this);
			var $info = $('#nested-tabInfo');
			var $name = $('span', $info);
			$name.text($tab.text());
			$info.show();
		}
	});
});
</script>
<script type="text/javascript">
		window.onload = function () {
			document.getElementById("password1").onchange = validatePassword;
			document.getElementById("password2").onchange = validatePassword;
		}
		function validatePassword(){
			var pass2=document.getElementById("password2").value;
			var pass1=document.getElementById("password1").value;
			if(pass1!=pass2)
				document.getElementById("password2").setCustomValidity("Passwords Don't Match");
			else
				document.getElementById("password2").setCustomValidity('');	 
				//empty string means no validation error
		}

</script>
<!--//tabs-->
</body>
</html>