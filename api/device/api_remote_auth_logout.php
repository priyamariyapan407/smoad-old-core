<?php 

//external API for: Device (CPE) <> SD-WAN Server
error_reporting(5); 
include('../../c/c_db_access.php');
include('c_include.php');

$_md5_serialnumber = $_POST['device']; //this is an md5 of device serialnumber
$_data = $_POST['data'];

$proceed_further=false;

$proceed_further = _api_do_initial_due_diligence($db, $_md5_serialnumber, $_data, $_serialnumber);

//_debug_api_test_to_db($db, "logout $_serialnumber $_data");

if($proceed_further==true)
{	$_data = chop($_data);
	
	//check if the decrypted data contains some signatures. If so, then parse !
	if(strpos($_data, 'serialnumber') !== false && strpos($_data, 'username') !== false)
	{	$lines = explode("&", $_data);
		foreach($lines as $line) 
		{  $line = chop($line);
			$valuepair = explode("=", $line);
			if($proceed_further==true)
			{	switch($valuepair[0]) 
				{	case 'serialnumber'		: { $_serialnumber_parsed=$valuepair[1];
														 
														 //bogus serial number ??
														 if($_serialnumber_parsed!=$_serialnumber) { $proceed_further=false; }
														 break;
													  }
					case 'id_rand_key'	: { $_id_rand_key=$valuepair[1]; break; }

					case 'username'	: { $_username=$valuepair[1]; break; }
					case 'logout_status' : { $_logout_status=$valuepair[1]; break; }
				}
			}
		}
	}
}

if($proceed_further==true) { $proceed_further = _api_validate_id_rand_key_salt($db, "smoad_user_device_access_log", $_id_rand_key); }

//log this event
if($proceed_further==true)
{	if($_logout_status==null) { $_logout_status="logout"; }
	$query2="insert into smoad_user_device_access_log (username, device_serialnumber, auth_status, access_type, id_rand_key) 
		values (\"$_username\", \"$_serialnumber\", \"n.a\", \"$_logout_status\", \"$_id_rand_key\")";
	$db->query($query2);
}

?>