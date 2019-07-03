<?php
require_once("includes/config.php");

//GLOBAL initials
$err = 0;
$errmsg = array();

//process admin login
if (isset($_POST['Login']) && $_POST['Login']=="Process") {
	
	if (!isset($_POST['loginid']) || empty($_POST['loginid'])) {
		$err = 1;
		$errmsg[] = "Login ID cannot be empty!";
	}
	if (!isset($_POST['password']) || empty($_POST['password'])) {
		$err = 1;
		$errmsg[] = "Password cannot be empty!";
	}

	if ($err == 0) {
		$userid = sanitize_data(mysqli_real_escape_string($hostelcon, $_POST['loginid']));/*sanitizing username to prevent SQL injection*/
		$pass = md5(mysqli_real_escape_string($hostelcon, $_POST['password']));/*encrypting password;*/
		//process admin login
		$query = "SELECT * FROM admin WHERE username='$userid' AND password='$pass'";

		$result = mysqli_query($hostelcon,$query);
		if($result) {
			$foundUser = mysqli_num_rows($result);
			if ($foundUser == 1) {//if the user is found in the database
				//the user will be redirected to the corresponding dashboard
				$_SESSION['adminid'] = $userid;
				$_SESSION['log_token'] = sha1(time());
				header('Location: '.APP_ADMIN_DIR."?token=".$_SESSION['log_token']);
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
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
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
</head>
<body class="bg agileinfo">
   <h1 class="agile_head text-center">Admin Login</h1>
   <div class="w3layouts_main wrap">
    <!--Horizontal Tab-->
        <div id="parentHorizontalTab_agile">
            <ul class="resp-tabs-list hor_1">
                <li>LogIn</li>
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
                    <form action="adminlogin.php?view=login" method="post" class="agile_form">
					  <p>Login ID</p>
					  <input type="text" name="loginid" required="required" />
					  <p>Password</p>
					  <input type="password" name="password" required="required" class="password" /> 
					  <input type="submit" value="LogIn" class="agileinfo" />
					  <input type="hidden" name="Login" value="Process" />
					</form>
					 <div class="login_w3ls">
				        <a href="index.php">Login as student</a>
					 </div>                    
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