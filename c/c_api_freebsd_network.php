<?php

function api_freebsd_if_port_dhcp($db, $port)
{	$output = chop(shell_exec("sysrc -n ifconfig_".$port));
	if($output=='DHCP') { return true; }
	
	return false;
}


function api_freebsd_get_port_macid($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w ether | awk '{print $2}'")); }

function api_freebsd_get_port_ipaddr($db, $port)
{ return chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $2}'")); }

function api_freebsd_get_port_netmask($db, $port)
{	$netmask = chop(shell_exec("ifconfig $port | grep -w inet | awk '{print $4}' | sed 's/0x// ; s/../& /g' | xargs | sed 's/ /./g'")); //gets us ff.ff.ff.0 format 
	//get 255.255.255.0 format
	$octets = explode('.',$netmask);
	$netmask = "";
	foreach($octets as $octet) 
	{  if($netmask!="") { $netmask.="."; }
		$netmask.=hexdec($octet);
	}
	
	return $netmask;
}

//single api which can set both static ipaddr and netmask
function api_freebsd_set_port_ip_mask($db, $port, $ip_mask_before, $ip_mask)
{	if($ip_mask_before!=$ip_mask)
	{  $output = chop(shell_exec("sysrc -n ifconfig_".$port));
		if($output!=null)
		{	$output = str_replace($ip_mask_before, $ip_mask, $output);
			$command = "sysrc ifconfig_".$port."='".$output."'";
		
			$query = "insert into smoad_jobs (job) values (\"$command\")"; $db->query($query);
			$query = "insert into smoad_jobs (job) values (\"/etc/netstart\")"; $db->query($query);
			
			print "<pre>NOTE: Settings will be updated shortly !</pre>";
			return true;
		}
		else { print "<pre>ERROR: Invalid port name or does not exist !</pre>"; }
	}
	return false;
}
 		  

?>