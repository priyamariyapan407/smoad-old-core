<br>
<?php
$_id = $_POST['id'];

	$query = "select id, prikey, allowedipsubnet, device_serialnumber from smoad_sds_wg_peers where id = $_id "; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$prikey = $row['prikey'];
			$allowedipsubnet = $row['allowedipsubnet'];
			$device_serialnumber = $row['device_serialnumber'];
		}
	}
	$query = "select pubkey, ipaddr from smoad_sdwan_servers where serialnumber = \"$G_sds_serialnumber\" "; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$pubkey = $row['pubkey'];
			$ipaddr = $row['ipaddr'];
		}
	}
	
	$config = "[Interface]\n";
	$config .= "PrivateKey = $prikey\n";
	$config .= "Address = $allowedipsubnet\n";
	$config .= "DNS = 8.8.8.8\n";
	$config .= "\n";
	$config .= "[Peer]\n";
	$config .= "PublicKey = $pubkey\n";
	$config .= "AllowedIPs = 0.0.0.0/0\n";
	$config .= "Endpoint = $ipaddr".":51820\n";
	
	print "<pre>$config</pre>";
	file_put_contents("/var/www/html/softclient.conf", $config);
	
	print "<a href=\"softclient.conf\" download=\"SMOADsoftclient".$device_serialnumber.".conf\"><img src=\"i/download.png\" 
		title=\"Download Soft-client configuration file for the EDGE: $device_serialnumber !\" /></a>";
?>

