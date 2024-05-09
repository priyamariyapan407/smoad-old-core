<br>
<?php

$target_dir = "/www/smoad";
//$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . "/edge_upload.config";
$file_upload = true;
$file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));


function _install_wan_config_template_to_edge($db, $template_id, $port)
{	$G_device_serialnumber = $GLOBALS['G_device_serialnumber'];
	$query = "select $port"."_proto _wan_proto, $port"."_ipaddr _wan_ipaddr, $port"."_netmask _wan_netmask, 
				$port"."_gateway _wan_gateway, $port"."_dns _wan_dns, $port"."_username _wan_username, $port"."_password _wan_password,
				$port"."_max_bandwidth _wan_max_bandwidth, $port"."_medium_bandwidth_pct _wan_medium_bandwidth_pct, $port"."_low_bandwidth_pct _wan_low_bandwidth_pct 
				from smoad_device_templates where id=$template_id ";
	//print "$query <br>";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$query2 = "select id from smoad_device_network_cfg where device_serialnumber='$G_device_serialnumber'";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id']; }
			}
			
			$wan_proto = $row['_wan_proto'];
			db_api_set_value($db, $wan_proto, $port."_proto", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".proto=^".$wan_proto."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_ipaddr = $row['_wan_ipaddr'];
			db_api_set_value($db, $wan_ipaddr, $port."_ipaddr", "smoad_device_network_cfg", $id, "char");
		   $job = "uci set network.".$port.".ipaddr=^".$wan_ipaddr."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job); 
			 
			$wan_netmask = $row['_wan_netmask'];
			db_api_set_value($db, $wan_netmask, $port."_netmask", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".netmask=^".$wan_netmask."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);

			$wan_gateway = $row['_wan_gateway'];
			db_api_set_value($db, $wan_gateway, $port."_gateway", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".gateway=^".$wan_gateway."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_dns = $row['_wan_dns'];
			db_api_set_value($db, $wan_dns, $port."_dns", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".dns=^".$wan_dns."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_username = $row['_wan_username'];
			db_api_set_value($db, $wan_username, $port."_username", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".username=^".$wan_username."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_password = $row['_wan_password'];
			db_api_set_value($db, $wan_password, $port."_password", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".password=^".$wan_password."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_max_bandwidth = $row['_wan_max_bandwidth'];
			db_api_set_value($db, $wan_max_bandwidth, $port."_max_bandwidth", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_max_bandwidth=^".$wan_max_bandwidth."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_medium_bandwidth_pct = $row['_wan_medium_bandwidth_pct'];
			db_api_set_value($db, $wan_medium_bandwidth_pct, $port."_medium_bandwidth_pct", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_medium_bandwidth_pct=^".$wan_medium_bandwidth_pct."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wan_low_bandwidth_pct = $row['_wan_low_bandwidth_pct'];
			db_api_set_value($db, $wan_low_bandwidth_pct, $port."_low_bandwidth_pct", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_low_bandwidth_pct=^".$wan_low_bandwidth_pct."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
		}
	}
}

function _install_lte_config_template_to_edge($db, $template_id, $port)
{	$G_device_serialnumber = $GLOBALS['G_device_serialnumber'];
	$query = "select $port"."_ipaddr _lte_ipaddr, $port"."_netmask _lte_netmask, $port"."_gateway _lte_gateway,
				$port"."_max_bandwidth _lte_max_bandwidth, $port"."_medium_bandwidth_pct _lte_medium_bandwidth_pct, $port"."_low_bandwidth_pct _lte_low_bandwidth_pct 
				from smoad_device_templates where id=$template_id ";
	//print "$query <br>";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$query2 = "select id from smoad_device_network_cfg where device_serialnumber='$G_device_serialnumber'";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id']; }
			}
			
			$lte_ipaddr = $row['_lte_ipaddr'];
			db_api_set_value($db, $lte_ipaddr, $port."_ipaddr", "smoad_device_network_cfg", $id, "char");
		   $job = "uci set network.".$port.".ipaddr=^".$lte_ipaddr."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job); 
			 
			$lte_netmask = $row['_lte_netmask'];
			db_api_set_value($db, $lte_netmask, $port."_netmask", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".netmask=^".$lte_netmask."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);

			$lte_gateway = $row['_lte_gateway'];
			db_api_set_value($db, $lte_gateway, $port."_gateway", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.".$port.".gateway=^".$lte_gateway."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$lte_max_bandwidth = $row['_lte_max_bandwidth'];
			db_api_set_value($db, $lte_max_bandwidth, $port."_max_bandwidth", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_max_bandwidth=^".$lte_max_bandwidth."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$lte_medium_bandwidth_pct = $row['_lte_medium_bandwidth_pct'];
			db_api_set_value($db, $lte_medium_bandwidth_pct, $port."_medium_bandwidth_pct", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_medium_bandwidth_pct=^".$lte_medium_bandwidth_pct."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$lte_low_bandwidth_pct = $row['_lte_low_bandwidth_pct'];
			db_api_set_value($db, $lte_low_bandwidth_pct, $port."_low_bandwidth_pct", "smoad_device_network_cfg", $id, "num");
		  	$job = "uci set smoad.qos.".$port."_low_bandwidth_pct=^".$lte_low_bandwidth_pct."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
		}
	}
}


$_command = $_POST['command'];
if($_command=="file_upload")
{
	/*
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	  if($check !== false) {
	    echo "File is an image - " . $check["mime"] . ".";
	    $file_upload = true;
	  } else {
	    echo "File is not an image.";
	    $file_upload = false;
	  }
	}
	
	// Check if file already exists
	if(file_exists($target_file)) 
	{	print "Sorry, file already exists.";
	  $file_upload = false;
	}*/
	
	
	// Check file size
	if($_FILES["fileToUpload"]["size"] > 5000)
	{
		print "<pre>ERROR: File is too large.</pre>";
		$file_upload = false;
	}
	
	/*
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
	  echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	  $file_upload = false;
	}*/
	
	
	if($file_type != "config") 
	{	print "<pre>ERROR: only .config files are allowed.</pre>";
		$file_upload = false;
	}
	
	// Check if $uploadOk is set to 0 by an error
	if ($file_upload == false) {
		print "<pre>ERROR: file was not uploaded.</pre>";
	// if everything is ok, try to upload file
	} else {
	  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		print "<pre>SUCCESS: The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.</pre>";
	  } else {	print "<pre>ERROR: there was an error uploading your file.</pre>";	}
	}
}
else if($_command=="add_device_template")
{	$template_details = $_POST['template_details'];
	if($template_details==null) { print "<pre>ERROR: Please enter template details !</pre>"; }
	else 
	{	$query = "select id, details, license, serialnumber, model, model_variant, root_password, superadmin_password, firmware, area, 
				sdwan_server_ipaddr, sdwan_proto, vlan_id, customer_id 
				from smoad_devices where id=$G_device_id"; 
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
			}
		}
		$query = "select lan_ipaddr, lan_netmask, 
				wan_proto, wan_ipaddr, wan_netmask, wan_gateway, wan_dns, wan_username, wan_password,
				wan_max_bandwidth, wan_medium_bandwidth_pct, wan_low_bandwidth_pct,
				wan2_proto, wan2_ipaddr, wan2_netmask, wan2_gateway, wan2_dns, wan2_username, wan2_password,
				wan2_max_bandwidth, wan2_medium_bandwidth_pct, wan2_low_bandwidth_pct,
				wan3_proto, wan3_ipaddr, wan3_netmask, wan3_gateway, wan3_dns, wan3_username, wan3_password,
				wan3_max_bandwidth, wan3_medium_bandwidth_pct, wan3_low_bandwidth_pct,
				lte1_ipaddr, lte1_netmask, lte1_gateway,
				lte1_max_bandwidth, lte1_medium_bandwidth_pct, lte1_low_bandwidth_pct,
				lte2_ipaddr, lte2_netmask, lte2_gateway, 
				lte2_max_bandwidth, lte2_medium_bandwidth_pct, lte2_low_bandwidth_pct,
				lte3_ipaddr, lte3_netmask, lte3_gateway,
				lte3_max_bandwidth, lte3_medium_bandwidth_pct, lte3_low_bandwidth_pct,
				wireless_ssid, wireless_key, wireless_encryption, wireless_auth_server, wireless_auth_secret,
				aggpolicy_mode, aggpolicy,
				sdwan_link_high_usage_threshold
				from smoad_device_network_cfg where device_serialnumber='$G_device_serialnumber'";
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$lan_ipaddr = $row['lan_ipaddr']; $lan_netmask = $row['lan_netmask'];
			
				$wan_proto = $row['wan_proto'];
				$wan_ipaddr = $row['wan_ipaddr'];
				$wan_netmask = $row['wan_netmask'];
				$wan_gateway = $row['wan_gateway'];
				$wan_dns = $row['wan_dns'];
				$wan_username = $row['wan_username'];
				$wan_password = $row['wan_password'];
				$wan_max_bandwidth = $row['wan_max_bandwidth']; 
				$wan_medium_bandwidth_pct = $row['wan_medium_bandwidth_pct'];
				$wan_low_bandwidth_pct = $row['wan_low_bandwidth_pct'];
				
				$wan2_proto = $row['wan2_proto'];
				$wan2_ipaddr = $row['wan2_ipaddr'];
				$wan2_netmask = $row['wan2_netmask'];
				$wan2_gateway = $row['wan2_gateway'];
				$wan2_dns = $row['wan2_dns'];
				$wan2_username = $row['wan2_username'];
				$wan2_password = $row['wan2_password'];
				$wan2_max_bandwidth = $row['wan2_max_bandwidth']; 
				$wan2_medium_bandwidth_pct = $row['wan2_medium_bandwidth_pct'];
				$wan2_low_bandwidth_pct = $row['wan2_low_bandwidth_pct'];
				
				$wan3_proto = $row['wan3_proto'];
				$wan3_ipaddr = $row['wan3_ipaddr'];
				$wan3_netmask = $row['wan3_netmask'];
				$wan3_gateway = $row['wan3_gateway'];
				$wan3_dns = $row['wan3_dns'];
				$wan3_username = $row['wan3_username'];
				$wan3_password = $row['wan3_password'];
				$wan3_max_bandwidth = $row['wan3_max_bandwidth']; 
				$wan3_medium_bandwidth_pct = $row['wan3_medium_bandwidth_pct'];
				$wan3_low_bandwidth_pct = $row['wan3_low_bandwidth_pct'];
				
				$lte1_ipaddr = $row['lte1_ipaddr']; $lte1_netmask = $row['lte1_netmask']; $lte1_gateway = $row['lte1_gateway'];
				$lte1_max_bandwidth = $row['lte1_max_bandwidth']; 
				$lte1_medium_bandwidth_pct = $row['lte1_medium_bandwidth_pct'];
				$lte1_low_bandwidth_pct = $row['lte1_low_bandwidth_pct'];
				
				$lte2_ipaddr = $row['lte2_ipaddr']; $lte2_netmask = $row['lte2_netmask']; $lte2_gateway = $row['lte2_gateway'];
				$lte2_max_bandwidth = $row['lte2_max_bandwidth']; 
				$lte2_medium_bandwidth_pct = $row['lte2_medium_bandwidth_pct'];
				$lte2_low_bandwidth_pct = $row['lte2_low_bandwidth_pct'];
				
				$lte3_ipaddr = $row['lte3_ipaddr']; $lte3_netmask = $row['lte3_netmask']; $lte3_gateway = $row['lte3_gateway'];
				$lte3_max_bandwidth = $row['lte3_max_bandwidth']; 
				$lte3_medium_bandwidth_pct = $row['lte3_medium_bandwidth_pct'];
				$lte3_low_bandwidth_pct = $row['lte3_low_bandwidth_pct'];
				
				$wireless_ssid = $row['wireless_ssid']; $wireless_key = $row['wireless_key']; $wireless_encryption = $row['wireless_encryption']; $wireless_auth_server = $row['wireless_auth_server']; $wireless_auth_secret = $row['wireless_auth_secret'];
				$aggpolicy_mode = $row['aggpolicy_mode']; $aggpolicy = $row['aggpolicy'];
				
				$sdwan_link_high_usage_threshold = $row['sdwan_link_high_usage_threshold'];
				
				//$ = $row[''];
			}
		}
		
		$query = "insert into smoad_device_templates 
						(template_details, details, model, model_variant, area, sdwan_proto, customer_id,
						 lan_ipaddr, lan_netmask, 
						 wan_proto, wan_ipaddr, wan_netmask, wan_gateway, wan_dns, wan_username, wan_password,
						 wan_max_bandwidth, wan_medium_bandwidth_pct, wan_low_bandwidth_pct,
						 wan2_proto, wan2_ipaddr, wan2_netmask, wan2_gateway, wan2_dns, wan2_username, wan2_password,
						 wan2_max_bandwidth, wan2_medium_bandwidth_pct, wan2_low_bandwidth_pct,
						 wan3_proto, wan3_ipaddr, wan3_netmask, wan3_gateway, wan3_dns, wan3_username, wan3_password,
						 wan3_max_bandwidth, wan3_medium_bandwidth_pct, wan3_low_bandwidth_pct,
						 lte1_ipaddr, lte1_netmask, lte1_gateway,
						 lte1_max_bandwidth, lte1_medium_bandwidth_pct, lte1_low_bandwidth_pct,
						 lte2_ipaddr, lte2_netmask, lte2_gateway,
						 lte2_max_bandwidth, lte2_medium_bandwidth_pct, lte2_low_bandwidth_pct, 
						 lte3_ipaddr, lte3_netmask, lte3_gateway,
						 lte3_max_bandwidth, lte3_medium_bandwidth_pct, lte3_low_bandwidth_pct,
						 wireless_ssid, wireless_key, wireless_encryption, wireless_auth_server, wireless_auth_secret, 
						 aggpolicy_mode, aggpolicy,
						 sdwan_link_high_usage_threshold
						) 
						values (\"$template_details\", \"$details\", \"$model\", \"$model_variant\", \"$area\", \"$sdwan_proto\", \"$customer_id\",
						 \"$lan_ipaddr\", \"$lan_netmask\", 
						 \"$wan_proto\", \"$wan_ipaddr\", \"$wan_netmask\", \"$wan_gateway\", \"$wan_dns\", \"$wan_username\", \"$wan_password\",
						 $wan_max_bandwidth, $wan_medium_bandwidth_pct, $wan_low_bandwidth_pct,
						 \"$wan2_proto\", \"$wan2_ipaddr\", \"$wan2_netmask\", \"$wan2_gateway\", \"$wan2_dns\", \"$wan2_username\", \"$wan2_password\",
						 $wan2_max_bandwidth, $wan2_medium_bandwidth_pct, $wan2_low_bandwidth_pct,
						 \"$wan3_proto\", \"$wan3_ipaddr\", \"$wan3_netmask\", \"$wan3_gateway\", \"$wan3_dns\", \"$wan3_username\", \"$wan3_password\",
						 $wan3_max_bandwidth, $wan3_medium_bandwidth_pct, $wan3_low_bandwidth_pct,
						 \"$lte1_ipaddr\", \"$lte1_netmask\", \"$lte1_gateway\",
						 $lte1_max_bandwidth, $lte1_medium_bandwidth_pct, $lte1_low_bandwidth_pct,
						 \"$lte2_ipaddr\", \"$lte2_netmask\", \"$lte2_gateway\",
						 $lte2_max_bandwidth, $lte2_medium_bandwidth_pct, $lte2_low_bandwidth_pct,
						 \"$lte3_ipaddr\", \"$lte3_netmask\", \"$lte3_gateway\",
						 $lte3_max_bandwidth, $lte3_medium_bandwidth_pct, $lte3_low_bandwidth_pct,
						 \"$wireless_ssid\", \"$wireless_key\", \"$wireless_encryption\", \"$wireless_auth_server\", \"$wireless_auth_secret\",
						 \"$aggpolicy_mode\", \"$aggpolicy\",
						 \"$sdwan_link_high_usage_threshold\"
						)";
		$db->query($query);
		print "<pre>SUCCESS: Edge config successfully added as a device template !</pre>";
	}
}
else if($_command=="install_dev_config_template")
{	$template_id = $_POST['template_id'];
	$query = "select details, area, sdwan_proto, customer_id,
						 lan_ipaddr, lan_netmask, 
						 wireless_ssid, wireless_key, wireless_encryption, wireless_auth_server, wireless_auth_secret, 
						 aggpolicy_mode, aggpolicy,
						 sdwan_link_high_usage_threshold
				from smoad_device_templates where id=$template_id ";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$query2 = "select id from smoad_device_network_cfg where device_serialnumber='$G_device_serialnumber'";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id']; }
			}
		
			//Device
			$details = $row['details'];
			db_api_set_value($db, $details, "details", "smoad_devices", $G_device_id, "char"); 
			$job = "uci set smoad.device.details=^".$details."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			$_SESSION['ztp_dev_details']=$details;
			
			$area = $row['area'];
			db_api_set_value($db, $area, "area", "smoad_devices", $G_device_id, "char"); 
			$job = "uci set smoad.device.area=^".$area."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);

			//LAN			
			$lan_ipaddr = $row['lan_ipaddr'];
			db_api_set_value($db, $lan_ipaddr, "lan_ipaddr", "smoad_device_network_cfg", $id, "char");
		   $job = "uci set network.lan.ipaddr=^".$lan_ipaddr."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job); 
			 
			$lan_netmask = $row['lan_netmask'];
			db_api_set_value($db, $lan_netmask, "lan_netmask", "smoad_device_network_cfg", $id, "char");
		  	$job = "uci set network.lan.netmask=^".$lan_netmask."^";
			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			//Wireless			
			$wireless_ssid = $row['wireless_ssid'];
			db_api_set_value($db, $wireless_ssid, "wireless_ssid", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.ssid=^".$wireless_ssid."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			$wireless_key = $row['wireless_key'];
			db_api_set_value($db, $wireless_key, "wireless_key", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.key=^".$wireless_key."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			$wireless_key = $row['wireless_key'];
			db_api_set_value($db, $wireless_key, "wireless_key", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.key=^".$wireless_key."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			$wireless_encryption = $row['wireless_encryption'];
			db_api_set_value($db, $wireless_encryption, "wireless_encryption", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.encryption=^".$wireless_encryption."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
			
			$wireless_auth_server = $row['wireless_auth_server'];
			db_api_set_value($db, $wireless_auth_server, "wireless_auth_server", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.auth_server=^".$wireless_auth_server."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			$wireless_auth_secret = $row['wireless_auth_secret'];
			db_api_set_value($db, $wireless_auth_secret, "wireless_auth_secret", "smoad_device_network_cfg", $id, "char");
			$job = "uci set wireless.default_radio0.auth_secret=^".$wireless_auth_secret."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			//Agg
  			$aggpolicy_mode = $row['aggpolicy_mode'];
			db_api_set_value($db, $aggpolicy_mode, "aggpolicy_mode", "smoad_device_network_cfg", $id, "char");
			$job = "uci set smoad.device.aggpolicy_mode=^".$aggpolicy_mode."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job); 
			
			$aggpolicy = $row['aggpolicy'];
			db_api_set_value($db, $aggpolicy, "aggpolicy", "smoad_device_network_cfg", $id, "char");
			$job = "uci set smoad.device.aggpolicy=^".$aggpolicy."^";
  			sm_ztp_add_job($db, $G_device_serialnumber, $job);
  			
  			//SDWAN
  			$sdwan_link_high_usage_threshold = $row['sdwan_link_high_usage_threshold'];
			db_api_set_value($db, $sdwan_link_high_usage_threshold, "sdwan_link_high_usage_threshold", "smoad_device_network_cfg", $id, "num");
			
		}
	}
	_install_wan_config_template_to_edge($db, $template_id, "wan");
	_install_wan_config_template_to_edge($db, $template_id, "wan2");
	_install_wan_config_template_to_edge($db, $template_id, "wan3");
	
	_install_lte_config_template_to_edge($db, $template_id, "lte1");
	_install_lte_config_template_to_edge($db, $template_id, "lte2");
	_install_lte_config_template_to_edge($db, $template_id, "lte3");
	
	print "<pre>SUCCESS: Device template successfully installed as the Edge config. Edge will reprovision shortly !</pre>";
}

system("php -f /usr/local/smoad/scripts/save_edge_config.php $G_device_serialnumber > /dev/null");

print '<table class="config_settings" style="width:600px;">';
/*
print "<tr><td>Upload config</td><td><form action=\"$curr_page\" method=\"post\" enctype=\"multipart/form-data\">
	<input type=\"hidden\" name=\"command\" value=\"file_upload\" />
  <input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">
  <input type=\"submit\" value=\"Upload file\" name=\"submit\">
</form></td></tr>";*/
print "<tr><td>Download config</td><td><a href=\"edge.config\" download=\"SMOAD_edge_".$G_device_serialnumber.".conf\"><img src=\"i/download.png\" 
		title=\"Download configuration file for the EDGE: $G_device_serialnumber !\" /></a></td></tr>";
print '</table><br>';

print '<p><strong>Add Edge Config as Template:</strong></p>';
	print '<p>';
	api_form_post($curr_page);
	api_input_hidden("command", "add_device_template");
	print '<table class="list_items2" style="width:1024px;font-size:10px;">';
	api_ui_config_option_text("Template Details", null, "template_details", null, null);
	api_ui_config_option_add(null);
	print '</table>';
	print '</form></p>';
	

print "<br>";

	$_page = $_GET['pagination']; if($_page==null) { $_page=1; }
	$where_clause_customer=null;
	
	$query = "select model, model_variant from smoad_devices where id=$G_device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$model = $row['model']; $model_variant = $row['model_variant']; }
	}
	$where_clause= " where model=\"$model\" and model_variant=\"$model_variant\" ";

	if($login_type=='customer') { $where_clause_customer=" customer_id = $id_customer "; }
	
	if($where_clause_customer!=null) 
	{ if($where_clause==null) { $where_clause=" where "; } //first where
	  $where_clause .= $where_clause_customer;
	}
	
	$total_items=0; $total_pages=0;
	api_ui_pagination_get_total_items_total_pages($db, 'smoad_device_templates', $where_clause, $G_items_per_page, $total_items, $total_pages);
	
	print "<p><strong>Available Config Templates for this model and variant: $total_items</strong></p>";
	api_ui_pagination_get_pagination_table($db, $_page, $total_pages, "index.php?page=dev_config_templates&skey=".$session_key);
	$limitstart = ($_page-1)*$G_items_per_page;
	
?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Template Details</th><th>Details</th><th>Model - Variant</th><th>Area</th><th></th><th></th></tr>

<?php
if($page_redirect==false)
{	$query = "select id, template_details, details, model, model_variant, area, sdwan_server_ipaddr, vlan_id, enable 
				from smoad_device_templates $where_clause and model=\"$model\" and model_variant=\"$model_variant\" order by id desc limit $limitstart".",$G_items_per_page ";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$template_details = $row['template_details'];
			$details = $row['details'];
			$model = $row['model'];
			$model_variant = $row['model_variant'];
			$area = $row['area'];
			$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
			$vlan_id = $row['vlan_id'];
			$enable = $row['enable'];

		
			if($model=="spider") { $_model="Spider"; }
			else if($model=="spider2") { $_model="Spider2"; }
			else if($model=="beetle") { $_model="Beetle"; }
			else if($model=="bumblebee") { $_model="BumbleBee"; }
			else if($model=="vm") { $_model="VM"; }
			else if($model=="soft_client") { $_model="Soft-client"; }
		
			//if($status=='up') { $status="led-green"; } else { $status="led-red"; }
			if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
			else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
			else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
			else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
			print "<tr><td>$id</td><td>$template_details</td><td>$details</td><td>$_model - $_model_variant</td><td>$area</td>";
					 
			
			print "<td>";
			api_form_post($curr_page);
			api_input_hidden("command", "config_device");
			api_input_hidden("device_id", $id);
			api_input_hidden("device_serialnumber", $serialnumber);
			api_input_hidden("device_details", $details);
			api_input_hidden("device_model", $model);
			api_input_hidden("device_model_variant", $model_variant);
			//print "<input type=\"submit\" name=\"submit_ok\" value=\"&#x2699; Configure\" style=\"border:0;\" class=\"a_button_blue\" />";
			print "<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Details\" class=\"top_title_icons\" />";
			print "</form></td>";
			
			if($login_type=='root' || $login_type=='admin')
			{	print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "install_dev_config_template");
				api_input_hidden("template_id", $id);
				//print "<input type=\"submit\" name=\"submit_ok\" value=\"&#x2699; Configure\" style=\"border:0;\" class=\"a_button_blue\" />";
				print "<input type=\"image\" src=\"i/install.png\" alt=\"Details\" title=\"Install\" class=\"top_title_icons\" />";
				print "</form></td>";
			}
			else { print "<td></td>"; }
		
		
			print "</tr>";
		}
	}
}
?>
</table>
<br><br>




