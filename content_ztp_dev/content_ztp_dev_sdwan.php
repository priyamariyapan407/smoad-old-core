<br>
<?php

$main_table = "smoad_devices";

$_config_update=false;
$_id = $_POST['id'];
$_id_smoad_device_network_cfg = $_POST['id_smoad_device_network_cfg'];

$_sdwan_enable = $_POST['sdwan_enable']; $_sdwan_enable_before = $_POST['sdwan_enable_before'];
 if($_sdwan_enable!="TRUE") { $_sdwan_enable="FALSE"; } //make binary
if(($_sdwan_enable_before!=$_sdwan_enable) &&  $_id!=null) 
{ 
  db_api_set_value($db, $_sdwan_enable, "sdwan_enable", $main_table, $_id, "char");
  if($_sdwan_enable=="TRUE") { $_sdwan_enable_dev=1; } else { $_sdwan_enable_dev=0; }
  $job = "uci set smoad.device.sdwan_enable=^".$_sdwan_enable_dev."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $job = "uci set smoad.device.wg=^".$_sdwan_enable_dev."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
 
  $_config_update=true;
}


$_sdwan_link_high_usage_threshold = $_POST['sdwan_link_high_usage_threshold']; $_sdwan_link_high_usage_threshold_before = $_POST['sdwan_link_high_usage_threshold_before'];
if(($_sdwan_link_high_usage_threshold_before!=$_sdwan_link_high_usage_threshold) &&  $_id_smoad_device_network_cfg!=null) 
{ db_api_set_value($db, $_sdwan_link_high_usage_threshold, "sdwan_link_high_usage_threshold", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "num");
  db_api_set_value($db, "notset", "sdwan_link_high_usage", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "char"); 
}

$_sdwan_link_high_latency_threshold = $_POST['sdwan_link_high_latency_threshold']; $_sdwan_link_high_latency_threshold_before = $_POST['sdwan_link_high_latency_threshold_before'];
if(($_sdwan_link_high_latency_threshold_before!=$_sdwan_link_high_latency_threshold) &&  $_id!=null) 
{	db_api_set_value($db, $_sdwan_link_high_latency_threshold, "sdwan_link_high_latency_threshold", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "num");
	db_api_set_value($db, "notset", "sdwan_link_high_latency", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "char");
}

$_sdwan_link_high_jitter_threshold = $_POST['sdwan_link_high_jitter_threshold']; $_sdwan_link_high_jitter_threshold_before = $_POST['sdwan_link_high_jitter_threshold_before'];
if(($_sdwan_link_high_jitter_threshold_before!=$_sdwan_link_high_jitter_threshold) &&  $_id!=null) 
{	db_api_set_value($db, $_sdwan_link_high_jitter_threshold, "sdwan_link_high_jitter_threshold", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "num");
	db_api_set_value($db, "notset", "sdwan_link_high_jitter", "smoad_device_network_cfg", $_id_smoad_device_network_cfg, "char");
}

$_qos_sdwan = $_POST['qos_sdwan']; $_qos_sdwan_before = $_POST['qos_sdwan_before'];
if($_qos_sdwan_before!=$_qos_sdwan && $_qos_sdwan!=null && $_id_smoad_device_network_cfg!=null) 
{ { db_api_set_value($db, $_qos_sdwan, "qos_sdwan", $main_table, $_id, "char");
  	 $job = "uci set smoad.qos.sdwan=^".$_qos_sdwan."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit network");
   sm_ztp_add_job($db, $G_device_serialnumber, "ifup wg0");
}

$query = "select id, sdwan_server_ipaddr, sdwan_enable, qos_sdwan from $main_table where serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
		$sdwan_enable = $row['sdwan_enable'];
		$qos_sdwan = $row['qos_sdwan'];
	}
}

$query = "select id, sdwan_link_high_usage_threshold, sdwan_link_high_latency_threshold, sdwan_link_high_jitter_threshold from smoad_device_network_cfg where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id_smoad_device_network_cfg = $row['id'];
		$sdwan_link_high_usage_threshold = $row['sdwan_link_high_usage_threshold'];
		$sdwan_link_high_latency_threshold = $row['sdwan_link_high_latency_threshold'];
		$sdwan_link_high_jitter_threshold = $row['sdwan_link_high_jitter_threshold'];
	}
}

function _qos_app_prio_select($title, $value, $name)
{
	print "<tr><td>$title</td><td>";
	print "<select name=\"$name\" id=\"$name\">";
	if($value=="high") { $selected="selected"; } else { $selected=""; } 
	print "<option value=\"high\" $selected >High</option>";
	if($value=="medium") { $selected="selected"; } else { $selected=""; }
	print "<option value=\"medium\" $selected >Medium</option>";
	if($value=="low") { $selected="selected"; } else { $selected=""; }
	print "<option value=\"low\" $selected >Low</option>";
	print "</select>";
	print "</td></tr>";
}

	api_form_post($curr_page);
	api_input_hidden("sdwan_enable_before", $sdwan_enable);
	api_input_hidden("sdwan_server_ipaddr_before", $sdwan_server_ipaddr);
	api_input_hidden("sdwan_link_high_usage_threshold_before", $sdwan_link_high_usage_threshold);
	api_input_hidden("sdwan_link_high_latency_threshold_before", $sdwan_link_high_latency_threshold);
	api_input_hidden("sdwan_link_high_jitter_threshold_before", $sdwan_link_high_jitter_threshold);
	api_input_hidden("qos_sdwan_before", $qos_sdwan);
	api_input_hidden("id_smoad_device_network_cfg", $id_smoad_device_network_cfg);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:600px;">';
	
	print "<tr><td>Enable</td><td>";
	if($sdwan_enable=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_enable\"  id=\"sdwan_enable\" value=\"TRUE\" $sdwan_enable $checked />";
	print "</td></tr>";

	api_ui_config_option_readonly("Gateway", $sdwan_server_ipaddr);
   api_ui_config_option_text("Link High Usage Alert Threshold (Kb/s)", $sdwan_link_high_usage_threshold, "sdwan_link_high_usage_threshold", $_login_current_user_access, "Kb/s");
	api_ui_config_option_text("Link High Latency Threshold (ms)", $sdwan_link_high_latency_threshold, "sdwan_link_high_latency_threshold", $_login_current_user_access, "ms");
	api_ui_config_option_text("Link High Jitter Threshold (ms)", $sdwan_link_high_jitter_threshold, "sdwan_link_high_jitter_threshold", $_login_current_user_access, "ms");
	
	_qos_app_prio_select("SD-WAN Priority", $qos_sdwan, "qos_sdwan");
	
	api_ui_config_option_update($_login_current_user_access);
			
	print '</table></form><br>';
			


?>

