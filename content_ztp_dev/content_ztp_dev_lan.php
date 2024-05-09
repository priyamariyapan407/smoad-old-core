<br>
<?php

$main_table = "smoad_device_network_cfg";

$_config_update=false;
$_id = $_POST['id'];
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

if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit network");
   sm_ztp_add_job($db, $G_device_serialnumber, "ifup lan");
}

$query = "select id, lan_ipaddr, lan_netmask from $main_table where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$ipaddr = $row['lan_ipaddr'];
		$netmask = $row['lan_netmask'];
	}
}

	api_form_post($curr_page);
	api_input_hidden("ipaddr_before", $ipaddr);
	api_input_hidden("netmask_before", $netmask);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:800px;">';

	api_ui_config_option_text("IP Address", $ipaddr, "ipaddr", $_login_current_user_access, null);	
	api_ui_config_option_text("Netmask", $netmask, "netmask", $_login_current_user_access, null);
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';

?>

