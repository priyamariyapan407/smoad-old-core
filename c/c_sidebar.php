<?php error_reporting(5); session_start(); $language=$_SESSION['language']; 
$current_year = date("Y");
?>
<style type="text/css">
table.sidebar_box {width:100%;border-width:0px;border-spacing:0px;border-style:ridge;background-color:#444;color:black;font-size:14px; }
.sidebar_box td { padding:12px;background-color:#444;color:#eee; }
.sidebar_box td:hover { background-color:#222; }
.sidebar_button {color:#eee;text-decoration:none;font-size:14px;vertical-align:middle;}
.sidebar_link {color:#eee;font-size:14px;text-decoration:none;margin-left:12px;}
.accordion { background-color:#444; color:#eee; cursor:pointer; padding:12px; width: 98%; margin:1px; border:none; text-align:left; outline:none; transition:0.5s; }
.active, .accordion:hover { background-color: #222; }
.panel { padding:1px; display:none; background-color:#444; overflow:hidden; color:black; }
.panel2 { padding: 1px; display: block; background-color: #444; overflow: hidden; color:black; }
img.icon_sidebar { width:14px;height:14px; }
</style>
<?php 

function _get_side_bar_button($link, $name, $icon = null)
{	$name = "&nbsp;<img src='i/$icon' class=\"icon_sidebar\">&nbsp; $name";
	print "<button class=\"accordion\">";
	if($link!=null) { print "<a href=\"$link\" class=\"sidebar_button\" >$name</a>"; } 
	else { print "$name</button>"; }
	print "</button>";
}



function _port_branching($port)
{	$session_key = $GLOBALS['session_key'];
   if($port == "WAN")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
	  	print "<tr><td><a href=\"index.php?page=ztp_dev_wan&wanport=wan&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; WAN1</a></td></tr>"; 
   }
	elseif($port == "WAN2")
	{	if(sm_get_device_port_branching_by_serialnumber($port))
      print "<tr><td><a href=\"index.php?page=ztp_dev_wan&wanport=wan2&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; WAN2</a></td></tr>"; 	
	}
	elseif($port == "WAN3")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
   	print "<tr><td><a href=\"index.php?page=ztp_dev_wan&wanport=wan3&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; WAN3</a></td></tr>"; 
   }	
   elseif($port == "LTE1")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
	   print "<tr><td><a href=\"index.php?page=ztp_dev_lte&lteport=lte1&skey=$session_key\" class=\"sidebar_link\"><img src='i/cellular-network.png' class=\"icon_sidebar\">&nbsp; LTE1</a></td></tr>";      
   }
   elseif($port == "LTE2")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
      print "<tr><td><a href=\"index.php?page=ztp_dev_lte&lteport=lte2&skey=$session_key\" class=\"sidebar_link\"><img src='i/cellular-network.png' class=\"icon_sidebar\">&nbsp; LTE2</a></td></tr>"; 
   }
   elseif($port == "LTE3")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
   	print "<tr><td><a href=\"index.php?page=ztp_dev_lte&lteport=lte3&skey=$session_key\" class=\"sidebar_link\"><img src='i/cellular-network.png' class=\"icon_sidebar\">&nbsp; LTE3</a></td></tr>"; 
   }
   elseif($port == "LAN")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
   	print "<tr><td><a href=\"index.php?page=ztp_dev_lan&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; LAN</a></td></tr>";
   }
   elseif($port == "WIRELESS")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
   	print "<tr><td><a href=\"index.php?page=ztp_dev_wireless&skey=$session_key\" class=\"sidebar_link\"><img src='i/wi-fi.png' class=\"icon_sidebar\">&nbsp; Wireless</a></td></tr>";
   }
   elseif($port == "SD-WAN")
   {	if(sm_get_device_port_branching_by_serialnumber($port))
   	print "<tr><td><a href=\"index.php?page=ztp_dev_sdwan&skey=$session_key\" class=\"sidebar_link\"><img src='i/cloud-connection.png' class=\"icon_sidebar\">&nbsp; SD-WAN</a></td></tr>";
   } 
}

if($G_ztp_dev_page==true) 
{	_get_side_bar_button("index.php?page=home&skey=$session_key", "CORE", "home-sidebar.png");
	_get_side_bar_button("index.php?page=ztp_dev_home&skey=$session_key", "HOME", "wi-fi-router-red.png");
	_get_side_bar_button("index.php?page=ztp_dev_dash&skey=$session_key", "STATUS", "combo-chart.png");
	_get_side_bar_button("index.php?page=ztp_dev_network&skey=$session_key", "NETWORK", "networking-manager.png");
	if($page=="ztp_dev_lan" || $page=="ztp_dev_wan" || $page=="ztp_dev_lte" || $page=="ztp_dev_wireless" || $page=="ztp_dev_sdwan" || $page=="ztp_dev_network") 
	{ $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	_port_branching("LAN");
	_port_branching("WAN");
	_port_branching("WAN2");
	_port_branching("WAN3");
	_port_branching("WIRELESS");	
	_port_branching("LTE1");
	_port_branching("LTE2");
	_port_branching("LTE3");
	if($login_type=='root') { _port_branching("SD-WAN", NULL, NULL); }
	print "</table></div>";
	
	_get_side_bar_button("index.php?page=ztp_dev_qos&skey=$session_key", "QoS", "settings.png");
	if($page=="ztp_dev_qos" || $page=="ztp_dev_qos_app_prio") 
	{ $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=ztp_dev_qos_app_prio&skey=$session_key\" class=\"sidebar_link\"><img src='i/settings.png' class=\"icon_sidebar\">&nbsp; App Prioritization</a></td></tr>";
	print "</table></div>";
	
	_get_side_bar_button("index.php?page=ztp_dev_agg&skey=$session_key", "Link Aggregation", "merge.png");
	if($login_type=='root')
	{	_get_side_bar_button("index.php?page=ztp_dev_firmware&skey=$session_key", "Firmware", "settings.png");
	}
	_get_side_bar_button("index.php?page=ztp_dev_config&skey=$session_key", "Device Config", "settings.png");
	
	_get_side_bar_button(null, "LOGS", "log.png");
	if($page=="ztp_dev_status_log_index" || $page=="ztp_dev_status_log" || $page=="ztp_dev_network_stats_log_index" || $page=="ztp_dev_network_stats_log" || $page=="ztp_dev_access_log_index" || $page=="ztp_dev_consolidated_log_index") 
	{ $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=ztp_dev_consolidated_log_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; Consolidated Logs</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=ztp_dev_status_log_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; Link-Status</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=ztp_dev_network_stats_log_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; Network-Status</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=ztp_dev_access_log_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; User-Access</a></td></tr>";
	print "</table></div>";
	
	_get_side_bar_button(null, "REPORTS", "log.png");
	if($page=="ztp_dev_consolidated_report_index") 
	{ $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=ztp_dev_consolidated_report_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; Consolidated Report</a></td></tr>";
	print "</table></div>";
	
	if($login_type=='root')
	{	_get_side_bar_button(null, "ENGG DEBUG", "gears.png");
		if($page=="ztp_dev_debug_jobs") { $panel="panel2"; } else { $panel="panel"; }
		print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
		print "<tr><td><a href=\"index.php?page=ztp_dev_debug_jobs&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Jobs</a></td></tr>";
		print "</table></div>";
	}
}
else if($G_ztp_sds_page==true) 
{	_get_side_bar_button("index.php?page=home&skey=$session_key", "CORE", "home-sidebar.png");
	_get_side_bar_button("index.php?page=ztp_sds_home&skey=$session_key", "HOME", "gateway-red.png");
	
	_get_side_bar_button(null, "NETWORK", "networking-manager.png");
	if($page=="ztp_sds_vlans" || $page=="ztp_sds_server_cfg" || $page=="ztp_sds_circuits" || $page=="ztp_sds_softclient_config" ) 
	{ $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=ztp_sds_server_cfg&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; Settings</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=ztp_sds_circuits&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; CIRCUITs</a></td></tr>";
	
	if($G_sds_type=="l3_stand_alone" || $G_sds_type=="l3_dc") { /* dont show vlans */ }
	else { print "<tr><td><a href=\"index.php?page=ztp_sds_vlans&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; VLANs</a></td></tr>"; }
	print "</table></div>";

	_get_side_bar_button("index.php?page=ztp_sds_devices&skey=$session_key", "DEVICES", "wi-fi-router.png");
	
	if($login_type=='root')
	{	_get_side_bar_button(null, "ENGG DEBUG", "gears.png");
		if($page=="ztp_sds_debug_jobs") { $panel="panel2"; } else { $panel="panel"; }
		print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
		print "<tr><td><a href=\"index.php?page=ztp_sds_debug_jobs&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Jobs</a></td></tr>";
		print "</table></div>";
	}
}
else if($G_ztp_cust_page==true) 
{	_get_side_bar_button("index.php?page=home&skey=$session_key", "CORE", "home-sidebar.png");
	_get_side_bar_button("index.php?page=ztp_cust_home&skey=$session_key", "HOME", "user-red.png");
	_get_side_bar_button("index.php?page=ztp_cust_devices&skey=$session_key", "DEVICES", "wi-fi-router.png");
	
/*	_get_side_bar_button(null, "ENGG DEBUG", "gears.png");
	if($page=="ztp_sds_debug_jobs") { $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=ztp_sds_debug_jobs&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Jobs</a></td></tr>";
	*/
	print "</table></div>";
}
else
{	_get_side_bar_button("index.php?page=home&skey=$session_key", "HOME", "home-sidebar.png");
	
	if($page=="home" || $page=="users" || $page=="password" || $page=="user_details" || $page=="customers" || $page=="customer_details" ) { $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	if($login_type=='root')
	{	print "<tr><td><a href=\"index.php?page=users&skey=$session_key\" class=\"sidebar_link\"><img src='i/user.png' class=\"icon_sidebar\">&nbsp; Users</a></td></tr>";
	}
	print "<tr><td><a href=\"index.php?page=password&skey=$session_key\" class=\"sidebar_link\"><img src='i/user.png' class=\"icon_sidebar\">&nbsp; Password</a></td></tr>";
	print "</table></div>";
	
	if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
	{	_get_side_bar_button("index.php?page=customers&skey=$session_key", "Customers", "user.png");
		if($G_cust_id!='notset')
		{	$panel="panel2";
			print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
			print "<tr><td><a href=\"index.php?page=ztp_cust_home&skey=$session_key\" class=\"sidebar_link\" ><img src='i/user-red.png' class=\"icon_sidebar\">&nbsp; $G_cust_name</a></td></tr>";
			print "</table></div>";
		}
	}
	
	if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
	{	_get_side_bar_button("index.php?page=sdwan_servers&skey=$session_key", "GATEWAY", "gateway.png");
		if($G_sds_serialnumber!='notset')
		{	$panel="panel2";
			print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
			print "<tr><td><a href=\"index.php?page=ztp_sds_home&skey=$session_key\" class=\"sidebar_link\" title=\"Serial Number: $G_sds_serialnumber\" ><img src='i/gateway-red.png' class=\"icon_sidebar\">&nbsp; $G_sds_details</a></td></tr>";
			print "</table></div>";
		}
	}
	
	_get_side_bar_button("index.php?page=devices&skey=$session_key", "EDGE", "wi-fi-router.png");
	if($page=="devices" || $page=="update_firmware_server" || $page=="dev_config_templates" || $page=="dev_config_template_details" || $G_device_serialnumber!='notset') { $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	if($G_device_serialnumber!='notset')
	{	$panel="panel2";
		
		print "<tr><td><a href=\"index.php?page=ztp_dev_home&skey=$session_key\" class=\"sidebar_link\" title=\"Serial Number: $G_device_serialnumber\" ><img src='i/wi-fi-router-red.png' class=\"icon_sidebar\">&nbsp; $G_device_details</a></td></tr>";
		
	}
	if($login_type=='root' || $login_type=='admin')
	{	print "<tr><td><a href=\"index.php?page=update_firmware_server&skey=$session_key\" class=\"sidebar_link\" ><img src='i/server.png' class=\"icon_sidebar\">&nbsp; Firmware Server</a></td></tr>"; }
	print "<tr><td><a href=\"index.php?page=dev_config_templates&skey=$session_key\" class=\"sidebar_link\" ><img src='i/config-template.png' class=\"icon_sidebar\">&nbsp; Config Template</a></td></tr>";
	print "</table></div>";
	
	_get_side_bar_button("index.php?page=firewall_dash&skey=$session_key", "SECURITY", "networking-manager.png");
	if($page=="firewall_dash" || $page=="firewall_config" || $page=="firewall_log_index" || $page=="firewall_log" || $page=="firewall_rules" || $page=="firewall_ip_list" ) { $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	print "<tr><td><a href=\"index.php?page=firewall_rules&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Firewall Rules</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=firewall_ip_list&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; IP List</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=firewall_log_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/log.png' class=\"icon_sidebar\">&nbsp; Firewall Log</a></td></tr>";
	print "</table></div>";
	
	_get_side_bar_button(null, "ALERTS", "bell-white.png");
	if($page=="alerts" || $page=="alert_config" || $page=="alert_details" || $page=="alerts_index"  ) { $panel="panel2"; } else { $panel="panel"; }
	print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
	//print "<tr><td><a href=\"index.php?page=firewall_rules&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Firewall Rules</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=alert_config&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Alert Config</a></td></tr>";

	print "<tr><td><a href=\"index.php?page=alerts_index&skey=$session_key\" class=\"sidebar_link\"><img src='i/bell-white.png' class=\"icon_sidebar\">&nbsp; Historical Log</a></td></tr>";
	print "<tr><td><a href=\"index.php?page=alerts&type=user_login&skey=$session_key\" class=\"sidebar_link\"><img src='i/bell-white.png' class=\"icon_sidebar\">&nbsp; User Login</a></td></tr>";
	 print "<tr><td><a href=\"index.php?page=alerts&type=edge&skey=$session_key\" class=\"sidebar_link\"><img src='i/bell-white.png' class=\"icon_sidebar\">&nbsp; Edge</a></td></tr>";
	 print "<tr><td><a href=\"index.php?page=alerts&type=gw&skey=$session_key\" class=\"sidebar_link\"><img src='i/bell-white.png' class=\"icon_sidebar\">&nbsp; Gateway</a></td></tr>";
	 print "<tr><td><a href=\"index.php?page=alerts&type=fw&skey=$session_key\" class=\"sidebar_link\"><img src='i/bell-white.png' class=\"icon_sidebar\">&nbsp; Security</a></td></tr>";
	print "</table></div>";
	
	if($login_type=='root')
	{	_get_side_bar_button("index.php?page=ticketing_servers&skey=$session_key", "IMS", "server.png"); }
	
	if($login_type=='root')
	{	_get_side_bar_button(null, "NETWORK", "networking-manager.png");
		if($page=="port_config") { $panel="panel2"; } else { $panel="panel"; }
		print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
		print "<tr><td><a href=\"index.php?page=port_config&port_type=uplink&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; Uplink</a></td></tr>";
		print "<tr><td><a href=\"index.php?page=port_config&port_type=console&skey=$session_key\" class=\"sidebar_link\"><img src='i/ethernet-on.png' class=\"icon_sidebar\">&nbsp; Console</a></td></tr>";
		print "</table></div>";
	}
	
	if($login_type=='root')
	{	_get_side_bar_button(null, "ENGG DEBUG", "gears.png");
		if($page=="debug_jobs") { $panel="panel2"; } else { $panel="panel"; }
		print "<div class=\"$panel\"><table summary=\"sidebar buttons\" class=\"sidebar_box\" cellspacing=\"30\" cellpadding=\"10\" >";
		print "<tr><td><a href=\"index.php?page=debug_jobs&skey=$session_key\" class=\"sidebar_link\"><img src='i/gears.png' class=\"icon_sidebar\">&nbsp; Jobs</a></td></tr>";
		print "</table></div>";
	}
}


?>

<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}


</script>
