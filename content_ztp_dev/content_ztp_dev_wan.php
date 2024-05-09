<br>
<?php

$wanport = $_GET['wanport'];
$main_table = "smoad_device_network_cfg";

if($wanport=="wan") { $wanport_dev = $G_wan; }
else if($wanport=="wan2") { $wanport_dev = $G_wan2; }
else if($wanport=="wan3") { $wanport_dev = $G_wan3; }

$_config_update=false;
$_id = $_POST['id'];

$_proto = $_POST['proto']; $_proto_before = $_POST['proto_before'];
if($_proto_before!=$_proto && $_id!=null) 
{ if($_proto=="static" || $_proto=="dhcp" || $_proto=="pppoe") 
  { db_api_set_value($db, $_proto, $wanport."_proto", $main_table, $_id, "char");
  	 if($G_device_os=="openwrt") { $job = "uci set network.".$wanport.".proto=^".$_proto."^"; }
  	 else if($G_device_os=="openwrt") { $job = "network.".$wanport.".proto=^".$_proto."^"; }
	 sm_ztp_add_job($db, $G_device_serialnumber, $job);
    $_config_update=true; 
  }
}

$_ipaddr = $_POST['ipaddr']; $_ipaddr_before = $_POST['ipaddr_before'];
if($_ipaddr_before!=$_ipaddr && $_ipaddr!=null && $_id!=null) 
{ //if(filter_var($_ipaddr, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_ipaddr, $wanport."_ipaddr", $main_table, $_id, "char");
  	 if($G_device_os=="openwrt") { $job = "uci set network.".$wanport.".ipaddr=^".$_ipaddr."^"; }
  	 else if($G_device_os=="openwrt") { $job = "uci set network.".$wanport.".ipaddr=^".$_ipaddr."^"; }
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_netmask = $_POST['netmask']; $_netmask_before = $_POST['netmask_before'];
if($_netmask_before!=$_netmask && $_netmask!=null && $_id!=null) 
{ //if(filter_var($_netmask, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_netmask, $wanport."_netmask", $main_table, $_id, "char");
  	 $job = "uci set network.".$wanport.".netmask=^".$_netmask."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_gateway = $_POST['gateway']; $_gateway_before = $_POST['gateway_before'];
if($_gateway_before!=$_gateway && $_gateway!=null && $_id!=null) 
{ //if(filter_var($_gateway, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_gateway, $wanport."_gateway", $main_table, $_id, "char");
  	 $job = "uci set network.".$wanport.".gateway=^".$_gateway."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_dns = $_POST['dns']; $_dns_before = $_POST['dns_before'];
if($_dns_before!=$_dns && $_dns!=null && $_id!=null) 
{ //if(filter_var($_dns, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_dns, $wanport."_dns", $main_table, $_id, "char");
  	 $job = "uci set network.".$wanport.".dns=^".$_dns."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_username = $_POST['username']; $_username_before = $_POST['username_before'];
if($_username_before!=$_username && $_username!=null && $_id!=null) 
{ //if(filter_var($_username, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_username, $wanport."_username", $main_table, $_id, "char");
  	 $job = "uci set network.".$wanport.".username=^".$_username."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_password = $_POST['password']; $_password_before = $_POST['password_before'];
if($_password_before!=$_password && $_password!=null && $_id!=null) 
{ //if(filter_var($_password, FILTER_VALIDATE_IP)) 
  { db_api_set_value($db, $_password, $wanport."_password", $main_table, $_id, "char");
  	 $job = "uci set network.".$wanport.".password=^".$_password."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_max_bandwidth = $_POST['max_bandwidth']; $_max_bandwidth_before = $_POST['max_bandwidth_before'];
if($_max_bandwidth_before!=$_max_bandwidth && $_max_bandwidth!=null && $_id!=null) 
{ { db_api_set_value($db, $_max_bandwidth, $wanport."_max_bandwidth", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$wanport."_max_bandwidth=^".$_max_bandwidth."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_medium_bandwidth_pct = $_POST['medium_bandwidth_pct']; $_medium_bandwidth_pct_before = $_POST['medium_bandwidth_pct_before'];
if($_medium_bandwidth_pct_before!=$_medium_bandwidth_pct && $_medium_bandwidth_pct!=null && $_id!=null) 
{ { db_api_set_value($db, $_medium_bandwidth_pct, $wanport."_medium_bandwidth_pct", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$wanport."_medium_bandwidth_pct=^".$_medium_bandwidth_pct."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_low_bandwidth_pct = $_POST['low_bandwidth_pct']; $_low_bandwidth_pct_before = $_POST['low_bandwidth_pct_before'];
if($_low_bandwidth_pct_before!=$_low_bandwidth_pct && $_low_bandwidth_pct!=null && $_id!=null) 
{ { db_api_set_value($db, $_low_bandwidth_pct, $wanport."_low_bandwidth_pct", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$wanport."_low_bandwidth_pct=^".$_low_bandwidth_pct."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}


if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit network");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
   sm_ztp_add_job($db, $G_device_serialnumber, "ifup $wanport");
}

$query = "select id, ".$wanport."_proto _wan_proto, 
		".$wanport."_ipaddr _wan_ipaddr, ".$wanport."_netmask _wan_netmask, ".$wanport."_gateway _wan_gateway,
		".$wanport."_dns _wan_dns, ".$wanport."_username _wan_username, ".$wanport."_password _wan_password, 
		".$wanport."_link_status _wan_link_status, 
		".$wanport."_max_bandwidth _max_bandwidth, ".$wanport."_medium_bandwidth_pct _medium_bandwidth_pct, 
		".$wanport."_low_bandwidth_pct _low_bandwidth_pct 
		from $main_table where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$proto = $row['_wan_proto'];
		$ipaddr = $row['_wan_ipaddr'];
		$netmask = $row['_wan_netmask'];
		$gateway = $row['_wan_gateway'];
		$dns = $row['_wan_dns'];
		$username = $row['_wan_username'];
		$password = $row['_wan_password'];
		$link_status = $row['_wan_link_status'];
		
		$max_bandwidth = $row['_max_bandwidth'];
		$medium_bandwidth_pct = $row['_medium_bandwidth_pct'];
		$low_bandwidth_pct = $row['_low_bandwidth_pct'];
		$medium_bandwidth = round((($max_bandwidth*$medium_bandwidth_pct)/100),2);
		$low_bandwidth = round((($max_bandwidth*$low_bandwidth_pct)/100),2);
	}
}

	api_form_post($curr_page);
	api_input_hidden("proto_before", $proto);
	api_input_hidden("ipaddr_before", $ipaddr);
	api_input_hidden("netmask_before", $netmask);
	api_input_hidden("gateway_before", $gateway);
	api_input_hidden("username_before", $username); //pppoe
	api_input_hidden("password_before", $password); //pppoe
	api_input_hidden("dns_before", $dns);
	api_input_hidden("max_bandwidth_before", $max_bandwidth);
	api_input_hidden("medium_bandwidth_pct_before", $medium_bandwidth_pct);
	api_input_hidden("low_bandwidth_pct_before", $low_bandwidth_pct);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:600px;">';
	
	print "<tr><td>Port</td><td>";
	if($wanport=="wan") { print "WAN1"; } else if($wanport=="wan2") { print "WAN2"; } else if($wanport=="wan3") { print "WAN3"; }
	print "</td></tr>";

	print "<tr><td>Connection Type</td><td>";
	if($_login_current_user_access=="access_level_limited") 
	{ if($proto=="dhcp") { print "DHCP"; } 
	  else if($proto=="static") { print "Static"; } 
	  else if($proto=="pppoe") { print "PPPoE"; }
	}
	else 
	{	print "<select name=\"proto\" id=\"proto\">";
		if($proto=="dhcp") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"dhcp\" $selected >DHCP</option>";
		if($proto=="pppoe") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"pppoe\" $selected >PPPoE</option>";
		if($proto=="static") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"static\" $selected >Static</option>";
		print "</select>";
		print "</td></tr>";
	}
	
	if($proto=="dhcp" || $proto=="pppoe" )
	{	if($ipaddr=="") { $ipaddr = "not set"; }
		if($netmask=="") { $netmask = "not set"; }
		if($gateway=="") { $gateway = "not set"; }
		api_ui_config_option_readonly("IP Address", $ipaddr);
		api_ui_config_option_readonly("Netmask", $netmask);
		api_ui_config_option_readonly("Gateway", $gateway);
		
		if($proto=="pppoe")
		{	api_ui_config_option_text("Username", $username, "username", $_login_current_user_access, null);				
			api_ui_config_option_password("Password", $password, "password", $_login_current_user_access, null);
		}
	}
	
	if($proto=="static")
	{	api_ui_config_option_text("IP Address", $ipaddr, "ipaddr", $_login_current_user_access, null);
		api_ui_config_option_text("Netmask", $netmask, "netmask", $_login_current_user_access, null);
		api_ui_config_option_text("Gateway", $gateway, "gateway", $_login_current_user_access, null);
	}
	
	$ipaddr_meta_dump = api_ip_addr_get_meta($ipaddr);
	if($ipaddr_meta_dump!=null) { api_ui_config_option_readonly("IP Whois Details", $ipaddr_meta_dump); }
   
   $help = "IP addresses of domain name servers used to resolve host names. Use spaces to separate multiple domain name server addresses.";
   api_ui_config_option_text("DNS Servers", $dns, "dns", $_login_current_user_access, $help);
   
   api_ui_config_option_readonly("Connection Status", $link_status);
   
   api_ui_config_option_readonly("<b>QoS Settings</b>", '');
   api_ui_config_option_text("Max Bandwidth (Mbps)", $max_bandwidth, "max_bandwidth", $_login_current_user_access, "Range: 1-990 Mbps");
	api_ui_config_option_text("Medium Bandwidth % (".$medium_bandwidth." Mbps)", $medium_bandwidth_pct, "medium_bandwidth_pct", $_login_current_user_access, "Range: 1-100");
	api_ui_config_option_text("Low Bandwidth % (".$low_bandwidth." Mbps)", $low_bandwidth_pct, "low_bandwidth_pct", $_login_current_user_access, "Range: 1-100");
			
   
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';
			


?>

