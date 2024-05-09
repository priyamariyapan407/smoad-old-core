<html><body><div style="font-family:arial;font-size: 12px;">
<?php
error_reporting(5);
include("c_db_access.php");
include("c_api_set1.php");
session_unset();
session_destroy();
$user = $_POST['username'];
$pass = $_POST['password'];
session_start();
$_SESSION = array();
$_SESSION['color_lighter'] = "#fcff3c";
$_SESSION['color_light'] = "#fcff3c";
$_SESSION['color_dark'] = "#fcff3c";
$_SESSION['username'] = NULL;
$_SESSION['login_type'] = NULL;
$_SESSION['hostname'] = `hostname`;
$_SESSION['ztp_dev_sn'] = 'notset'; $_SESSION['ztp_dev_id'] = 'notset'; $_SESSION['ztp_dev_details'] = 'notset';
$_SESSION['ztp_sds_sn'] = 'notset'; $_SESSION['ztp_sds_id'] = 'notset'; $_SESSION['ztp_sds_details'] = 'notset';
$_SESSION['ztp_cust_id'] = 'notset'; $_SESSION['ztp_cust_name'] = 'notset';
$_SESSION['id_customer'] = NULL;
$_SESSION['root_email1'] = NULL;
$_SESSION['user_email1'] = NULL;
$_SESSION['cust_email1'] = NULL;
$session_key = random_bytes(12); $session_key = bin2hex($session_key);
$_SESSION['sessionkey'] = $session_key;
$login_current_timestamp = time();
$userdb='';
$login=false;
$user = api_prevent_sql_injection($db, $user); //prevent SQL injection
$pass = api_prevent_sql_injection($db, $pass); //prevent SQL injection

include("c_api_mail.php");
include("c_api_alert.php");

if($login==false)
{	$query = "select username, password, language, email1 from profile where password='$pass' and username='$user'";
	if($res = $db->query($query))
	{	while ($row = $res->fetch_assoc()) 
		{  $userdb = $row['username'];
			$login=true;
			$_SESSION['login_type'] = 'root';
			$_SESSION['username'] = $user;
			$_SESSION['root_email1'] = $row['email1'];
			$query2 = "update profile set login_current_timestamp=\"$login_current_timestamp\" where username='$user'";
			$db->query($query2);
			api_alert_send_login_alert($db, $user, 'Root');
			print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=../index.php?page=home&skey=$session_key\">";
	  	}
	}
	
	if($login==false)
	{	$query = "select id, username, access_level, email1 from smoad_users where password='$pass' and username='$user' and access_level<>'block' ";
		if($res = $db->query($query))
		{	while ($row = $res->fetch_assoc()) 
			{  $userdb = $row['username'];
			   $_SESSION['id_user'] = $row['id'];
				$login=true;
				$_SESSION['login_type'] = $row['access_level'];
				$_SESSION['username'] = $user;
				$_SESSION['user_email1'] = $row['email1'];
				$query2 = "update smoad_users set login_current_timestamp=\"$login_current_timestamp\" where username='$user'";
				$db->query($query2);
				if($row['access_level']=='admin') { $_login_type="Admin"; }
				else if($row['access_level']=='limited') { $_login_type="Limited"; }
				api_alert_send_login_alert($db, $user, $_login_type);
				print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=../index.php?page=home&skey=$session_key\">";
	   	}
		}
	}

	if($login==false)
	{	$query = "select id, custname, email1 from smoad_customers where password='$pass' and custname='$user'";
		if($res = $db->query($query))
		{	while ($row = $res->fetch_assoc()) 
			{  $userdb = $row['custname'];
			   $_SESSION['id_customer'] = $row['id'];
				$login=true;
				$_SESSION['login_type'] = 'customer';
				$_SESSION['username'] = $user;
				$_SESSION['cust_email1'] = $row['email1'];
				$query2 = "update smoad_customers set login_current_timestamp=\"$login_current_timestamp\" where custname='$user'";
				$db->query($query2);
				api_alert_send_login_alert($db, $user, 'Customer');
				print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=../index.php?page=home&skey=$session_key\">";
	   	}
		}
	}


	if($login==false)
	{	api_alert_send_failed_login_alert($db, $user);
		session_unset();
		session_destroy();
		print "Incorrect login name or password.<br><br>";
	   print "Redirecting... &nbsp;  <br>";
	   print "<meta HTTP-EQUIV=\"REFRESH\" content=\"10; url=../index.php?page=login\">";
	}
}

if($login!=false)
{	$query = "select server_name from profile where id=1";
	if($res = $db->query($query))
	{	while ($row = $res->fetch_assoc()) 
		{  $_SESSION['server_name'] = $row['server_name']; 
		}
	}
}

mysqli_close($db);
?>
</div></body></html>