<?php

//select the appropriate query

function sm_get_device_sm_config_by_serialnumber($serialnumber, $type, $value)
{	$query=null;
	if($type=="smoad.device.aggpolicy_mode")
	{ $query = "update smoad_device_network_cfg set aggpolicy_mode='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.device.aggpolicy")
	{ $query = "update smoad_device_network_cfg set aggpolicy='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.device.firmware")
	{ $query = "update smoad_devices set firmware='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.device.update_firmware")
	{ $query = "update smoad_devices set firmware_status='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.device.uptime")
	{ $query = "update smoad_devices set uptime='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.device.wg")
	{	if($value==1) { $value="TRUE"; } else { $value="FALSE"; } 
		$query = "update smoad_devices set sdwan_enable='$value' where serialnumber='$serialnumber'"; 
	}

	return $query;
} /* sm_get_device_sm_config_by_serialnumber */

function sm_get_device_lan_config_by_serialnumber($serialnumber, $type, $value, $port)
{	$query=null;

	if($type=="network.".$port.".ipaddr") { $query = "update smoad_device_network_cfg set ".$port."_ipaddr='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".netmask") { $query = "update smoad_device_network_cfg set ".$port."_netmask='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".link_status") { $query = "update smoad_device_network_cfg set ".$port."_link_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".cable_status") { $query = "update smoad_device_network_cfg set ".$port."_cable_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".latency") { $query = "update smoad_device_network_cfg set ".$port."_latency='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".jitter") { $query = "update smoad_device_network_cfg set ".$port."_jitter='$value' where device_serialnumber='$serialnumber'"; }

	return $query;
} /* sm_get_device_lan_config_by_serialnumber */

function sm_get_device_wan_config_by_serialnumber($serialnumber, $type, $value, $port)
{	$query=null;

	if($type=="network.".$port.".proto") { $query = "update smoad_device_network_cfg set ".$port."_proto='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".ipaddr") { $query = "update smoad_device_network_cfg set ".$port."_ipaddr='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".netmask") { $query = "update smoad_device_network_cfg set ".$port."_netmask='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".gateway") { $query = "update smoad_device_network_cfg set ".$port."_gateway='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".dns") { $query = "update smoad_device_network_cfg set ".$port."_dns='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".username") { $query = "update smoad_device_network_cfg set ".$port."_username='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".password") { $query = "update smoad_device_network_cfg set ".$port."_password='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".link_status") { $query = "update smoad_device_network_cfg set ".$port."_link_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".cable_status") { $query = "update smoad_device_network_cfg set ".$port."_cable_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".latency") { $query = "update smoad_device_network_cfg set ".$port."_latency='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".jitter") { $query = "update smoad_device_network_cfg set ".$port."_jitter='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_max_bandwidth") { $query = "update smoad_device_network_cfg set ".$port."_max_bandwidth='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_medium_bandwidth_pct") { $query = "update smoad_device_network_cfg set ".$port."_medium_bandwidth_pct='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_low_bandwidth_pct") { $query = "update smoad_device_network_cfg set ".$port."_low_bandwidth_pct='$value' where device_serialnumber='$serialnumber'"; }

	return $query;
} /* sm_get_device_wan_config_by_serialnumber */

function sm_get_device_sdwan_config_by_serialnumber($serialnumber, $type, $value)
{	$query=null;

	if($type=="network.sdwan.link_status") { $query = "update smoad_device_network_cfg set sdwan_link_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.sdwan.cable_status") { $query = "update smoad_device_network_cfg set sdwan_cable_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.sdwan.latency") { $query = "update smoad_device_network_cfg set sdwan_latency='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.sdwan.jitter") { $query = "update smoad_device_network_cfg set sdwan_jitter='$value' where device_serialnumber='$serialnumber'"; }

	return $query;
} /* sm_get_device_sdwan_config_by_serialnumber */

function sm_get_device_qos_config_by_serialnumber($serialnumber, $type, $value)
{	$query=null;
	
	if($type=="smoad.qos.enabled")
	{	if($value==1) { $value="TRUE"; } else { $value="FALSE"; } 
		$query = "update smoad_devices set qos_enabled='$value' where serialnumber='$serialnumber'"; 
	}
	else if($type=="smoad.qos.microsoft_teams") { $query = "update smoad_devices set qos_microsoft_teams='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.youtube") { $query = "update smoad_devices set qos_youtube='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.iperf") { $query = "update smoad_devices set qos_iperf='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.voip") { $query = "update smoad_devices set qos_voip='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.skype") { $query = "update smoad_devices set qos_skype='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.zoom") { $query = "update smoad_devices set qos_zoom='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.sdwan") { $query = "update smoad_devices set qos_sdwan='$value' where serialnumber='$serialnumber'"; }
	return $query;
} /* sm_get_device_qos_config_by_serialnumber */

function sm_get_device_lte_config_by_serialnumber($serialnumber, $type, $value, $port)
{	$query=null;

	if($type=="network.".$port.".ipaddr") { $query = "update smoad_device_network_cfg set ".$port."_ipaddr='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".netmask") { $query = "update smoad_device_network_cfg set ".$port."_netmask='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".gateway") { $query = "update smoad_device_network_cfg set ".$port."_gateway='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".carrier") { $query = "update smoad_device_network_cfg set ".$port."_carrier='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".imei") { $query = "update smoad_device_network_cfg set ".$port."_imei='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".signal_strength") { $query = "update smoad_device_network_cfg set ".$port."_signal_strength='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".link_status") { $query = "update smoad_device_network_cfg set ".$port."_link_status='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".latency") { $query = "update smoad_device_network_cfg set ".$port."_latency='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="network.".$port.".jitter") { $query = "update smoad_device_network_cfg set ".$port."_jitter='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_max_bandwidth") { $query = "update smoad_device_network_cfg set ".$port."_max_bandwidth='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_medium_bandwidth_pct") { $query = "update smoad_device_network_cfg set ".$port."_medium_bandwidth_pct='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="smoad.qos.".$port."_low_bandwidth_pct") { $query = "update smoad_device_network_cfg set ".$port."_low_bandwidth_pct='$value' where device_serialnumber='$serialnumber'"; }

	return $query;
} /* sm_get_device_lte_config_by_serialnumber */

function sm_get_device_wifi_config_by_serialnumber($serialnumber, $type, $value)
{	$query=null;

	if($type=="wireless.default_radio0.ssid") { $query = "update smoad_device_network_cfg set wireless_ssid='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="wireless.default_radio0.key") { $query = "update smoad_device_network_cfg set wireless_key='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="wireless.default_radio0.encryption") { $query = "update smoad_device_network_cfg set wireless_encryption='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="wireless.default_radio0.auth_server") { $query = "update smoad_device_network_cfg set wireless_auth_server='$value' where device_serialnumber='$serialnumber'"; }
	else if($type=="wireless.default_radio0.auth_secret") { $query = "update smoad_device_network_cfg set wireless_auth_secret='$value' where device_serialnumber='$serialnumber'"; }

	//print "$query \n";
	
	return $query;
} /* sm_get_device_wifi_config_by_serialnumber */

function sm_get_device_port_branching_by_serialnumber($port)
{	$model = $GLOBALS['G_device_model'];
	$model_variant = $GLOBALS['G_device_model_variant'];
   if($port == "WAN")
   {	//wan1 port is there for all variants
	  	return true; 
   }
	elseif($port == "WAN2")
	{	if(($model=='vm' && $model_variant=="l2") || ($model=='vm' && $model_variant=="l3") ||
         ($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") ||
         ($model=='spider2' && $model_variant=="l2") || ($model=='spider2' && $model_variant=="l3") ||
         ($model=='beetle' && $model_variant=="l2") || ($model=='beetle' && $model_variant=="l3") ||
         ($model=='bumblebee' && $model_variant=="l2") || ($model=='bumblebee' && $model_variant=="l3"))
         return true; 	
	}
	elseif($port == "WAN3")
   {	if(($model=='spider2' && $model_variant=="l3"))
   	return true; 
   }	
   elseif($port == "LTE1")
   {	if(($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") || ($model=='spider' && $model_variant=="l2w1l2") ||
	      ($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3") ||
	      ($model=='beetle' && $model_variant=="l2") ||($model=='beetle' && $model_variant=="l3") ||
	      ($model=='bumblebee' && $model_variant=="l2") || ($model=='bumblebee' && $model_variant=="l3"))
	      return true;      
   }
   elseif($port == "LTE2")
   {	if(($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") || ($model=='spider' && $model_variant=="l2w1l2") ||
         ($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3"))
         return true; 
   }
   elseif($port == "LTE3")
   {	if(($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3"))
   	return true; 
   }
   elseif($port == "LAN")
   {	//lan port is there for all variants
   	return true;
   }
   elseif($port == "WIRELESS")
   {	//wifi port is there for all variants
   	return true;
   }
   elseif($port == "SD-WAN")
   {	//sdwan port is there for all variants
   	return true;
   }
	
	return false;
} /* sm_get_device_port_branching_by_serialnumber */

?>