<?php

//select the appropriate query


function sm_get_sds_wg_peers_config_by_serialnumber($db, $serialnumber, $type, $value)
{	
	$query=null;
	print "$type \n";
	if($type=="smoad_wg_peers.update_peer") 
	{ 	$items = explode("|", $value);
		$id_peer = $items[0];
		$device_serialnumber = $items[1];
		$prikey = hex2bin($items[2]);
		$pubkey = hex2bin($items[3]);		
		$allowedipsubnet = $items[4];
		$vxlan_id = $items[5];
		$vlan_id = $items[6];
		$status = $items[7];
		$sdwan_link_status = $items[8];
		$sdwan_link_status_up_count = $items[9];
		$sdwan_link_status_last_up_timestamp = $items[10];
		$data_transfer = $items[11];
		$handshake = $items[12];

		//find matching entry - id_peer
		$found_entry=false;
		$query = "select id from smoad_sds_wg_peers where serialnumber = \"$serialnumber\" and device_serialnumber='$device_serialnumber' and id_peer=$id_peer";
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$found_entry=true;
			}
		}
		
		//find matching entry - of serialnumber (i.e gw) - but different id_peer or serialnumber combo (then this is a stray entry in the DB, so delete it)
		$query = "select id, id_peer, device_serialnumber from smoad_sds_wg_peers where serialnumber = \"$serialnumber\" and id_peer=$id_peer";
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$_id_peer = $row['id_peer'];
				$_id = $row['id'];
				$_device_serialnumber = $row['device_serialnumber'];
				if($id_peer!=$_id_peer || $_device_serialnumber!=$device_serialnumber) 
				{	 $query2 = "delete from smoad_sds_wg_peers where id=$_id";
					 $db->query($query2);
				}
			}
		}
		
		//$query = "delete from smoad_sds_wg_peers where serialnumber = \"$serialnumber\" and id_peer=$id_peer";
		//$db->query($query);
		if($found_entry==false)
		{	$query = "insert into smoad_sds_wg_peers 
						(serialnumber, device_serialnumber, id_peer, prikey, pubkey, allowedipsubnet, vxlan_id, vlan_id, status, 
						 sdwan_link_status, sdwan_link_status_up_count, sdwan_link_status_last_up_timestamp, 
						 data_transfer, handshake) values
						(\"$serialnumber\", \"$device_serialnumber\", $id_peer, \"$prikey\", \"$pubkey\", \"$allowedipsubnet\", $vxlan_id, 
							$vlan_id, \"$status\", \"$sdwan_link_status\", \"$sdwan_link_status_up_count\", \"$sdwan_link_status_last_up_timestamp\", 
							\"$data_transfer\", \"$handshake\")";
		}
		else
		{	$query = "update smoad_sds_wg_peers set  
							prikey=\"$prikey\", pubkey=\"$pubkey\", allowedipsubnet=\"$allowedipsubnet\", 
							vxlan_id=$vxlan_id, vlan_id=$vlan_id, status=\"$status\", sdwan_link_status=\"$sdwan_link_status\", 
							sdwan_link_status_up_count = $sdwan_link_status_up_count, sdwan_link_status_last_up_timestamp = \"$sdwan_link_status_last_up_timestamp\",
							data_transfer=\"$data_transfer\", handshake=\"$handshake\" 
							where id_peer=$id_peer and serialnumber = \"$serialnumber\"";
		}				
		//print "$query\n";
		 
	}
	
	return $query;
} /* sm_get_sds_wg_peers_config_by_serialnumber */


function sm_get_sds_wg_server_config_by_serialnumber($serialnumber, $type, $value)
{	print "sm_get_sds_wg_server_config_by_serialnumber $serialnumber $type $value\n";
	$query=null;
	if($type=="smoad_wg_server.address")
	{ $query = "update smoad_sdwan_servers set address='$value' where serialnumber='$serialnumber'"; }
	else if($type=="smoad_wg_server.prikey")
	{  $_value = hex2bin($value);
		$query = "update smoad_sdwan_servers set prikey='$_value' where serialnumber='$serialnumber'"; 
	}
	else if($type=="smoad_wg_server.pubkey")
	{ 	$_value = hex2bin($value);
		$query = "update smoad_sdwan_servers set pubkey='$_value' where serialnumber='$serialnumber'"; 
	}
	print ">> $query\n";
	return $query;
} /* sm_get_sds_wg_server_config_by_serialnumber */

function sm_get_sds_gw_qos_logs($serialnumber, $type, $value)
{	print "sm_get_sds_gw_qos_logs $serialnumber $type $value\n";
	$query=null;
	if($type=="smoad_wg_server.qos_log")
	{ 	$items = explode("|", $value);
		$id_peer = $items[0];
		$vxlan_id = $items[0];
		$device_serialnumber = $items[1];
		$http_rx_bytes = $items[2]; $http_tx_bytes = $items[3]; $http_rx_pkts = $items[4]; $http_tx_pkts = $items[5];
		$https_rx_bytes = $items[6]; $https_tx_bytes = $items[7]; $https_rx_pkts = $items[8]; $https_tx_pkts = $items[9];
		$iperf_rx_bytes = $items[10]; $iperf_tx_bytes = $items[11]; $iperf_rx_pkts = $items[12]; $iperf_tx_pkts = $items[13];
		$zoom_rx_bytes = $items[14]; $zoom_tx_bytes = $items[15]; $zoom_rx_pkts = $items[16]; $zoom_tx_pkts = $items[17];
		$microsoft_teams_rx_bytes = $items[18]; $microsoft_teams_tx_bytes = $items[19]; $microsoft_teams_rx_pkts = $items[20]; $microsoft_teams_tx_pkts = $items[21];
		$skype_rx_bytes = $items[22]; $skype_tx_bytes = $items[23]; $skype_rx_pkts = $items[24]; $skype_tx_pkts = $items[25];
		$voip_rx_bytes = $items[26]; $voip_tx_bytes = $items[27]; $voip_rx_pkts = $items[28]; $voip_tx_pkts = $items[29];
		$other_rx_bytes = $items[30]; $other_tx_bytes = $items[31]; $other_rx_pkts = $items[32]; $other_tx_pkts = $items[33];
		$icmp_rx_bytes = $items[34]; $icmp_tx_bytes = $items[35]; $icmp_rx_pkts = $items[36]; $icmp_tx_pkts = $items[37];
		$tcp_rx_bytes = $items[38]; $tcp_tx_bytes = $items[39]; $tcp_rx_pkts = $items[40]; $tcp_tx_pkts = $items[41];
		$udp_rx_bytes = $items[42]; $udp_tx_bytes = $items[43]; $udp_rx_pkts = $items[44]; $udp_tx_pkts = $items[45];
		$id_rand_key = $items[46]; $log_timestamp = $items[47];
		
		$query = "insert into smoad_device_network_qos_stats_log 
		      	(vxlan_id, device_serialnumber, 
		      		http_rx_bytes, http_tx_bytes, http_rx_pkts, http_tx_pkts, 
						https_rx_bytes, https_tx_bytes, https_rx_pkts, https_tx_pkts,
						iperf_rx_bytes, iperf_tx_bytes, iperf_rx_pkts, iperf_tx_pkts,
						zoom_rx_bytes, zoom_tx_bytes, zoom_rx_pkts, zoom_tx_pkts,
						microsoft_teams_rx_bytes, microsoft_teams_tx_bytes, microsoft_teams_rx_pkts, microsoft_teams_tx_pkts,
						skype_rx_bytes, skype_tx_bytes, skype_rx_pkts, skype_tx_pkts,
						voip_rx_bytes, voip_tx_bytes, voip_rx_pkts, voip_tx_pkts,
						other_rx_bytes, other_tx_bytes, other_rx_pkts, other_tx_pkts,
						icmp_rx_bytes, icmp_tx_bytes, icmp_rx_pkts, icmp_tx_pkts,
						tcp_rx_bytes, tcp_tx_bytes, tcp_rx_pkts, tcp_tx_pkts,
						udp_rx_bytes, udp_tx_bytes, udp_rx_pkts, udp_tx_pkts,
						id_rand_key, log_timestamp)
		      	values ($vxlan_id, '$serialnumber',
		      		$http_rx_bytes, $http_tx_bytes, $http_rx_pkts, $http_tx_pkts, 
						$https_rx_bytes, $https_tx_bytes, $https_rx_pkts, $https_tx_pkts,
						$iperf_rx_bytes, $iperf_tx_bytes, $iperf_rx_pkts, $iperf_tx_pkts,
						$zoom_rx_bytes, $zoom_tx_bytes, $zoom_rx_pkts, $zoom_tx_pkts,
						$microsoft_teams_rx_bytes, $microsoft_teams_tx_bytes, $microsoft_teams_rx_pkts, $microsoft_teams_tx_pkts,
						$skype_rx_bytes, $skype_tx_bytes, $skype_rx_pkts, $skype_tx_pkts,
						$voip_rx_bytes, $voip_tx_bytes, $voip_rx_pkts, $voip_tx_pkts,
						$other_rx_bytes, $other_tx_bytes, $other_rx_pkts, $other_tx_pkts,
						$icmp_rx_bytes, $icmp_tx_bytes, $icmp_rx_pkts, $icmp_tx_pkts,
						$tcp_rx_bytes, $tcp_tx_bytes, $tcp_rx_pkts, $tcp_tx_pkts,
						$udp_rx_bytes, $udp_tx_bytes, $udp_rx_pkts, $udp_tx_pkts,
						'$id_rand_key', '$log_timestamp')";

	}
	
	print ">> $query\n";
	return $query;
} /* sm_get_sds_gw_qos_logs */

?>