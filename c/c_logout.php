<?php session_start();  

$username = $_SESSION['username'];
$login_type = $_SESSION['login_type'];
if($login_type=='root') { $_login_type="Root"; }
else if($login_type=='admin') { $_login_type="Admin"; }
else if($login_type=='limited') { $_login_type="Limited"; }
else if($login_type=='customer') { $_login_type="Customer"; }

include("c_db_access.php");
include("c_api_mail.php");
include("c_api_alert.php");

$query = "update profile set login_current_timestamp=\"notset\" where username='$username'";
$db->query($query);

api_alert_send_logout_alert($db, $username, $_login_type);

session_regenerate_id();
session_destroy();
session_unset(); 

header("Location: ../index.php?page=login"); 

?>