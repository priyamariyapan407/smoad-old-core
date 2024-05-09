<br>
<?php 

$lteport = $_GET['lteport'];
$main_table = "smoad_device_network_cfg";

$_config_update=false;

$_max_bandwidth = $_POST['max_bandwidth']; $_max_bandwidth_before = $_POST['max_bandwidth_before'];
if($_max_bandwidth_before!=$_max_bandwidth && $_max_bandwidth!=null && $_id!=null) 
{ { db_api_set_value($db, $_max_bandwidth, $lteport."_max_bandwidth", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$lteport."_max_bandwidth=^".$_max_bandwidth."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_medium_bandwidth_pct = $_POST['medium_bandwidth_pct']; $_medium_bandwidth_pct_before = $_POST['medium_bandwidth_pct_before'];
if($_medium_bandwidth_pct_before!=$_medium_bandwidth_pct && $_medium_bandwidth_pct!=null && $_id!=null) 
{ { db_api_set_value($db, $_medium_bandwidth_pct, $lteport."_medium_bandwidth_pct", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$lteport."_medium_bandwidth_pct=^".$_medium_bandwidth_pct."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}

$_low_bandwidth_pct = $_POST['low_bandwidth_pct']; $_low_bandwidth_pct_before = $_POST['low_bandwidth_pct_before'];
if($_low_bandwidth_pct_before!=$_low_bandwidth_pct && $_low_bandwidth_pct!=null && $_id!=null) 
{ { db_api_set_value($db, $_low_bandwidth_pct, $lteport."_low_bandwidth_pct", $main_table, $_id, "num");
  	 $job = "uci set smoad.qos.".$lteport."_low_bandwidth_pct=^".$_low_bandwidth_pct."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  	 $_config_update=true; 
  }
}


if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
}

//Front end

function _signal_graph($signal_strength)
{	if($signal_strength=="excellent")
	{ print '<td style="padding:0px;"><div style="background-color:#00baad;width:7px;height:8px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#00baad;width:7px;height:12px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#00baad;width:7px;height:16px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#00baad;width:7px;height:20px;border-radius:2px;"></div></td>';
	}
	else if($signal_strength=="good")
	{ print '<td style="padding:0px;"><div style="background-color:#add45c;width:7px;height:8px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#add45c;width:7px;height:12px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#add45c;width:7px;height:16px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:16px;border-radius:2px;"></div></td>'; 
	}
	else if($signal_strength=="fair")
	{ print '<td style="padding:0px;"><div style="background-color:#FF5733;width:7px;height:8px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#FF5733;width:7px;height:12px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:16px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:20px;border-radius:2px;"></div></td>'; 
	}
	else if($signal_strength=="bad")
	{ print '<td style="padding:0px;"><div style="background-color:#C70039;width:7px;height:8px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:16px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:16px;border-radius:2px;"></div></td>';
	  print '<td style="padding:0px;"><div style="background-color:#fff;width:7px;height:16px;border-radius:2px;"></div></td>'; 
	}
}

$query = "select id, ".$lteport."_ipaddr _lte_ipaddr, ".$lteport."_netmask _lte_netmask, ".$lteport."_gateway _lte_gateway,
		".$lteport."_carrier _lte_carrier, ".$lteport."_imei _lte_imei, ".$lteport."_signal_strength _lte_signal_strength, 
		".$lteport."_link_status _lte_link_status, 
		".$lteport."_max_bandwidth _max_bandwidth, ".$lteport."_medium_bandwidth_pct _medium_bandwidth_pct, 
		".$lteport."_low_bandwidth_pct _low_bandwidth_pct  
		from $main_table where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$proto = $row['_lte_proto'];
		$ipaddr = $row['_lte_ipaddr'];
		$netmask = $row['_lte_netmask'];
		$gateway = $row['_lte_gateway'];
		
		$carrier = $row['_lte_carrier'];
		$imei = $row['_lte_imei'];
		$signal_strength = $row['_lte_signal_strength'];
		
		$link_status = $row['_lte_link_status'];
		$max_bandwidth = $row['_max_bandwidth'];
		$medium_bandwidth_pct = $row['_medium_bandwidth_pct'];
		$low_bandwidth_pct = $row['_low_bandwidth_pct'];
		$medium_bandwidth = round((($max_bandwidth*$medium_bandwidth_pct)/100),2);
		$low_bandwidth = round((($max_bandwidth*$low_bandwidth_pct)/100),2);
	}
}

	api_form_post($curr_page);
	api_input_hidden("max_bandwidth_before", $max_bandwidth);
	api_input_hidden("medium_bandwidth_pct_before", $medium_bandwidth_pct);
	api_input_hidden("low_bandwidth_pct_before", $low_bandwidth_pct);
	print '<table class="config_settings" style="width:600px;">';
	if($lteport=="lte1") { $_lteport="LTE1"; } else if($lteport=="lte2") { $_lteport="LTE2"; } else if($lteport=="lte3") { $_lteport="LTE3"; }
	api_ui_config_option_readonly("Port", $_lteport);
	api_ui_config_option_readonly("Carrier", $carrier);
	api_ui_config_option_readonly("IMEI", $imei);
	api_ui_config_option_readonly("Connection Status", $link_status);
	
	print "<tr><td>Signal Strength</td><td>";
	if($signal_strength=="error") { print "$signal_strength"; }
	else 
	{	print '<table style="background-color:white;width:30px;padding:0px;">';
		print '<tr style="padding:0px;vertical-align:bottom;">';
		_signal_graph($signal_strength);
		print '</tr>';
		print '</table>';
	}
	print "</td></tr>";
	
	
	api_ui_config_option_readonly("IP Address", $ipaddr);
	api_ui_config_option_readonly("Netmask", $netmask);
	api_ui_config_option_readonly("Gateway", $gateway);
	
	$ipaddr_meta_dump = api_ip_addr_get_meta($ipaddr);
	if($ipaddr_meta_dump!=null) { api_ui_config_option_readonly("IP Whois Details", $ipaddr_meta_dump); }
	
	api_ui_config_option_readonly("<b>QoS Settings</b>", '');
   api_ui_config_option_text("Max Bandwidth (Mbps)", $max_bandwidth, "max_bandwidth", $_login_current_user_access, "Range: 1-990 Mbps");
	api_ui_config_option_text("Medium Bandwidth % (".$medium_bandwidth." Mbps)", $medium_bandwidth_pct, "medium_bandwidth_pct", $_login_current_user_access, "Range: 1-100");
	api_ui_config_option_text("Low Bandwidth % (".$low_bandwidth." Mbps)", $low_bandwidth_pct, "low_bandwidth_pct", $_login_current_user_access, "Range: 1-100");
   
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';

?>

