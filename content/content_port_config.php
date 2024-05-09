<br>
<?php

include('c/c_api_ubuntu_server_network.php');

$port_type = $_GET['port_type'];
$main_table = "smoad_port_cfg";

$_id = $_POST['id'];
$_port = $_POST['port']; $_port_before = $_POST['port_before'];
if($_port != $_port_before && $_port!=null)
{	$query = "update $main_table set port=\"$_port\" where id = $_id";
	$db->query($query);
}

$_proto = $_POST['proto']; $_proto_before = $_POST['proto_before'];
if($_proto != $_proto_before && $_port!=null && $_proto!=null)
{	$query = "update $main_table set proto=\"$_proto\" where id = $_id";
	$db->query($query);
	
	if($_proto=='dhcp')
	{ api_ubuntu_server_set_port_ip_mask_gw($db, $_port, $_proto, null, null, null); }
}

$_ipaddr = $_POST['ipaddr']; $_ipaddr_before = $_POST['ipaddr_before'];
$_netmask = $_POST['netmask']; $_netmask_before = $_POST['netmask_before'];
$_gateway = $_POST['gateway']; $_gateway_before = $_POST['gateway_before'];
if($_proto=='static' && $_port!=null && ($_ipaddr!=$_ipaddr_before || $_netmask!=$_netmask_before || $_gateway!=$_gateway_before))
{	
	api_ubuntu_server_set_port_ip_mask_gw($db, $_port, $_proto, $_ipaddr, $_netmask, $_gateway);
}


//Front end

$query = "select id, type, port, proto, details, ipaddr, netmask, broadcast, gateway, macid, status, enable, updated from $main_table where type = \"$port_type\"";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$port = $row['port'];
		$type = $row['type'];
		$proto = $row['proto'];
		$details = $row['details'];
		$ipaddr = $row['ipaddr'];
		$netmask = $row['netmask'];
		$broadcast = $row['broadcast'];			
		$gateway = $row['gateway'];
		//$macid = $row['macid'];
		$status = $row['status'];
		$enable = $row['enable'];
		$updated = $row['updated'];
		
				
		if($enable=="TRUE") { $bg_style="style=\"background-color:#D6FA89;\""; }
		else { $bg_style="style=\"background-color:#FF707D;\""; }
		
		$_status_output = `ifconfig | grep $port`;
		if(strpos($_status_output, $port) !== false) 
		{	$query2="update $main_table set status=\"UP\" where id=1"; $db->query($query2);
			$status="UP";
		}
		else  
		{	$query2="update $main_table set status=\"DOWN\" where id=1"; $db->query($query2);
			$status="DOWN";
		}
		
		if($status=="UP") { $bg_style="style=\"background-color:#D6FA89;\""; }
		else if($status=="DOWN") { $bg_style="style=\"background-color:#F77575;\""; }
		else if($status=="UP_WAITING") { $bg_style="style=\"background-color:#ebcc34;\""; }

		$macid = api_ubuntu_server_get_port_macid_kernel($db, $port);
		if($proto=='dhcp')
		{	$ipaddr = api_ubuntu_server_get_port_ipaddr_kernel($db, $port);
			$netmask = api_ubuntu_server_get_port_netmask_kernel($db, $port);
			
			if($ipaddr=="") { $ipaddr="retrieving data ..."; }
			if($netmask=="") { $netmask="retrieving data ..."; }
		}
		
		api_form_post($curr_page);
		api_input_hidden("id", $id);
		api_input_hidden("port_before", $port);
		api_input_hidden("port", $port); //since port is hardcoded
		api_input_hidden("port_type", $type);
		api_input_hidden("proto_before", $proto);
		api_input_hidden("ipaddr_before", $ipaddr);
		api_input_hidden("netmask_before", $netmask);
		api_input_hidden("gateway_before", $gateway);
		print '<table class="config_settings" style="width:600px;">';

		if($port_type=="uplink") { $_port_type = "Uplink"; }
		else if($port_type=="trunk") { $_port_type = "VPN Trunk"; }
		else if($port_type=="console") { $_port_type = "Console"; }
		print "<tr><td>Type</td><td><b>$_port_type Port</b></td></tr>";

		api_ui_config_option_readonly("Port", $port);
		api_ui_config_option_readonly("MAC ID", $macid);
		
		print "<tr><td>Connection Type</td><td>";
		print "<select name=\"proto\" id=\"proto\">";
		if($proto=="dhcp") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"dhcp\" $selected >DHCP</option>";
		//if($proto=="pppoe") { $selected="selected"; } else { $selected=""; }
		//print "<option value=\"pppoe\" $selected >PPPoE</option>";
		if($proto=="static") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"static\" $selected >Static</option>";
		print "</select>";
		print "</td></tr>";

		if($proto=='dhcp') { api_ui_config_option_readonly("IP Address", $ipaddr); }
		else { api_ui_config_option_text("IP Address", $ipaddr, "ipaddr", null, null); }	
	
		if($proto=='dhcp') { api_ui_config_option_readonly("Netmask", $netmask); }
		else { api_ui_config_option_text("Netmask", $netmask, "netmask", null, null); } 
		
		if($proto=='dhcp') { /*print "$gateway";*/ } //dont show
		else { api_ui_config_option_text("Gateway", $gateway, "gateway", null, null); } 

		api_ui_config_option_update($_login_current_user_access);
		print '</table></form><br>';
	}
}


?>
