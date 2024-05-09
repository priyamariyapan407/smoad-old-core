<br>
<?php

$main_table = "smoad_devices";

$_config_update=false;
$_id = $_POST['id'];
$_id_smoad_device_network_cfg = $_POST['id_smoad_device_network_cfg'];

$_qos_enabled = $_POST['qos_enabled']; $_qos_enabled_before = $_POST['qos_enabled_before'];
 if($_qos_enabled!="TRUE") { $_qos_enabled="FALSE"; } //make binary
if(($_qos_enabled_before!=$_qos_enabled) &&  $_id!=null) 
{ 
  db_api_set_value($db, $_qos_enabled, "qos_enabled", $main_table, $_id, "char");
  if($_qos_enabled=="TRUE") { $_qos_enabled_dev=1; } else { $_qos_enabled_dev=0; }
  $job = "uci set smoad.qos.enabled=^".$_qos_enabled_dev."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
 
  $_config_update=true;
}


if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
}

$query = "select id, qos_enabled from $main_table where serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$qos_enabled = $row['qos_enabled'];
	}
}


	api_form_post($curr_page);
	api_input_hidden("qos_enabled_before", $qos_enabled);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:600px;">';
	
	print "<tr><td>Enable</td><td>";
	if($qos_enabled=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"qos_enabled\"  id=\"qos_enabled\" value=\"TRUE\" $qos_enabled $checked />";
	print "</td></tr>";

	api_ui_config_option_update($_login_current_user_access);
			
	print '</table></form><br>';
			


?>

