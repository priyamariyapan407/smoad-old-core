<br>
<?php

$main_table = "smoad_device_network_cfg";

$_config_update=false;
$_id = $_POST['id'];
$_aggpolicy = $_POST['aggpolicy']; $_aggpolicy_before = $_POST['aggpolicy_before'];
if($_aggpolicy_before!=$_aggpolicy && $_aggpolicy!=null && $_id!=null) 
{ db_api_set_value($db, $_aggpolicy, "aggpolicy", $main_table, $_id, "char");
  $job = "uci set smoad.device.aggpolicy=^".$_aggpolicy."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_aggpolicy_mode = $_POST['aggpolicy_mode']; $_aggpolicy_mode_before = $_POST['aggpolicy_mode_before'];
if($_aggpolicy_mode_before!=$_aggpolicy_mode && $_aggpolicy_mode!=null && $_id!=null) 
{ db_api_set_value($db, $_aggpolicy_mode, "aggpolicy_mode", $main_table, $_id, "char");
  $job = "uci set smoad.device.aggpolicy_mode=^".$_aggpolicy_mode."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true; 
}

if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit mwan3");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit network");
}

$query = "select id, aggpolicy, aggpolicy_mode from $main_table where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$aggpolicy = $row['aggpolicy'];
		$aggpolicy_mode = $row['aggpolicy_mode'];
	}
}

	api_form_post($curr_page);
	api_input_hidden("aggpolicy_before", $aggpolicy);
	api_input_hidden("aggpolicy_mode_before", $aggpolicy_mode);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:800px;">';
			
	print "<tr><td>Mode</td><td>";
	if($_login_current_user_access=="access_level_limited" || $aggpolicy=="balanced") //if aggpolicy is balanced, then disable this input
	{	if($aggpolicy_mode=="linkfail") { print "Link Failover"; }
		else if($aggpolicy_mode=="loadbal") { print "Load Balance"; }
	}
	else 
	{	print "<select name=\"aggpolicy_mode\" id=\"aggpolicy_mode\">";
		if($aggpolicy_mode=="notset") { print "<option value=\"notset\" selected >Not Set</option>"; } //show this option if nothing is set !
		
		if($aggpolicy_mode=="linkfail") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"linkfail\" $selected >Link Failover</option>";
		if($aggpolicy_mode=="loadbal") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"loadbal\" $selected >Load Balance</option>";
		print "</select>";
	}
	print "</td></tr>";

	print "<tr><td>Prefer</td><td>";
	if($_login_current_user_access=="access_level_limited")
	{	if($aggpolicy=="balanced") { print "Balanced"; }
		else if($aggpolicy=="wan") { print "WAN"; }
		else if($aggpolicy=="wan2") { print "WAN2"; }
		else if($aggpolicy=="notset") { print "Not Set"; }
	}
	else 
	{	print "<select name=\"aggpolicy\" id=\"aggpolicy\">";
		if($aggpolicy=="notset") { print "<option value=\"notset\" selected >Not Set</option>"; } //show this option if nothing is set !
		
		if($aggpolicy_mode=="loadbal")
		{	if($aggpolicy=="balanced") { $selected="selected"; } else { $selected=""; }
			print "<option value=\"balanced\" $selected >Balanced</option>";
		}
		if($aggpolicy=="wan") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"wan\" $selected >WAN</option>";
		if($aggpolicy=="wan2") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"wan2\" $selected >WAN2</option>";
		if($aggpolicy=="lte1") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"lte1\" $selected >LTE1</option>";
		if($aggpolicy=="lte2") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"lte2\" $selected >LTE2</option>";
		print "</select>";
	}
	print "</td></tr>";
	

	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';

?>

