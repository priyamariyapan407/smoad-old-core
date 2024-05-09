<?php 

include('../../c/c_api_set1.php');
include('../../c/c_api_mail.php');
include('../../c/c_api_alert.php');

//instead print, since these are API driven, dump debug into this DB
function _debug_api_test_to_db($db, $dump) { $db->query("insert into smoad_api_test (dump) values (\"$dump\")"); }

//does basic initial validation
//returns: decrypted data, device serialnumber via call by args, and returns proceed_further
function _api_do_initial_due_diligence($db, $_md5_serialnumber, &$_data, &$_serialnumber)
{	$proceed_further=false;
	$_serialnumber="";
	
	if($_md5_serialnumber!=null && $_data!=null) { $proceed_further=true; }

	//first find matching device ??
	if($proceed_further==true)
	{	$query = "SELECT serialnumber FROM smoad_devices WHERE MD5(serialnumber)=\"$_md5_serialnumber\""; 
		if($res = $db->query($query)) 
		{ while($row = $res->fetch_assoc()) { $_serialnumber=$row['serialnumber']; $proceed_further=true; } 
		}
	}
	
	if($proceed_further==true)
	{	$query = "select api_prikey from smoad_devices where serialnumber=\"$_serialnumber\""; 
		if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $api_prikey = hex2bin($row['api_prikey']); $proceed_further=true; } }
	}
	
	if($proceed_further==true)
	{  //$_data = hex2bin($_data);
		//if(openssl_private_decrypt($_data, $_data, $api_prikey)!=false) { $proceed_further=true; } else { $proceed_further=false; }
		if(api_dencrypt_from_hex($_data, $decrypted, $api_prikey)==false) { $proceed_further=false; } else { $proceed_further=true; }
		$_data = $decrypted;
	}
	
	return $proceed_further;
} 

//check if there is already log-entry for this device with this random-key ??
/// this will eliminate man in the middle attack, or some bogus pkt data matching original pkts
function _api_validate_id_rand_key_salt($db, $_table, $_id_rand_key)
{	$proceed_further=true;
	$query = "select id_rand_key from $_table where device_serialnumber=\"$_serialnumber\" and id_rand_key=\"$_id_rand_key\"";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $proceed_further=false; } }
	return $proceed_further;
}

?>