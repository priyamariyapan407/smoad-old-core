<?php ob_start( 'ob_gzhandler' );
session_start(); error_reporting(5);
$login_user_name = NULL;
if(isset($_SESSION['username']))
{ $login_user_name = $_SESSION['username']; }
else
{ session_unset(); session_destroy();
 print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=../index.php\">";
}
?>