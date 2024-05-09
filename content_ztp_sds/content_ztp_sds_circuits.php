
<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>ID Peer</th><th>Details</th>
<th>Serial Number</th><th>Public Key</th><th>Allowed IPs</th><th>VLAN-ID</th><th>VXLAN-ID</th><th>Tunnel Status</th><th>Link Status</th>
<th>Link Up Count</th><th>Last Link Up Timestamp</th>
<th>Data Transfer</th><th>Handshake</th><th>Timestamp</th><th>Soft-client Config</th></tr>

<?php

$query = "select id, id_peer, details, license, device_serialnumber, pubkey, prikey, allowedipsubnet, vlan_id, vxlan_id, new_key_device_sync_timestamp,
	status, sdwan_link_status, sdwan_link_status_up_count, sdwan_link_status_last_up_timestamp, 
	data_transfer, handshake, enable, updated from smoad_sds_wg_peers where serialnumber=\"$G_sds_serialnumber\" 
	order by id_peer "; 

	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$id_peer = $row['id_peer'];
			$details = $row['details'];
			$license = $row['license'];
			$device_serialnumber = $row['device_serialnumber'];
			$pubkey = $row['pubkey']; $prikey = $row['prikey'];			
			$allowedipsubnet = $row['allowedipsubnet'];
			$vlan_id = $row['vlan_id'];
			$vxlan_id = $row['vxlan_id'];
			$new_key_device_sync_timestamp = $row['new_key_device_sync_timestamp'];
			$status = $row['status'];
			$sdwan_link_status = $row['sdwan_link_status'];
			$sdwan_link_status_up_count = $row['sdwan_link_status_up_count'];
			$sdwan_link_status_last_up_timestamp = $row['sdwan_link_status_last_up_timestamp'];
			$handshake = $row['handshake'];
			$data_transfer = $row['data_transfer'];
			$enable = $row['enable'];
			$updated = $row['updated'];
		

			
			print "<tr ><td >$id</td><td >$id_peer</td><td>$details</td>";
			//	 <td $bg_style>$license</td>
			print "<td>$device_serialnumber</td><td>$pubkey<br>$prikey</td><td>$allowedipsubnet</td>
			<td>$vlan_id</td><td>$vxlan_id</td>";

			if($status=="UP") { $status = api_ui_up_down_display_status(1, null); }
			else if($status=="DOWN") { $status = api_ui_up_down_display_status(0, null); }
			else if($status=="UP_WAITING") { $status = api_ui_up_down_display_status(2, "*"); }
			print "<td>$status</td>";
			
			if($sdwan_link_status=="UP") { $sdwan_link_status = api_ui_up_down_display_status(1, null); }
			else if($sdwan_link_status=="DOWN") { $sdwan_link_status = api_ui_up_down_display_status(0, null); }
			print "<td>$sdwan_link_status</td>";
			
			print "<td>$sdwan_link_status_up_count</td><td>$sdwan_link_status_last_up_timestamp</td><td>$data_transfer</td><td>$handshake</td><td>$updated</td>";
			
			print "<td $bg_style><form method=\"POST\" action=\"index.php?page=ztp_sds_softclient_config&skey=$session_key\" >
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Soft-client Configuration\" class=\"top_title_icons\" />
					</form></td>";
		
			print "</tr>";
		}
	}
?>
</table>





