<br>
<?php
$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

$_date = $_GET['date'];
$date = explode('-', $_date);

if($_date!=null) { $where_clause_date=" year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1] "; }

if($where_clause_date!=null) 
{  if($where_clause==null) { $where_clause=" where "; } //first where
	$where_clause .= $where_clause_date; 
}

$total_items=0; $total_pages=0;
api_ui_pagination_get_total_items_total_pages($db, 'smoad_device_network_stats_log', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, $curr_page);
$limitstart = ($_page-1)*$G_items_per_page;

?>


<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>LAN</th><th>WAN1</th><th>WAN2</th><th>LTE1</th><th>LTE2</th><th>LTE3</th><th>SD-WAN</th><th>Timestamp</th></tr>

<?php


function _get_network_bytes_rate_per_port_from_db($db, $id, $serialnumber, $port, &$rx_bytes_rate, &$tx_bytes_rate)
{	$rx_bytes_rate = $tx_bytes_rate = 0;
	return;
	$query2 = "SELECT g1.id as id,
			    g1.device_serialnumber,
			    g1.log_timestamp log_timestamp_from,
			    g2.log_timestamp log_timestamp_to,
				 (UNIX_TIMESTAMP(g2.log_timestamp) - UNIX_TIMESTAMP(g1.log_timestamp)) AS log_timestamp_diff,
				 g1.".$port."_rx_bytes ".$port."_rx_bytes_from,
			    g2.".$port."_rx_bytes ".$port."_rx_bytes_to,
			    (g2.".$port."_rx_bytes - g1.".$port."_rx_bytes) AS rx_bytes_diff,
			  	 g1.".$port."_tx_bytes ".$port."_tx_bytes_from,
			    g2.".$port."_tx_bytes ".$port."_tx_bytes_to,
			    (g2.".$port."_tx_bytes - g1.".$port."_tx_bytes) AS tx_bytes_diff
			FROM smoad_device_network_stats_log g1
			INNER JOIN smoad_device_network_stats_log g2 ON g2.id = g1.id + 1
			WHERE g1.device_serialnumber = \"$serialnumber\" and g1.id = $id ORDER BY g1.id DESC LIMIT 1";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$_id = $row2['id'];
			$_log_timestamp_diff = $row2['log_timestamp_diff'];
			$_rx_bytes_diff = $row2['rx_bytes_diff'];
			$_tx_bytes_diff = $row2['tx_bytes_diff'];
			
			//print "-> $_id $_log_timestamp_diff <br>";
			
			$rx_bytes_rate = api_net_stats_get_disp($_rx_bytes_diff/$_log_timestamp_diff);
			$tx_bytes_rate = api_net_stats_get_disp($_tx_bytes_diff/$_log_timestamp_diff);
		}	
	}
}

$query = "select id, device_serialnumber, 
	 lan_rx_bytes, lan_rx_bytes_rate, lan_rx_pkts, lan_rx_drop, lan_tx_bytes, lan_tx_bytes_rate, lan_tx_pkts, lan_tx_drop, 
	 wan1_rx_bytes, wan1_rx_bytes_rate, wan1_rx_pkts, wan1_rx_drop, wan1_tx_bytes, wan1_tx_bytes_rate, wan1_tx_pkts, wan1_tx_drop,
	 wan2_rx_bytes, wan2_rx_bytes_rate, wan2_rx_pkts, wan2_rx_drop, wan2_tx_bytes, wan2_tx_bytes_rate, wan2_tx_pkts, wan2_tx_drop,
	 lte1_rx_bytes, lte1_rx_bytes_rate, lte1_rx_pkts, lte1_rx_drop, lte1_tx_bytes, lte1_tx_bytes_rate, lte1_tx_pkts, lte1_tx_drop,
	 lte2_rx_bytes, lte2_rx_bytes_rate, lte2_rx_pkts, lte2_rx_drop, lte2_tx_bytes, lte2_tx_bytes_rate, lte2_tx_pkts, lte2_tx_drop,
	 lte3_rx_bytes, lte3_rx_bytes_rate, lte3_rx_pkts, lte3_rx_drop, lte3_tx_bytes, lte3_tx_bytes_rate, lte3_tx_pkts, lte3_tx_drop,
	 sdwan_rx_bytes, sdwan_rx_bytes_rate, sdwan_rx_pkts, sdwan_rx_drop, sdwan_tx_bytes, sdwan_tx_bytes_rate, sdwan_tx_pkts, sdwan_tx_drop,  
	 log_timestamp, id_rand_key, consolidated 
	 from smoad_device_network_stats_log where device_serialnumber=\"$G_device_serialnumber\" 
	 order by id desc limit $limitstart".",$G_items_per_page"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$_lan_rx_bytes = $row['lan_rx_bytes']; $_lan_rx_bytes_rate = $row['lan_rx_bytes_rate']; $_lan_rx_pkts = $row['lan_rx_pkts']; $_lan_rx_drop = $row['lan_rx_drop']; 
			$_lan_tx_bytes = $row['lan_tx_bytes']; $_lan_tx_bytes_rate = $row['lan_tx_bytes_rate']; $_lan_tx_pkts = $row['lan_tx_pkts']; $_lan_tx_drop = $row['lan_tx_drop']; 
	  		$_wan1_rx_bytes = $row['wan1_rx_bytes']; $_wan1_rx_bytes_rate = $row['wan1_rx_bytes_rate']; $_wan1_rx_pkts = $row['wan1_rx_pkts']; $_wan1_rx_drop = $row['wan1_rx_drop']; 
			$_wan1_tx_bytes = $row['wan1_tx_bytes']; $_wan1_tx_bytes_rate = $row['wan1_tx_bytes_rate']; $_wan1_tx_pkts = $row['wan1_tx_pkts']; $_wan1_tx_drop = $row['wan1_tx_drop'];
	  		$_wan2_rx_bytes = $row['wan2_rx_bytes']; $_wan2_rx_bytes_rate = $row['wan2_rx_bytes_rate']; $_wan2_rx_pkts = $row['wan2_rx_pkts']; $_wan2_rx_drop = $row['wan2_rx_drop']; 
			$_wan2_tx_bytes = $row['wan2_tx_bytes']; $_wan2_tx_bytes_rate = $row['wan2_tx_bytes_rate']; $_wan2_tx_pkts = $row['wan2_tx_pkts']; $_wan2_tx_drop = $row['wan2_tx_drop'];
	  		$_lte1_rx_bytes = $row['lte1_rx_bytes']; $_lte1_rx_bytes_rate = $row['lte1_rx_bytes_rate']; $_lte1_rx_pkts = $row['lte1_rx_pkts']; $_lte1_rx_drop = $row['lte1_rx_drop']; 
			$_lte1_tx_bytes = $row['lte1_tx_bytes']; $_lte1_tx_bytes_rate = $row['lte1_tx_bytes_rate']; $_lte1_tx_pkts = $row['lte1_tx_pkts']; $_lte1_tx_drop = $row['lte1_tx_drop'];
	  		$_lte2_rx_bytes = $row['lte2_rx_bytes']; $_lte2_rx_bytes_rate = $row['lte2_rx_bytes_rate']; $_lte2_rx_pkts = $row['lte2_rx_pkts']; $_lte2_rx_drop = $row['lte2_rx_drop']; 
			$_lte2_tx_bytes = $row['lte2_tx_bytes']; $_lte2_tx_bytes_rate = $row['lte2_tx_bytes_rate']; $_lte2_tx_pkts = $row['lte2_tx_pkts']; $_lte2_tx_drop = $row['lte2_tx_drop'];
			$_lte3_rx_bytes = $row['lte3_rx_bytes']; $_lte3_rx_bytes_rate = $row['lte3_rx_bytes_rate']; $_lte3_rx_pkts = $row['lte3_rx_pkts']; $_lte3_rx_drop = $row['lte3_rx_drop']; 
			$_lte3_tx_bytes = $row['lte3_tx_bytes']; $_lte3_tx_bytes_rate = $row['lte3_tx_bytes_rate']; $_lte3_tx_pkts = $row['lte3_tx_pkts']; $_lte3_tx_drop = $row['lte3_tx_drop'];
	  		$_sdwan_rx_bytes = $row['sdwan_rx_bytes']; $_sdwan_rx_bytes_rate = $row['sdwan_rx_bytes_rate']; $_sdwan_rx_pkts = $row['sdwan_rx_pkts']; $_sdwan_rx_drop = $row['sdwan_rx_drop']; 
			$_sdwan_tx_bytes = $row['sdwan_tx_bytes']; $_sdwan_tx_bytes_rate = $row['sdwan_tx_bytes_rate']; $_sdwan_tx_pkts = $row['sdwan_tx_pkts']; $_sdwan_tx_drop = $row['sdwan_tx_drop'];
			$timestamp = $row['log_timestamp'];
			$id_rand_key = $row['id_rand_key'];
			$consolidated = $row['consolidated'];
			if($consolidated==1) { $consolidated="<img src=\"i/archive.png\" alt=\"Consolidated log entry !\" title=\"Consolidated log entry !\" class=\"top_title_icons\" />"; } else { $consolidated=""; }
			
			print "<tr><td>$id</td>";
		
			$_lan_rx_bytes = api_net_stats_get_disp($_lan_rx_bytes); $_lan_tx_bytes = api_net_stats_get_disp($_lan_tx_bytes);
			$_lan_rx_bytes_rate = api_net_rate_stats_get_disp($_lan_rx_bytes_rate); $_lan_tx_bytes_rate = api_net_rate_stats_get_disp($_lan_tx_bytes_rate);
			print "<td>RxB $_lan_rx_bytes<br>RxP $_lan_rx_pkts<br>RxD $_lan_rx_drop<br>Rate $_lan_rx_bytes_rate<br><br>  
						TxB $_lan_tx_bytes<br>TxP $_lan_tx_pkts<br>TxD $_lan_tx_drop<br>Rate $_lan_tx_bytes_rate<br></td>";
			
			$_wan1_rx_bytes = api_net_stats_get_disp($_wan1_rx_bytes); $_wan1_tx_bytes = api_net_stats_get_disp($_wan1_tx_bytes);
			$_wan1_rx_bytes_rate = api_net_rate_stats_get_disp($_wan1_rx_bytes_rate); $_wan1_tx_bytes_rate = api_net_rate_stats_get_disp($_wan1_tx_bytes_rate);
			print "<td>RxB $_wan1_rx_bytes<br>RxP $_wan1_rx_pkts<br>RxD $_wan1_rx_drop<br>Rate $_wan1_rx_bytes_rate<br><br>  
						TxB $_wan1_tx_bytes<br>TxP $_wan1_tx_pkts<br>TxD $_wan1_tx_drop<br>Rate $_wan1_tx_bytes_rate<br></td>";
			
			$_wan2_rx_bytes = api_net_stats_get_disp($_wan2_rx_bytes); $_wan2_tx_bytes = api_net_stats_get_disp($_wan2_tx_bytes);
			$_wan2_rx_bytes_rate = api_net_rate_stats_get_disp($_wan2_rx_bytes_rate); $_wan2_tx_bytes_rate = api_net_rate_stats_get_disp($_wan2_tx_bytes_rate);
			print "<td>RxB $_wan2_rx_bytes<br>RxP $_wan2_rx_pkts<br>RxD $_wan2_rx_drop<br>Rate $_wan2_rx_bytes_rate<br><br>  
						TxB $_wan2_tx_bytes<br>TxP $_wan2_tx_pkts<br>TxD $_wan2_tx_drop<br>Rate $_wan2_tx_bytes_rate<br></td>";
			
			$_lte1_rx_bytes = api_net_stats_get_disp($_lte1_rx_bytes); $_lte1_tx_bytes = api_net_stats_get_disp($_lte1_tx_bytes);
			$_lte1_rx_bytes_rate = api_net_rate_stats_get_disp($_lte1_rx_bytes_rate); $_lte1_tx_bytes_rate = api_net_rate_stats_get_disp($_lte1_tx_bytes_rate);
			print "<td>RxB $_lte1_rx_bytes<br>RxP $_lte1_rx_pkts<br>RxD $_lte1_rx_drop<br>Rate $_lte1_rx_bytes_rate<br><br>  
						TxB $_lte1_tx_bytes<br>TxP $_lte1_tx_pkts<br>TxD $_lte1_tx_drop<br>Rate $_lte1_tx_bytes_rate<br></td>";
			
			$_lte2_rx_bytes = api_net_stats_get_disp($_lte2_rx_bytes); $_lte2_tx_bytes = api_net_stats_get_disp($_lte2_tx_bytes);
			$_lte2_rx_bytes_rate = api_net_rate_stats_get_disp($_lte2_rx_bytes_rate); $_lte2_tx_bytes_rate = api_net_rate_stats_get_disp($_lte2_tx_bytes_rate);
			print "<td>RxB $_lte2_rx_bytes<br>RxP $_lte2_rx_pkts<br>RxD $_lte2_rx_drop<br>Rate $_lte2_rx_bytes_rate<br><br>  
						TxB $_lte2_tx_bytes<br>TxP $_lte2_tx_pkts<br>TxD $_lte2_tx_drop<br>Rate $_lte2_tx_bytes_rate<br></td>";

			$_lte3_rx_bytes = api_net_stats_get_disp($_lte3_rx_bytes); $_lte3_tx_bytes = api_net_stats_get_disp($_lte3_tx_bytes);
			$_lte3_rx_bytes_rate = api_net_rate_stats_get_disp($_lte3_rx_bytes_rate); $_lte3_tx_bytes_rate = api_net_rate_stats_get_disp($_lte3_tx_bytes_rate);
			print "<td>RxB $_lte3_rx_bytes<br>RxP $_lte3_rx_pkts<br>RxD $_lte3_rx_drop<br>Rate $_lte3_rx_bytes_rate<br><br>  
						TxB $_lte3_tx_bytes<br>TxP $_lte3_tx_pkts<br>TxD $_lte3_tx_drop<br>Rate $_lte3_tx_bytes_rate<br></td>";

						
			$_sdwan_rx_bytes = api_net_stats_get_disp($_sdwan_rx_bytes); $_sdwan_tx_bytes = api_net_stats_get_disp($_sdwan_tx_bytes);
			$_sdwan_rx_bytes_rate = api_net_rate_stats_get_disp($_sdwan_rx_bytes_rate); $_sdwan_tx_bytes_rate = api_net_rate_stats_get_disp($_sdwan_tx_bytes_rate);
			print "<td>RxB $_sdwan_rx_bytes<br>RxP $_sdwan_rx_pkts<br>RxD $_sdwan_rx_drop<br>Rate $_sdwan_rx_bytes_rate<br><br>  
						TxB $_sdwan_tx_bytes<br>TxP $_sdwan_tx_pkts<br>TxD $_sdwan_tx_drop<br>Rate $_sdwan_tx_bytes_rate<br></td>";

			print "<td>$timestamp $consolidated</td>";
			
			print "</tr>";
		}
	}
?>
</table>
<br><br>
