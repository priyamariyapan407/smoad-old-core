<br>
<?php

$main_table = "smoad_devices";
$device_serialnumber = $_POST['device_serialnumber'];

$query = "select ipaddr from smoad_sdwan_servers where serialnumber='$G_sds_serialnumber'"; 
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $sds_ipaddr = $row['ipaddr']; } }

/*
$_config_update=false;

$_ipaddr = $_POST['ipaddr']; $_ipaddr_before = $_POST['ipaddr_before'];
if($_ipaddr_before!=$_ipaddr && $_ipaddr!=null && $_id!=null) 
{ //if(filter_var($_ipaddr, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_ipaddr, "lan_ipaddr", $main_table, $_id, "char");
  	 $job = "uci set network.lan.ipaddr=^".$_ipaddr."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_netmask = $_POST['netmask']; $_netmask_before = $_POST['netmask_before'];
if($_netmask_before!=$_netmask && $_netmask!=null && $_id!=null) 
{ //if(filter_var($_netmask, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_netmask, "lan_netmask", $main_table, $_id, "char");
  	 $job = "uci set network.lan.netmask=^".$_netmask."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}
*/
$_id = $_POST['id'];
$_vlan_id = $_POST['vlan_id']; $_vlan_id_before = $_POST['vlan_id_before'];
if($_vlan_id_before!=$_vlan_id && $_vlan_id!=null && $_id!=null) 
{ db_api_set_value($db, $_vlan_id, "vlan_id", $main_table, $_id, "num");
  
  //add job here 
  $job = "device_vlan=^".$device_serialnumber.",".$_vlan_id."^";
  sm_ztp_sds_add_job($db, $G_sds_serialnumber, $job); 
}

$_sdwan_enable = $_POST['sdwan_enable']; $_sdwan_enable_before = $_POST['sdwan_enable_before'];
if($_sdwan_enable==null) { $_sdwan_enable='FALSE'; }
if($_sdwan_enable_before!=$_sdwan_enable && $_sdwan_enable!=null && $_id!=null) 
{ db_api_set_value($db, $_sdwan_enable, "sdwan_enable", $main_table, $_id, "char");
  
  //add job here 
  $job = "device_enable=^".$device_serialnumber.",".$_sdwan_enable."^";
  sm_ztp_sds_add_job($db, $G_sds_serialnumber, $job);  
}


$query = "select id, vlan_id, sdwan_enable from $main_table where serialnumber='$device_serialnumber' and sdwan_server_ipaddr='$sds_ipaddr'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$vlan_id = $row['vlan_id'];
		$sdwan_enable = $row['sdwan_enable'];
	}
}

	api_form_post($curr_page);
	api_input_hidden("vlan_id_before", $vlan_id);
	api_input_hidden("sdwan_enable_before", $sdwan_enable);
	api_input_hidden("id", $id);
	api_input_hidden("device_serialnumber", $device_serialnumber);
	print '<table class="config_settings" style="width:800px;">';
			
	if($G_sds_type=="l3_stand_alone" || $G_sds_type=="l3_dc") { /* dont show vlans */ }
	else 
	{	print "<tr><td align=right style=\"padding-right:40px;\">VLAN-ID</td><td>";
		if($sdwan_enable=='TRUE') { $disabled='disabled'; } else { $disabled=''; }
		print "<select name=\"vlan_id\" $disabled>";
		if($vlan_id==0) { $selected="selected"; } else { $selected=""; }
		print "<option value=\"0\" $selected >Disable</option>\n";
		$query = "select id, details, vlan_id from smoad_sdwan_server_vlans where id_smoad_sdwan_servers=$G_sds_id"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$vlan_details2 = $row['details'];
				$vlan_id2 = $row['vlan_id'];
				if($vlan_id==$vlan_id2) { $selected="selected"; } else { $selected=""; }
				print "<option value=\"$vlan_id2\" $selected >$vlan_id2 - $vlan_details2</option>\n";
			}
		}
		print "</select></td></tr>";
	}
	
	print "<tr><td align=right style=\"padding-right:40px;\">Enable</td><td>";
   if($sdwan_enable=="TRUE") { $checked="checked"; } else { $checked=""; }
   if($G_sds_type=="l2") //dont allow enable button if this is a vlan based sdwan server !
   { if($vlan_id==0) { $disabled='disabled'; } else { $disabled=''; } }
   print "<input type=\"checkbox\" id=\"sdwan_enable\" name=\"sdwan_enable\" value=\"TRUE\" $checked $disabled >";
   print "</td></tr>";
	
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
		
		
	print '</table></form><br>';

?>


