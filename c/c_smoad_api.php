<?php

function sm_wg_print_tunnel_details($db, $smoad_wg_peers_id)
{	
	$query = "select id, type, port, details, license, serialnumber, pubkey, prikey, allowedipsubnet, status, enable, updated from smoad_wg_peers
					where id=$smoad_wg_peers_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$port = $row['port'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$pubkey = $row['pubkey']; $prikey = $row['prikey'];			
			$allowedipsubnet = $row['allowedipsubnet'];
			$status = $row['status'];
			$enable = $row['enable'];
			$updated = $row['updated'];
	
			print "<div style=\"font-family:courier;\">";
			print "ID: $id <br>";
			print "<br>";
			print "Details: $details <br>";
			print "License: $license <br>";
			print "Serial Number: $serialnumber <br>";
	
			print "<br>";
			print "Allowed IP Subnet: $allowedipsubnet <br>";
			print "Prikey: $prikey <br>";
			
			print "<br>";
			print "Status: $status <br>";
			print "Enable: $enable <br>";
			print "</div>";
		}
	}
}

function sm_get_device_provision_script_by_serialnumber($db, $serialnumber)
{
	$query = "select id from smoad_devices where serialnumber=\"$serialnumber\"";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $id = $row['id']; } }
	return sm_wg_get_device_provision_script($db, $id);
}


function sm_wg_get_device_provision_script_openwrt($db, $device_id)
{	
	$query = "select id, details, license, serialnumber, model_variant, root_password, superadmin_password,  
		sdwan_server_id, sdwan_proto from smoad_devices where id=$device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model_variant = $row['model_variant'];
			$root_password = $row['root_password'];
			$superadmin_password = $row['superadmin_password'];
			$sdwan_server_id = $row['sdwan_server_id'];
			$sdwan_proto = $row['sdwan_proto'];
		}
	}
	
	$output_script .= "uci set smoad.device.details=\"$details\"\n";
	$output_script .= "uci set smoad.device.serial=\"$serialnumber\"\n";
	$output_script .= "uci set smoad.device.license=\"$license\"\n";
	$output_script .= "uci set smoad.device.gw_server=\"notset\"\n";
	$output_script .= "uci set smoad.device.sdwan_proto=\"$sdwan_proto\"\n";
	$output_script .= "uci set smoad.device.wg=\"0\"\n"; //disable initially wg
	$output_script .= "uci set network.wg0.addresses=\"0.0.0.0/0\"\n";
	$output_script .= "uci set network.wg0.private_key=\"notset\"\n"; //device prikey
	$output_script .= "uci set network.@wireguard_wg0[0].public_key=\"notset\"\n"; //server pubkey
	if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "uci set network.vxlan0.peeraddr=\"0.0.0.0\"\n"; }
	$output_script .= "uci set smoad.device.wgaddresses=\"0.0.0.0\"\n";
	if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "uci set network.vxlan0.vid=\"0\"\n"; }
	$output_script .= "uci set smoad.login.superadmin_pass=\"$superadmin_password\"\n";
	$output_script .= "uci commit network\n";
	$output_script .= "uci commit smoad\n";
	$output_script .= "uci commit\n";
	$output_script .= "ifup wg0\n";
	$output_script .= "echo -e \"".$root_password."\\n".$root_password."\" | passwd root > /dev/null\n";
	if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "ifup vxlan0\n"; }

	if($sdwan_server_id!=null && $sdwan_server_id!="notset")
	{	
		if($sdwan_proto=="wg")
		{	$query = "select serialnumber, pubkey, address, mtu, ipaddr from smoad_sdwan_servers where id=$sdwan_server_id"; 
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc()) 
				{	$gw_server_serialnumber = $row['serialnumber'];
					$gw_server_pubkey = $row['pubkey'];
					$gw_server_wg_address = $row['address'];
					$gw_server_mtu = $row['mtu'];
					$gw_server_ipaddr = $row['ipaddr'];
				}
			}
			
			$output_script .= "uci set smoad.device.gw_server=\"$gw_server_ipaddr\"\n";
			$output_script .= "uci set network.@wireguard_wg0[0].endpoint_host=\"$gw_server_ipaddr\"\n";
			$output_script .= "uci set smoad.device.wg=\"1\"\n"; //enable wg
			
			$query = "select id, id_peer, details, license, device_serialnumber, pubkey, prikey, allowedipsubnet, vlan_id, vxlan_id 
						from smoad_sds_wg_peers where serialnumber=\"$gw_server_serialnumber\" and device_serialnumber=\"$serialnumber\""; 
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc())
				{	$id = $row['id'];
					$port = $row['port'];
					$details = $row['details'];
					$license = $row['license'];
					$serialnumber = $row['serialnumber'];
					$pubkey = $row['pubkey']; $prikey = $row['prikey'];			
					$allowedipsubnet = $row['allowedipsubnet'];
					$vxlan_id = $row['vxlan_id'];
					$status = $row['status'];
					$enable = $row['enable'];
					$updated = $row['updated'];
			
					$allowedipsubnet = str_replace("/32", "/16", $allowedipsubnet);
					$output_script .= "uci set network.wg0.addresses=\"$allowedipsubnet\"\n";
					$output_script .= "uci set network.wg0.private_key=\"$prikey\"\n"; //device prikey
					$output_script .= "uci set network.@wireguard_wg0[0].public_key=\"$gw_server_pubkey\"\n"; //server pubkey
					
					$temp = explode("/", $gw_server_wg_address);
					$gw_server_wg_address = $temp[0];
					if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "uci set network.vxlan0.peeraddr=\"$gw_server_wg_address\"\n"; }
					$output_script .= "uci set smoad.device.wgaddresses=\"$allowedipsubnet\"\n";
					$output_script .= "uci set smoad.device.sdwan_mtu=\"$gw_server_mtu\"\n";
					if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "uci set network.vxlan0.vid=\"$vxlan_id\"\n"; }
					$output_script .= "uci commit network\n";
					$output_script .= "uci commit smoad\n";
					$output_script .= "uci commit\n";
					$output_script .= "ifup wg0\n";
					if($model_variant=="l2" || $model_variant=="l2w1l2") { $output_script .= "ifup vxlan0\n"; }
				}
			}
		}
	}

	return $output_script;
}

function sm_wg_get_device_provision_script_ubuntu($db, $device_id)
{	
	$query = "select id, details, license, serialnumber, model_variant, root_password, superadmin_password,  
		sdwan_server_id, sdwan_proto from smoad_devices where id=$device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model_variant = $row['model_variant'];
			$root_password = $row['root_password'];
			$superadmin_password = $row['superadmin_password'];
			$sdwan_server_id = $row['sdwan_server_id'];
			$sdwan_proto = $row['sdwan_proto'];
		}
	}

	$output_script .= "smoad.device.details=\"$details\"\n";
	$output_script .= "smoad.device.serial=\"$serialnumber\"\n";
	$output_script .= "smoad.device.license=\"$license\"\n";
	$output_script .= "smoad.device.gw_server=\"notset\"\n";
	$output_script .= "smoad.device.sdwan_proto=\"$sdwan_proto\"\n";
	$output_script .= "smoad.device.wg=\"0\"\n"; //disable initially wg
	$output_script .= "smoad.login.superadmin_pass=\"$superadmin_password\"\n";
	//$output_script .= "echo -e \"".$root_password."\\n".$root_password."\" | passwd root > /dev/null\n"; //disable root pass for now for Ubuntu EDGE

	if($sdwan_server_id!=null && $sdwan_server_id!="notset")
	{	
		if($sdwan_proto=="wg")
		{	$query = "select serialnumber, pubkey, address, mtu, ipaddr from smoad_sdwan_servers where id=$sdwan_server_id"; 
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc()) 
				{	$gw_server_serialnumber = $row['serialnumber'];
					$gw_server_pubkey = $row['pubkey'];
					$gw_server_wg_address = $row['address'];
					$gw_server_mtu = $row['mtu'];
					$gw_server_ipaddr = $row['ipaddr'];
				}
			}
		
			$output_script .= "smoad.device.wg=\"1\"\n"; //enable wg
			$output_script .= "smoad.device.gw_server=\"$gw_server_ipaddr\"\n";
			
			$query = "select id, id_peer, details, license, device_serialnumber, pubkey, prikey, allowedipsubnet, vlan_id, vxlan_id 
						from smoad_sds_wg_peers where serialnumber=\"$gw_server_serialnumber\" and device_serialnumber=\"$serialnumber\""; 
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc())
				{	$id = $row['id'];
					$port = $row['port'];
					$details = $row['details'];
					$license = $row['license'];
					$serialnumber = $row['serialnumber'];
					$pubkey = $row['pubkey']; $prikey = $row['prikey'];			
					$allowedipsubnet = $row['allowedipsubnet'];
					$vxlan_id = $row['vxlan_id'];
					$status = $row['status'];
					$enable = $row['enable'];
					$updated = $row['updated'];
					$prikey = bin2hex($prikey);
					$gw_server_pubkey = bin2hex($gw_server_pubkey);
					$allowedipsubnet = str_replace("/32", "/16", $allowedipsubnet);
					$output_script .= "smoad.device.wgaddresses=\"$allowedipsubnet\"\n";
					$output_script .= "smoad.device.wg_private_key=\"$prikey\"\n"; //device prikey
					$output_script .= "smoad.device.wg_public_key=\"$gw_server_pubkey\"\n"; //server pubkey
					$output_script .= "smoad.device.sdwan_mtu=\"$gw_server_mtu\"\n";
				}
			}
		}
	}

	return $output_script;
}


function sm_wg_get_device_provision_script($db, $device_id)
{	$output_script = null;
	$query = "select os from smoad_devices where id=$device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$os = $row['os'];
			if($os=="openwrt") { $output_script = sm_wg_get_device_provision_script_openwrt($db, $device_id); }
			else if($os=="ubuntu") { $output_script = sm_wg_get_device_provision_script_ubuntu($db, $device_id); }
		}
	}

	return $output_script;
}


function sm_get_device_jobs_by_serialnumber($db, $serialnumber)
{	$output_script = "";
	$query = "select id, job from smoad_device_jobs where device_serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$job = $row['job'];
			$output_script .= "$job\n";
			$query2 = "delete from smoad_device_jobs where id=\"$id\"";
			$db->query($query2);
		}
	}

	return $output_script;
}


//get ZTP Device->SM Server config and update/sync the same in the SMOAD Server
function sm_get_device_config_by_serialnumber($db, $serialnumber, $config)
{	
	//wait till all pending jobs for this device gets complete
	//first count if there are any pending jobs to be executed for this device, if any jobs are there then
    /// dont sync Device->SM config
   $pending_jobs = 0;
   $query = "select count(*) pending_jobs from smoad_device_jobs where device_serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{ while($row = $res->fetch_assoc()) { $pending_jobs = $row['pending_jobs']; } }
	if($pending_jobs>0) { return "success"; }

	$lines = explode("\n", $config);
	foreach($lines as $line) 
	{  $line = chop($line);
	 	$type = ""; $value = "";
		if(strpos($line , "=")!== false)
		{	$list = explode("=", $line);
			$type=$list[0]; $type = chop($type);
			$value=$list[1]; $value = chop($value);
			$value = str_replace("^", "", $value);
		}
		
		$query=null;
		
		//Generic SMOAD Device
		if($query==null) { $query=sm_get_device_sm_config_by_serialnumber($serialnumber, $type, $value); }
		
		//LAN
		if($query==null) { $query=sm_get_device_lan_config_by_serialnumber($serialnumber, $type, $value, "lan"); }
		if($query==null) { $query=sm_get_device_lan_config_by_serialnumber($serialnumber, $type, $value, "lan2"); }
		if($query==null) { $query=sm_get_device_lan_config_by_serialnumber($serialnumber, $type, $value, "lan3"); }
		
		//WAN
		if($query==null) { $query=sm_get_device_wan_config_by_serialnumber($serialnumber, $type, $value, "wan"); }
		if($query==null) { $query=sm_get_device_wan_config_by_serialnumber($serialnumber, $type, $value, "wan2"); }
		
		//SDWAN
		if($query==null) { $query=sm_get_device_sdwan_config_by_serialnumber($serialnumber, $type, $value); }
		
		//QoS
		if($query==null) { $query=sm_get_device_qos_config_by_serialnumber($serialnumber, $type, $value); }
		
		//LTE
		if($query==null) { $query=sm_get_device_lte_config_by_serialnumber($serialnumber, $type, $value, "lte1"); }
		if($query==null) { $query=sm_get_device_lte_config_by_serialnumber($serialnumber, $type, $value, "lte2"); }
		if($query==null) { $query=sm_get_device_lte_config_by_serialnumber($serialnumber, $type, $value, "lte3"); }
		
		//WiFi
		if($query==null) { $query=sm_get_device_wifi_config_by_serialnumber($serialnumber, $type, $value); }

		if($query!=null) { $db->query($query); }
	}
		
	return "success";
	
} /* sm_get_device_config_by_serialnumber */

//get ZTP GW->SM Server config and update/sync the same in the SMOAD Server
function sm_get_sds_config_by_serialnumber($db, $serialnumber, $config)
{	
	//wait till all pending jobs for this gw gets complete
	//first count if there are any pending jobs to be executed for this device, if any jobs are there then
    /// dont sync Device->SM config
   $pending_jobs = 0;
   $query = "select count(*) pending_jobs from smoad_sdwan_server_jobs where sds_serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{ while($row = $res->fetch_assoc()) { $pending_jobs = $row['pending_jobs']; } }
	if($pending_jobs>0) { return "success"; }

	$lines = explode("\n", $config);
	foreach($lines as $line) 
	{  $line = chop($line);
	 	$type = ""; $value = "";
		if(strpos($line , "=")!== false)
		{	$list = explode("=", $line);
			$type=$list[0]; $type = chop($type);
			$value=$list[1]; $value = chop($value);
			$value = str_replace("^", "", $value);
		}

		$query=null;
		
		//WG Peers
		if($query==null) { $query=sm_get_sds_wg_peers_config_by_serialnumber($db, $serialnumber, $type, $value); }
		
		//WG Server
		if($query==null) { $query=sm_get_sds_wg_server_config_by_serialnumber($serialnumber, $type, $value); }
		
		//gw qos logs
		if($query==null) { $query=sm_get_sds_gw_qos_logs($serialnumber, $type, $value); }

		if($query!=null) 
		{  $db->query($query); 
		}
	}
		
	return "success";
	
} /* sm_get_sds_config_by_serialnumber */

function sm_get_device_api_pubkey_by_serialnumber($db, $serialnumber)
{	$query = "select api_pubkey, api_pubkey_new, api_prikey, api_prikey_new from smoad_devices where serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$api_pubkey = $row['api_pubkey'];
			$api_pubkey_new = $row['api_pubkey_new'];
			$api_prikey = $row['api_prikey'];
			$api_prikey_new = $row['api_prikey_new'];
			
			//key changed by cron job script ? then update locally
			/*
			if($api_pubkey!=$api_pubkey_new)
			{	$new_key_device_sync_timestamp = date("Y/m/d - H:i:s"); 
				$query2 = "update smoad_devices set api_pubkey=\"$api_pubkey_new\",api_prikey=\"$api_prikey_new\", 
					new_key_device_sync_timestamp=\"$new_key_device_sync_timestamp\" where serialnumber=\"$serialnumber\"";
				$db->query($query2);
				$api_pubkey = $api_pubkey_new;
			}*/
			return $api_pubkey;
		}
	}
	return null;
}

function sm_get_device_api_device_prikey_by_serialnumber($db, $serialnumber)
{	$query = "select api_pubkey, api_pubkey_new, api_prikey, api_prikey_new from smoad_devices where serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$api_pubkey = $row['api_pubkey'];
			$api_pubkey_new = $row['api_pubkey_new'];
			$api_prikey = $row['api_prikey'];
			$api_prikey_new = $row['api_prikey_new'];
			
			//key changed by cron job script ? then update locally
			/*
			if($api_pubkey!=$api_pubkey_new)
			{	$new_key_device_sync_timestamp = date("Y/m/d - H:i:s"); 
				$query2 = "update smoad_devices set api_pubkey=\"$api_pubkey_new\",api_prikey=\"$api_prikey_new\", 
					new_key_device_sync_timestamp=\"$new_key_device_sync_timestamp\" where serialnumber=\"$serialnumber\"";
				$db->query($query2);
				$api_pubkey = $api_pubkey_new;
			}*/
			return $api_prikey;
		}
	}
	return null;
}


?>