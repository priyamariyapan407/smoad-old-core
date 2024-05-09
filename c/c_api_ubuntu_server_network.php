<?php

/*
function api_ubuntu_server_if_port_dhcp($db, $port)
{	$output = chop(shell_exec("sysrc -n ifconfig_".$port));
	if($output=='DHCP') { return true; }
	
	return false;
}*/

function mask2cdr($mask) 
{	$dq = explode(".",$mask);
   for ($i=0; $i<4 ; $i++) { $bin[$i]=str_pad(decbin($dq[$i]), 8, "0", STR_PAD_LEFT); }
   $bin = implode("",$bin); 
	return strlen(rtrim($bin,"0"));
}

function api_ubuntu_server_get_port_macid_kernel($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w ether | awk '{print $2}'")); }

function api_ubuntu_server_get_port_ipaddr_kernel($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $2}' | cut -d ':' -f 2")); }

function api_ubuntu_server_get_port_netmask_kernel($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $4}' | cut -d ':' -f 2")); }

//single api which can set both static ipaddr and netmask
function api_ubuntu_server_set_port_ip_mask_gw($db, $port, $proto, $ip, $mask, $gw)
{	if($port==null) {	print "<pre>ERROR: Invalid port name or does not exist !</pre>"; return false; }
	
	if($proto=='dhcp')
	{	$command = "sudo netplan set network.ethernets.".$port.".dhcp4=true";
		$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);

		$command = "sudo netplan apply";
		$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
		
		$query = "update smoad_port_cfg set ipaddr=\"0.0.0.0\", netmask=\"0.0.0.0\", gateway=\"0.0.0.0\" where port = \"$port\""; $db->query($query);
	}
	else if($proto=='static')
	{	if($ip==null || $mask==null) { return false; }
	
		$cidr = mask2cdr($mask);
		$command = "sudo netplan set network.ethernets.".$port.".addresses=[".$ip."/".$cidr."]";
		$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
		
		if($gw==null || $gw=="")
		{	$command = "sudo netplan set network.ethernets.".$port.".gateway4=";
			$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
			
			$query = "update smoad_port_cfg set gateway=\"$gw\" where port = \"$port\"";
			$db->query($query);
		}
		else
		{	$command = "sudo netplan set network.ethernets.".$port.".gateway4=$gw";
			$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
			
			$query = "update smoad_port_cfg set gateway=\"$gw\" where port = \"$port\"";
			$db->query($query);
		}
		
		$command = "sudo netplan set network.ethernets.".$port.".dhcp4=false";
		$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
	
		$command = "sudo netplan apply";
		$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
	
		$query = "update smoad_port_cfg set ipaddr=\"$ip\", netmask=\"$mask\" where port = \"$port\"";
		$db->query($query);
	}
	
	print "<pre>NOTE: Settings will be updated shortly !</pre>";
	return true;
}

function api_ubuntu_server_get_port_ipaddr($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $2}' | cut -d ':' -f 2")); }

function api_ubuntu_server_get_port_netmask($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $4}' | cut -d ':' -f 2")); }

 
//NOTE:
//change to dhcp from static 
//ifconfig eth0 0.0.0.0 0.0.0.0 && dhclient

//change back to static from dhcp
//killall dhclient && ifconfig eth0 10.0.1.22 netmask 255.255.255.0
//ip route add 192.168.1.0/24 dev eth0	

//$ ifconfig eth0 up 
//$ sudo dhclient eth0

//To set IP address you want (for example 192.168.0.1) type:

//ifconfig eth0 192.168.0.1 netmask 255.255.255.0 up
//route add default gw GATEWAY-IP eth0
	  

?>