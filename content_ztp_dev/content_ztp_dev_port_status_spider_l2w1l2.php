<?php

	include('content_ztp_dev_port_status_common.php');



	$query = "select lan_cable_status, wan_cable_status, sdwan_cable_status,
					wan_link_status, wan2_link_status, sdwan_link_status, lte1_link_status, lte2_link_status, 
					wan_latency, wan2_latency, sdwan_latency, lte1_latency, lte2_latency, wan_jitter, wan2_jitter, sdwan_jitter, lte1_jitter, lte2_jitter   
					from smoad_device_network_cfg where device_serialnumber=\"$G_device_serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$lan_cable_status = get_port_cable_status_icon($row['lan_cable_status']);
			$wan_cable_status = get_port_cable_status_icon($row['wan_cable_status']);
			$sdwan_cable_status = get_port_cable_status_icon($row['sdwan_cable_status']);
			
			$wan_link_status = get_link_status_icon($row['wan_link_status']);
			$sdwan_link_status = get_link_status_icon($row['sdwan_link_status']);
			$lte1_link_status = get_link_status_icon($row['lte1_link_status']);
			$lte2_link_status = get_link_status_icon($row['lte2_link_status']);
			
			$wan_latency = get_link_latency($row['wan_latency']);
			$sdwan_latency = get_link_latency($row['sdwan_latency']);
			$lte1_latency = get_link_latency($row['lte1_latency']);
			$lte2_latency = get_link_latency($row['lte2_latency']);
			
			$wan_jitter = get_link_jitter($row['wan_jitter']);
			$sdwan_jitter = get_link_jitter($row['sdwan_jitter']);
			$lte1_jitter = get_link_jitter($row['lte1_jitter']);
			$lte2_jitter = get_link_jitter($row['lte2_jitter']);
		}
	}
	
	$link_status_wan_up_count = get_last_24_port_up_count($db, $G_device_serialnumber, "link_status_wan_up_count");
	$link_status_lte1_up_count = get_last_24_port_up_count($db, $G_device_serialnumber, "link_status_lte1_up_count");
	$link_status_lte2_up_count = get_last_24_port_up_count($db, $G_device_serialnumber, "link_status_lte2_up_count");
	$link_status_sdwan_up_count = get_last_24_port_up_count($db, $G_device_serialnumber, "link_status_sdwan_up_count");
	
	$link_status_wan_up_count_timestamp = get_last_port_up_count_timestamp($db, $G_device_serialnumber, "link_status_wan_up_count");
	$link_status_lte1_up_count_timestamp = get_last_port_up_count_timestamp($db, $G_device_serialnumber, "link_status_lte1_up_count");
	$link_status_lte2_up_count_timestamp = get_last_port_up_count_timestamp($db, $G_device_serialnumber, "link_status_lte2_up_count");
	$link_status_sdwan_up_count_timestamp = get_last_port_up_count_timestamp($db, $G_device_serialnumber, "link_status_sdwan_up_count");
	
	$link_status_wan_down_count = get_last_24_port_down_count($db, $G_device_serialnumber, "link_status_wan_down_count");
	$link_status_lte1_down_count = get_last_24_port_down_count($db, $G_device_serialnumber, "link_status_lte1_down_count");
	$link_status_lte2_down_count = get_last_24_port_down_count($db, $G_device_serialnumber, "link_status_lte2_down_count");
	$link_status_sdwan_down_count = get_last_24_port_down_count($db, $G_device_serialnumber, "link_status_sdwan_down_count");
	
	$link_status_wan_down_count_timestamp = get_last_port_down_count_timestamp($db, $G_device_serialnumber, "link_status_wan_down_count");
	$link_status_lte1_down_count_timestamp = get_last_port_down_count_timestamp($db, $G_device_serialnumber, "link_status_lte1_down_count");
	$link_status_lte2_down_count_timestamp = get_last_port_down_count_timestamp($db, $G_device_serialnumber, "link_status_lte2_down_count");
	$link_status_sdwan_down_count_timestamp = get_last_port_down_count_timestamp($db, $G_device_serialnumber, "link_status_sdwan_down_count");
	
	print '<div style="padding:12px;"><b>Port Status</b>';
	print '<table class="list_items" style="width:680px;text-align:center;">';
	print "<tr><td align=left>H/w Status</td>
				<td style=\"width:80px;\"><a title=\"Configure WAN port ?\" href=\"index.php?page=ztp_dev_wan&wanport=wan&skey=$session_key\" >$wan_cable_status</td>				
				<td style=\"width:80px;\"><a title=\"Configure SDWAN ?\" href=\"index.php?page=sdwan&skey=$session_key\" >$sdwan_cable_status</a></td>
				<td style=\"width:80px;\"><a title=\"Configure LAN port ?\" href=\"index.php?page=lan&skey=$session_key\" >$lan_cable_status</a></td>
				<td style=\"width:80px;\"><a title=\"Configure LTE1 port ?\" href=\"index.php?page=ztplte&lteport=lte1&skey=$_session_key\" ><img src=\"i/lte.png\" style=\"width:20px;height:20px;\" /></a></td>
            <td style=\"width:80px;\"><a title=\"Configure LTE2 port ?\" href=\"index.php?page=ztplte&lteport=lte2&skey=$_session_key\" ><img src=\"i/lte.png\" style=\"width:20px;height:20px;\" /></a></td>
				</tr>";
	print "<tr><td></td><td>WAN</td><td>SD-WAN</td><td>LAN</td><td>LTE1</td><td>LTE2</td></tr>";
	print "<tr title=\"Link Status is the real end-to-end network connectivity status. Hence it is not real-time !\"><td align=left>Link Status</td>
			<td>$wan_link_status</td><td>$sdwan_link_status</td><td>-</td><td>$lte1_link_status</td><td>$lte2_link_status</td></tr>";
			
	print "<tr title=\"Latency is the real end-to-end network latency status polled periodically. Hence it is not real-time !\"><td align=left>Latency</td>
			<td>$wan_latency</td><td>$sdwan_latency</td><td>-</td><td>$lte1_latency</td><td>$lte2_latency</td></tr>";
	
	print "<tr title=\"Jitter is the real end-to-end network jitter status (derived from latency) polled periodically. Hence it is not real-time !\"><td align=left>Jitter</td>
			<td>$wan_jitter</td><td>$sdwan_jitter</td><td>-</td><td>$lte1_jitter</td><td>$lte2_jitter</td></tr>";
			
	print "<tr title=\"Derived from port status last 24 hours. Hence it is not real-time !\"><td align=left>Link Up Count (past 24 hours)</td>
			<td>$link_status_wan_up_count</td><td>$link_status_sdwan_up_count</td><td>-</td><td>$link_status_lte1_up_count</td><td>$link_status_lte2_up_count</td></tr>";
			
	print "<tr title=\"\"><td align=left>Last Link Up Timestamp</td>
			<td>$link_status_wan_up_count_timestamp</td><td>$link_status_sdwan_up_count_timestamp</td><td>-</td><td>$link_status_lte1_up_count_timestamp</td><td>$link_status_lte2_up_count_timestamp</td></tr>";
	
	print "<tr title=\"Derived from port status last 24 hours. Hence it is not real-time !\"><td align=left>Link Down Count (past 24 hours)</td>
			<td>$link_status_wan_down_count</td><td>$link_status_sdwan_down_count</td><td>-</td><td>$link_status_lte1_down_count</td><td>$link_status_lte2_down_count</td></tr>";
			
	print "<tr title=\"\"><td align=left>Last Link Down Timestamp</td>
			<td>$link_status_wan_down_count_timestamp</td><td>$link_status_sdwan_down_count_timestamp</td><td>-</td><td>$link_status_lte1_down_count_timestamp</td><td>$link_status_lte2_down_count_timestamp</td></tr>";
			
	print "<tr title=\"\"><td align=left>Stats</td>";
	print "<td>"; get_stats_button($curr_page, "wan1", "wan"); print "</td>";
	print "<td>"; get_stats_button($curr_page, "sdwan", "sdwan"); print "</td>";
	print "<td>-</td>";
	print "<td>"; get_stats_button($curr_page, "lte1", "lte1"); print "</td>";
	print "<td>"; get_stats_button($curr_page, "lte2", "lte2"); print "</td>";
	print "</tr>";
			
	print '</table>';
	print '</div><br>';
?>


