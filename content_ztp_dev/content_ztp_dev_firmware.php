<?php

$main_table = "smoad_devices";

$_command = $_POST['command'];
$_id = $_POST['id'];
if($_command=='update_firmware' && $_id!=null) 
{ db_api_set_value($db, 'yes', "firmware_status", $main_table, $_id, "char");
  
  $query = "select update_firmware_server_user, update_firmware_server_ipaddr, update_firmware_server_base_path, update_firmware_server_pass from smoad_update_firmware_server where id=1"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$update_firmware_server_user = $row['update_firmware_server_user'];
			$update_firmware_server_ipaddr = $row['update_firmware_server_ipaddr'];
			$update_firmware_server_base_path = $row['update_firmware_server_base_path'];
			$update_firmware_server_pass = $row['update_firmware_server_pass'];
		}
	}  
  
  $job = "uci set smoad.device.update_firmware_server_user=^".$update_firmware_server_user."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci set smoad.device.update_firmware_server_ipaddr=^".$update_firmware_server_ipaddr."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci set smoad.device.update_firmware_server_base_path=^".$update_firmware_server_base_path."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci set smoad.device.update_firmware_server_pass=^".$update_firmware_server_pass."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci set smoad.device.update_firmware=^yes^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci commit smoad";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);

  print "<pre>NOTE: the Edge will initiate firmware update task shortly (if any) !</pre>";
}

   
$query = "select id, details, license, serialnumber, model, area, sdwan_server_ipaddr, vlan_id, firmware, firmware_status, enable, updated 
			from $main_table where id=$G_device_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model = $row['model'];
			$area = $row['area'];
			$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
			$vlan_id = $row['vlan_id'];
			$enable = $row['enable'];
			$updated = $row['updated'];
			$firmware = $row['firmware'];
			$firmware_status = $row['firmware_status'];
		}
	}

	if($firmware_status=='yes') { $_firmware_status="Pending Update"; } else { $_firmware_status="Complete"; }
	
	api_form_post($curr_page);
	api_input_hidden("id", $id);
	api_input_hidden("command", 'update_firmware');
	print '<table class="config_settings" style="width:400px;">';		
	api_ui_config_option_readonly("Firmware", $firmware);
	api_ui_config_option_readonly("Update Status", $_firmware_status);
	
	if($login_type=='root')
	{	if($firmware_status!='yes') { api_ui_config_option_update_firmware($_login_current_user_access); } //disable update when it is pending update
	}
	print '</table></form><br>';

?>


