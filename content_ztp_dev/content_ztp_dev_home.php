<?php

$main_table = "smoad_devices";

$_config_update=false;
$_id = $_POST['id'];
$_details = $_POST['details']; $_details_before = $_POST['details_before'];
if($_details_before!=$_details && $_details!=null && $_id!=null) 
{ db_api_set_value($db, $_details, "details", $main_table, $_id, "char");
  $job = "uci set smoad.device.details=^".$_details."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  //update current selected ztp
  $_SESSION['ztp_dev_details']=$_details;
  db_api_set_value($db, $_details, "ztp_dev_details", "profile", 1, "char");
  $_config_update=true;
}

$_area = $_POST['area']; $_area_before = $_POST['area_before'];
if($_area_before!=$_area && $_area!=null && $_id!=null) 
{ db_api_set_value($db, $_area, "area", $main_table, $_id, "char"); 
  $job = "uci set smoad.device.area=^".$_area."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $_config_update=true;
}

$_command = $_POST['command'];
if($_command=="reboot") 
{ $job = "reboot";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  print "<pre>Edge reboot request raised. Edge will reboot shortly !</pre>";
  $_config_update=true;
}
else if($_command=="reprovision") 
{ $output_script = sm_wg_get_device_provision_script($db, $G_device_id);
  $lines = explode("\n", $output_script);
  foreach($lines as $line) 
  { $line = chop($line);
	 sm_ztp_add_job($db, $G_device_serialnumber, $line);
  }
  
  print "<pre>Edge reprovision request raised. Edge will reprovision shortly !</pre>";
  $_config_update=true;
}
else if($_command=="reset_sdwan") 
{ 
  sm_ztp_add_job($db, $G_device_serialnumber, "ifdown wg0");
  sm_ztp_add_job($db, $G_device_serialnumber, "ifup wg0");
  sm_ztp_add_job($db, $G_device_serialnumber, "ifdown vxlan0");
  
  sm_ztp_add_job($db, $G_device_serialnumber, "ifdown vx_vlan_br0");
  sm_ztp_add_job($db, $G_device_serialnumber, "ifup vxlan0");
  sm_ztp_add_job($db, $G_device_serialnumber, "ifup vx_vlan_br0");
  sm_ztp_add_job($db, $G_device_serialnumber, "ifconfig wg0 mtu 1280"); //later make it dynamic
  
  print "<pre>Edge reset SDWAN request raised. Edge will reset SDWAN service shortly !</pre>";
  $_config_update=true;
}

if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
}
   
$query = "select id, details, license, serialnumber, model, model_variant, os, root_password, superadmin_password, firmware, area, sdwan_server_ipaddr, sdwan_proto, vlan_id, 
			customer_id, enable, updated, uptime 
			from $main_table where id=$G_device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model = $row['model']; $model_variant = $row['model_variant'];
			$os = $row['os'];
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

	print "<form method=\"POST\" action=\"$curr_page\" >";
	api_input_hidden("details_before", $details);
	api_input_hidden("area_before", $area);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:660px;">';		

	api_ui_config_option_readonly("ID", $id);
	api_ui_config_option_readonly("Serial Number", $G_device_serialnumber);		
	api_ui_config_option_text("Details", $details, "details", null, null);
	api_ui_config_option_text("Area", $area, "area", null, null);
	
	if($model=="spider") { $_model="SMOAD Spider"; }
	else if($model=="spider2") { $_model="SMOAD Spider2"; }
	else if($model=="beetle") { $_model="SMOAD Beetle"; }
	else if($model=="bumblebee") { $_model="SMOAD BumbleBee"; }
	else if($model=="wasp1") { $_model="SMOAD Wasp1"; }
	else if($model=="wasp2") { $_model="SMOAD Wasp2"; }
	else if($model=="vm") { $_model="SMOAD VM"; }
	api_ui_config_option_readonly("Model", $_model);
	
	if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
	else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
	else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
	else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
	api_ui_config_option_readonly("Model Variant", $_model_variant);
	
	if($os=="openwrt") { $os="OpenWRT"; }
	else if($os=="ubuntu") { $os="Ubuntu"; }
	api_ui_config_option_readonly("OS", $os);
	
	api_ui_config_option_readonly("Firmware", $firmware);
	api_ui_config_option_readonly("Uptime", $uptime);
	
	function get_last_24_boot_up_count($db, $serialnumber)
	{	$port_up_count=0;
		$query = "SELECT sum(boot_up_count) boot_up_count FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and log_timestamp > (NOW() - INTERVAL 24 hour)";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $boot_up_count = $row['boot_up_count']; } }
		if($boot_up_count>0) { return $boot_up_count; }
		return $boot_up_count;
	}
	
	function get_last_boot_up_count_timestamp($db, $serialnumber)
	{	$boot_up_count_timestamp='-';
		$query = "SELECT log_timestamp boot_up_count_timestamp FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and boot_up_count = 1 order by id desc limit 1";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $boot_up_count_timestamp = $row['boot_up_count_timestamp']; } }
		return $boot_up_count_timestamp;
	}
	
	api_ui_config_option_readonly("Boot Up Count (past 24 hours)", get_last_24_boot_up_count($db, $G_device_serialnumber));
	api_ui_config_option_readonly("Last Boot Up Timestamp", get_last_boot_up_count_timestamp($db, $G_device_serialnumber));
	
	$query = "select details from smoad_sdwan_servers where ipaddr=\"$sdwan_server_ipaddr\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc()) 
		{	$gw_server_details = $row['details'];
		}
	}
	api_ui_config_option_readonly("Gateway", $gw_server_details, "IP Addr: $sdwan_server_ipaddr");
	
	if($login_type=='root')
	{	api_ui_config_option_readonly("Root Password", $root_password);
		api_ui_config_option_readonly("Superadmin Password", $superadmin_password);
	}
	
	if($login_type=='root' || $login_type=='admin')
	{	if($customer_id!='notset') { $customer_name = db_api_get_value($db, "name", "smoad_customers", $customer_id); }
		else { $customer_name = "None"; $customer_id="N.A"; }
		api_ui_config_option_readonly("Customer", $customer_name, "Customer ID: $customer_id");
	}
	
	
	//provision ready ?
	// if sdwan proto is wg, then check if the tunnel is ready in the WG server, else show provision ready is false.
	// if not associated with any sdwan proto (or mptcp), then show true
	if($login_type=='root' || $login_type=='admin')
	{  $help = "";
		$provision_ready = false;
		if($sdwan_proto=='wg') 
		{	$wg_peers = false;
			$query = "select serialnumber from smoad_sds_wg_peers where device_serialnumber=\"$G_device_serialnumber\"";
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc())
				{	$sdwan_server_serialnumber = $row['serialnumber'];
					$wg_peers=true;
				}
			}
			if($wg_peers==true) { $help.="WG peer configured\n"; } else { $help.="no WG peer configured\n"; }
			
			$sdwan_server_status = "down";
			$query = "select status from smoad_sdwan_servers where serialnumber=\"$sdwan_server_serialnumber\" ";
			if($res = $db->query($query))
			{	while($row = $res->fetch_assoc())
				{	$sdwan_server_status = $row['status'];
				}
			}
			if($sdwan_server_status=="up") { $help.="GW is up\n"; } else { $help.="GW is down\n"; } 
			
			if($wg_peers==true && $sdwan_server_status=="up") { $provision_ready = true; }
		}
		else 
		{ $provision_ready = true; $help.="no GW assigned\n"; }
		if($provision_ready==true) { $_provision_ready="led-green2"; } else { $_provision_ready="led-red2"; }
		$_provision_ready = "<div class=\"$_provision_ready\">&#11044;</div>";
		api_ui_config_option_readonly("Provision Ready ?", $_provision_ready, $help);
	}
	
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
	
	print '</table></form><br>';
	
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{
		print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("command", "reboot");
		print '<table class="config_settings" style="width:660px;">';
		print '<tr><td>'; api_button_post("&#x26A0; Reboot", "Reboot Edge: $details - $serialnumber ?", "red"); print '</td></tr>';
		print '</table></form><br>';
	}
	
	if($provision_ready==true && ($login_type=='root' || $login_type=='admin') )
	{	print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("command", "reprovision");
		print '<table class="config_settings" style="width:660px;">';
		print '<tr><td>'; api_button_post("&#x26A0; Reprovision", "Reprovision Edge: $details - $serialnumber ?", "red"); print '</td></tr>';
		print '</table></form><br>';
	}
	
	if($login_type=='root' || $login_type=='admin')
	{	print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("command", "reset_sdwan");
		print '<table class="config_settings" style="width:660px;">';
		print '<tr><td>'; api_button_post("&#x26A0; Reset SDWAN", "Reprovision Edge: $details - $serialnumber ?", "red"); print '</td></tr>';
		print '</table></form><br>';
	}
?>


