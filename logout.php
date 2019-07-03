<?php
require_once("includes/config.php");
//logout action
$logoutGoTo = APP_ROOT."index.php?view=login&_rdir=logout&r=1";
if ((isset($_GET['role'])) && !empty($_GET['role'])) {
  //to fully log out a visitor we need to clear the session varialbles
  if ($_GET['role']=="student") {
    $_SESSION['stdtid'] = NULL;
    $_SESSION['log_token'] = NULL;
    unset($_SESSION['stdtid']);
    unset($_SESSION['log_token']);
  }
  elseif ($_GET['role']=="admin") {
    $_SESSION['adminid'] = NULL;
    $_SESSION['log_token'] = NULL;
    unset($_SESSION['adminid']);
    unset($_SESSION['log_token']);
    $logoutGoTo = APP_ROOT."adminlogin.php?view=login&_rdir=logout&r=1";
  }
  else{
    session_unset();
  }
  //
  if (isset($_SESSION['PrevUrl'])) {
    $_SESSION['PrevUrl'] = NULL;
    unset($_SESSION['PrevUrl']);
  }
}
else{
    if (isset($_SESSION['stdtid'])) {
      $_SESSION['stdtid'] = NULL;
      unset($_SESSION['stdtid']);
    }
    elseif (isset($_SESSION['adminid'])) {
      $_SESSION['adminid'] = NULL;
      unset($_SESSION['adminid']);
    }
    else{
      session_destroy();
    }
}
header("Location: $logoutGoTo");
exit;
?>
<h4><a href="<?= $logoutGoTo; ?>">Click here to continue&raquo;</a></h4>