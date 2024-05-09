<?php 

//external API for: Device (CPE) <> SD-WAN Server

error_reporting(5); 
include('../../c/c_db_access.php');
include('c_include.php');

$_md5_serialnumber = $_POST['device']; //this is an md5 of device serialnumber
$_data = $_POST['data'];

$proceed_further=false;

$proceed_further = _api_do_initial_due_diligence($db, $_md5_serialnumber, $_data, $_serialnumber);

//_debug_api_test_to_db($db, "login $_serialnumber $_data");

if($proceed_further==true)
{	$_data = chop($_data);
	
	//check if the decrypted data contains some signatures. If so, then parse !
	if(strpos($_data, 'serialnumber') !== false && strpos($_data, 'password') !== false)
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
					case 'password'	: { $_password=$valuepair[1]; break; }
				}
			}
		}
	}
}


if($proceed_further==true) { $proceed_further = _api_validate_id_rand_key_salt($db, "smoad_user_device_access_log", $_id_rand_key); }

$auth_status="fail";

//return string salt
$id_rand_key = random_bytes(12); 
$id_rand_key = bin2hex($id_rand_key);
$print_string = "id_rand_key:$id_rand_key\n";

if($proceed_further==true)
{	$query = "select id, username, password, id_user_access from smoad_users where username=\"$_username\""; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$id = $row['id'];
				$username = $row['username'];
				$password = $row['password'];
				$id_user_access = $row['id_user_access'];
				
				if($_username==$username && $_password==$password)
				{	$auth_status="success"; $auth_type="login";
					$query2 = "select access_level from smoad_user_access where id=$id_user_access"; 
					 if($res2 = $db->query($query2))
					 {	while($row2 = $res2->fetch_assoc())
						{	$access_level = $row2['access_level'];
							$print_string .= "access_level:$access_level\n";
						}
					 }
				}
				else 
				{	$auth_status="fail"; $auth_type="n.a";
				}
			}
		}
	
	//log this event
	$query2="insert into smoad_user_device_access_log (username, device_serialnumber, auth_status, access_type, id_rand_key) 
		values (\"$_username\", \"$_serialnumber\", \"$auth_status\", \"$auth_type\", \"$_id_rand_key\")";
	$db->query($query2);
	//_debug_api_test_to_db($db, $query2);
}

$print_string .= "auth_status:$auth_status\n";

if($proceed_further==true)
{	$query = "select api_device_pubkey from smoad_devices where serialnumber=\"$_serialnumber\""; 
	if($res = $db->query($query)) 
	{ while($row = $res->fetch_assoc()) 
  	  { $api_device_pubkey = hex2bin($row['api_device_pubkey']); $proceed_further=true; } 
  	}
}
	
if($proceed_further==true)
{	//if(openssl_public_encrypt($print_string, $print_string_encrypt, $api_device_pubkey)!=false) { $print_string_encrypt = bin2hex($print_string_encrypt); $proceed_further=true; } else { $proceed_further=false; }
	if(api_encrypt_to_hex($print_string, $print_string_encrypt, $api_device_pubkey)==false) { $proceed_further=false; } else { $proceed_further=true; } 
}

//_debug_api_test_to_db($db, "login $_serialnumber - print string: $print_string_encrypt");

if($proceed_further==true) { print "$print_string_encrypt"; }

?>