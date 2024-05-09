<?php

if($login_type=='root') { $main_table = "profile"; }
else if($login_type=='customer') { $main_table = "smoad_customers"; }
else if($login_type=='admin' || $login_type=='limited') { $main_table = "smoad_users"; }

$_user_id = $_POST['user_id'];
$_password_old = $_POST['password_old'];
$_password = $_POST['password'];
$_password2 = $_POST['password2'];
$setpass = false;

if($_password_old!=$_password) 
{	if($_password!=$_password2) { print "<pre>ERROR: Password does not match !</pre>"; } 
	if($_password==null || $_password2==null) { print "<pre>ERROR: Password is empty or null !</pre>"; }
	if($_password==$_password2 && $_password!=null || $_password2!=null && $_password_old!=$_password) { $setpass=true; }		
}

$_email1_old = $_POST['email1_old'];
$_email1 = $_POST['email1'];
if($_email1_old!=$_email1) 
{	db_api_set_value($db, $_POST['email1'], "email1", $main_table, $_user_id, "char"); }

if($setpass==true) 
{ db_api_set_value($db, $_password, "password", $main_table, $_user_id, "char");

  print "<pre>SUCCESS: Password changed successfully !</pre>";
}
 
//Front end
	
	if($login_type=='root') { $query = "select id, password, email1 from $main_table where username=\"$username\""; }
	else if($login_type=='customer') { $query = "select id, password, email1 from $main_table where custname=\"$username\""; }
	else if($login_type=='admin' || $login_type=='limited') { $query = "select id, password, email1 from $main_table where username=\"$username\""; }
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$password = $row['password'];
			$email1 = $row['email1'];
			
			api_form_post($curr_page);
         api_input_hidden("user_id", "$id");
			api_input_hidden("password_old", "$password");
			api_input_hidden("email1_old", "$email1");
			print '<table class="list_items" style="width:1024px;font-size:10px;">';
			api_ui_config_option_password("Password", $password, "password", null, null);
			api_ui_config_option_password("Retype password", $password, "password2", null, null);
			
			api_ui_config_option_text("Email-1", $email1, "email1", null, null);
			
			api_ui_config_option_update(null);
			print '</table></form><br>';
		}
	}

?>


