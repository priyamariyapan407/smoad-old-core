<?php
$_id = $_POST['id'];

$query = "select id, template_details, details, model, model_variant, area, 
				lan_ipaddr, lan_netmask, 
				wan_proto, wan_ipaddr, wan_netmask, wan_gateway, wan_dns, wan_username, wan_password,
				wan2_proto, wan2_ipaddr, wan2_netmask, wan2_gateway, wan2_dns, wan2_username, wan2_password,
				wan3_proto, wan3_ipaddr, wan3_netmask, wan3_gateway, wan3_dns, wan3_username, wan3_password,
				lte1_ipaddr, lte1_netmask, lte1_gateway,
				lte2_ipaddr, lte2_netmask, lte2_gateway, 
				lte3_ipaddr, lte3_netmask, lte3_gateway,
				wireless_ssid, wireless_key, wireless_encryption, wireless_auth_server, wireless_auth_secret,
				aggpolicy_mode, aggpolicy,
				sdwan_link_high_usage_threshold
				from smoad_device_templates where id = $_id "; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$template_details = $row['template_details'];
			$details = $row['details'];
			$model = $row['model'];
			$model_variant = $row['model_variant'];
			$area = $row['area'];
			
			$lan_ipaddr = $row['lan_ipaddr']; $lan_netmask = $row['lan_netmask'];
			$wan_proto = $row['wan_proto'];
			$wan_ipaddr = $row['wan_ipaddr'];
			$wan_netmask = $row['wan_netmask'];
			$wan_gateway = $row['wan_gateway'];
			$wan_dns = $row['wan_dns'];
			$wan_username = $row['wan_username'];
			$wan_password = $row['wan_password'];
			$wan2_proto = $row['wan2_proto'];
			$wan2_ipaddr = $row['wan2_ipaddr'];
			$wan2_netmask = $row['wan2_netmask'];
			$wan2_gateway = $row['wan2_gateway'];
			$wan2_dns = $row['wan2_dns'];
			$wan2_username = $row['wan2_username'];
			$wan2_password = $row['wan2_password'];
			$wan3_proto = $row['wan3_proto'];
			$wan3_ipaddr = $row['wan3_ipaddr'];
			$wan3_netmask = $row['wan3_netmask'];
			$wan3_gateway = $row['wan3_gateway'];
			$wan3_dns = $row['wan3_dns'];
			$wan3_username = $row['wan3_username'];
			$wan3_password = $row['wan3_password'];
			$lte1_ipaddr = $row['lte1_ipaddr']; $lte1_netmask = $row['lte1_netmask']; $lte1_gateway = $row['lte1_gateway'];
			$lte2_ipaddr = $row['lte2_ipaddr']; $lte2_netmask = $row['lte2_netmask']; $lte2_gateway = $row['lte2_gateway'];
			$lte3_ipaddr = $row['lte3_ipaddr']; $lte3_netmask = $row['lte3_netmask']; $lte3_gateway = $row['lte3_gateway'];
			$wireless_ssid = $row['wireless_ssid']; $wireless_key = $row['wireless_key']; $wireless_encryption = $row['wireless_encryption']; $wireless_auth_server = $row['wireless_auth_server']; $wireless_auth_secret = $row['wireless_auth_secret'];
			$aggpolicy_mode = $row['aggpolicy_mode']; $aggpolicy = $row['aggpolicy'];
			$sdwan_link_high_usage_threshold = $row['sdwan_link_high_usage_threshold'];
			
			if($model=="spider") { $_model="Spider"; }
			else if($model=="spider2") { $_model="Spider2"; }
			else if($model=="beetle") { $_model="Beetle"; }
			else if($model=="bumblebee") { $_model="BumbleBee"; }
			else if($model=="vm") { $_model="VM"; }
			else if($model=="soft_client") { $_model="Soft-client"; }
		
			//if($status=='up') { $status="led-green"; } else { $status="led-red"; }
			if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
			else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
			else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
			else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
			
			print '<table class="config_settings" style="width:600px;">';
			api_ui_config_option_readonly("ID", $id);
			api_ui_config_option_readonly("Template Details", $template_details);	
			api_ui_config_option_readonly("Edge Details", $details);
			api_ui_config_option_readonly("Model - Variant", "$_model - $_model_variant");
			api_ui_config_option_readonly("Area", $area);
			
			api_ui_config_option_readonly("LAN IP Address", $lan_ipaddr);
			api_ui_config_option_readonly("LAN Netmask", $lan_netmask);
			
			api_ui_config_option_readonly("WAN Connection Type", $wan_proto);
			api_ui_config_option_readonly("WAN IP Address", $wan_ipaddr);
			api_ui_config_option_readonly("WAN Netmask", $wan_netmask);
			api_ui_config_option_readonly("WAN Gateway", $wan_gateway);
			api_ui_config_option_readonly("WAN DNS Servers", $wan_dns);
			api_ui_config_option_readonly("WAN Username", $wan_username);
			api_ui_config_option_readonly("WAN Password", $wan_password);
			
			api_ui_config_option_readonly("WAN2 Connection Type", $wan2_proto);
			api_ui_config_option_readonly("WAN2 IP Address", $wan2_ipaddr);
			api_ui_config_option_readonly("WAN2 Netmask", $wan2_netmask);
			api_ui_config_option_readonly("WAN2 Gateway", $wan2_gateway);
			api_ui_config_option_readonly("WAN2 DNS Servers", $wan2_dns);
			api_ui_config_option_readonly("WAN2 Username", $wan2_username);
			api_ui_config_option_readonly("WAN2 Password", $wan2_password);
			
			api_ui_config_option_readonly("WAN3 Connection Type", $wan3_proto);
			api_ui_config_option_readonly("WAN3 IP Address", $wan3_ipaddr);
			api_ui_config_option_readonly("WAN3 Netmask", $wan3_netmask);
			api_ui_config_option_readonly("WAN3 Gateway", $wan3_gateway);
			api_ui_config_option_readonly("WAN3 DNS Servers", $wan3_dns);
			api_ui_config_option_readonly("WAN3 Username", $wan3_username);
			api_ui_config_option_readonly("WAN3 Password", $wan3_password);

			api_ui_config_option_readonly("LTE1 IP Address", $lte1_ipaddr);
			api_ui_config_option_readonly("LTE1 Netmask", $lte1_netmask);
			api_ui_config_option_readonly("LTE1 Gateway", $lte1_gateway);
			
			api_ui_config_option_readonly("LTE2 IP Address", $lte2_ipaddr);
			api_ui_config_option_readonly("LTE2 Netmask", $lte2_netmask);
			api_ui_config_option_readonly("LTE2 Gateway", $lte2_gateway);
			
			api_ui_config_option_readonly("LTE3 IP Address", $lte3_ipaddr);
			api_ui_config_option_readonly("LTE3 Netmask", $lte3_netmask);
			api_ui_config_option_readonly("LTE3 Gateway", $lte3_gateway);
			
			api_ui_config_option_readonly("Wireless SSID", $wireless_ssid);
			api_ui_config_option_readonly("Wireless Key", $wireless_key);
			api_ui_config_option_readonly("Wireless Security (encryption)", $wireless_encryption);
			api_ui_config_option_readonly("Wireless RADIUS Authentication Server", $wireless_auth_server);
			api_ui_config_option_readonly("Wireless RADIUS Authentication Secret", $wireless_auth_secret);
			
			api_ui_config_option_readonly("Link aggregation mode", $aggpolicy_mode);
			api_ui_config_option_readonly("Link aggregation prefer", $aggpolicy);
			
			api_ui_config_option_readonly("Link High Usage Alert Threshold (Kb/s)", $sdwan_link_high_usage_threshold);
			
			print '</table><br>';
		}
	}
		

?>