<?php 

function api_alert_check_alert_enabled($db, $alert)
{	$query = "select $alert alert from smoad_alert_config where id=1";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$alert = $row['alert'];
			return $alert;
		}
	}
	return 'FALSE';
}

function api_alert_send_login_alert($db, $user, $login_type)
{	$query = "select core_ui_user_login, core_ui_user_login_mail from smoad_alert_config where id=1";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$core_ui_user_login = $row['core_ui_user_login'];
			$core_ui_user_login_mail = $row['core_ui_user_login_mail']; 
		}
	}
	if($core_ui_user_login=='FALSE') { return; }

	$alert = "CORE UI user (".$user.") login incident !";
	$_id_rand_key = random_bytes(6);	$_id_rand_key = bin2hex($_id_rand_key);
	$alert_details = "Alert: $alert<br><br>\n";
	$alert_details .= "User name: $user <br>\n";
	$alert_details .= "User type: $login_type <br>\n";
	$query2="insert into smoad_alerts (title, type, details, id_rand_key) values ('$alert', 'user_login', '$alert_details', '$_id_rand_key')";
	$db->query($query2);
	
	$G_root_email1 = $GLOBALS['G_root_email1'];
	if($core_ui_user_login_mail=='TRUE') { api_send_mail_for_alert($alert, $alert_details, $G_root_email1); }
}

function api_alert_send_failed_login_alert($db, $user)
{	$alert = "CORE UI user (".$user.") login fail incident !";
	$_id_rand_key = random_bytes(6);	$_id_rand_key = bin2hex($_id_rand_key);
	$alert_details = "Alert: $alert<br><br>\n";
	$alert_details .= "User name: $user <br>\n";
	$query2="insert into smoad_alerts (title, type, details, id_rand_key) values ('$alert', 'user_login', '$alert_details', '$_id_rand_key')";
	$db->query($query2);
	
	$G_root_email1 = $GLOBALS['G_root_email1'];
	api_send_mail_for_alert($alert, $alert_details, $G_root_email1);
}

function api_alert_send_logout_alert($db, $user, $login_type)
{	$query = "select core_ui_user_login, core_ui_user_login_mail from smoad_alert_config where id=1";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$core_ui_user_login = $row['core_ui_user_login'];
			$core_ui_user_login_mail = $row['core_ui_user_login_mail']; 
		}
	}
	if($core_ui_user_login=='FALSE') { return; }

	$alert = "CORE UI user (".$user.") logout incident !";
	$_id_rand_key = random_bytes(6);	$_id_rand_key = bin2hex($_id_rand_key);
	$alert_details = "Alert: $alert<br><br>\n";
	$alert_details .= "User name: $user <br>\n";
	$alert_details .= "User type: $login_type <br>\n";
	$query2="insert into smoad_alerts (title, type, details, id_rand_key) values ('$alert', 'user_login', '$alert_details', '$_id_rand_key')";
	$db->query($query2);
	
	$G_root_email1 = $GLOBALS['G_root_email1'];
	if($core_ui_user_login_mail=='TRUE') { api_send_mail_for_alert($alert, $alert_details, $G_root_email1); }
}

function api_alert_get_dev_details_for_alert($db, $serialnumber, $alert)
{	$query = "select id, details, license, serialnumber, model, model_variant, root_password, superadmin_password, 
					firmware, area, sdwan_server_ipaddr, sdwan_proto, vlan_id, 
					customer_id, enable, updated, uptime 
					from smoad_devices where serialnumber='$serialnumber'"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model = $row['model']; $model_variant = $row['model_variant'];
			$root_password = $row['root_password'];
			$superadmin_password = $row['superadmin_password'];
			$firmware = $row['firmware'];
			$area = $row['area'];
			$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
			$sdwan_proto = $row['sdwan_proto'];
			$vlan_id = $row['vlan_id'];
			$customer_id = $row['customer_id'];
			$enable = $row['enable'];
			$updated = $row['updated'];
			$uptime = $row['uptime'];
		}
	}
	
	$query = "select ipaddr from smoad_port_cfg where type='uplink'"; 
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $core_ipaddr = $row['ipaddr']; } }
	
	$alert_details = "Alert: $alert<br><br><b>Edge Details:</b><br>\n";
	$alert_details .= "ID: $id <br>\n";
	$alert_details .= "Serial Number: $serialnumber <br>\n";
	$alert_details .= "Details: $details <br>\n";
	$alert_details .= "Area: $area <br>\n";

	if($model=="spider") { $_model="SMOAD Spider"; }
	else if($model=="spider2") { $_model="SMOAD Spider2"; }
	else if($model=="beetle") { $_model="SMOAD Beetle"; }
	else if($model=="bumblebee") { $_model="SMOAD BumbleBee"; }
	else if($model=="vm") { $_model="SMOAD VM"; }
	$alert_details .= "Model: $_model <br>\n";
	
	if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
	else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
	else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
	else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
	$alert_details .= "Model Variant: $_model_variant <br>\n";
	
	$alert_details .= "GW: $sdwan_server_ipaddr <br>\n";
	$alert_details .= "CORE: $core_ipaddr <br>\n";
	return $alert_details;
}

function api_alert_get_sds_details_for_alert($db, $serialnumber, $alert)
{	$query = "select id, details, license, serialnumber, ipaddr, area, status, enable, updated, type 
				  from smoad_sdwan_servers where serialnumber='$serialnumber'"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$ipaddr = $row['ipaddr'];
			$area = $row['area'];
			$type = $row['type'];
		}
	}
	
	$query = "select ipaddr from smoad_port_cfg where type='uplink'"; 
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $core_ipaddr = $row['ipaddr']; } }
	
	$alert_details = "Alert: $alert<br><br><b>Gateway Details:</b><br>\n";
	$alert_details .= "ID: $id <br>\n";
	$alert_details .= "Serial Number: $serialnumber <br>\n";
	$alert_details .= "Details: $details <br>\n";
	$alert_details .= "Area: $area <br>\n";

	if($type=="l2") { $type="L2 SD-WAN"; }
	else if($type=="l3") { $type="L3 SD-WAN"; }
	else if($type=="mptcp") { $type="MPTCP"; }
	$alert_details .= "Type: $type <br>\n";
	
	$alert_details .= "CORE: $core_ipaddr <br>\n";
	return $alert_details;
}


?>
