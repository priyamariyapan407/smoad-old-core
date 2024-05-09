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
api_ui_pagination_get_total_items_total_pages($db, 'smoad_device_consolidated_stats_log', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, $curr_page);
$limitstart = ($_page-1)*$G_items_per_page;

?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th> BYTES </th><th> RATE </th><th>    LINK STATUS UP-COUNT</th><th> LATENCY </th><th> JITTER </th><th> TIMESTAMP </th></tr>

<?php
$query = "select id, device_serialnumber,
          sum_lan_rx_bytes, sum_lan_tx_bytes, sum_wan1_rx_bytes, sum_wan1_tx_bytes, sum_wan2_rx_bytes, sum_wan2_tx_bytes,
			 sum_lte1_rx_bytes, sum_lte1_tx_bytes, sum_lte2_rx_bytes, sum_lte2_tx_bytes, sum_lte3_rx_bytes, sum_lte3_tx_bytes,
			 sum_sdwan_rx_bytes, sum_sdwan_tx_bytes,
          avg_lan_rx_bytes_rate, avg_lan_tx_bytes_rate, 
			 avg_wan1_rx_bytes_rate, avg_wan1_tx_bytes_rate, avg_wan2_rx_bytes_rate, avg_wan2_tx_bytes_rate, 
          avg_lte1_rx_bytes_rate, avg_lte1_tx_bytes_rate, avg_lte2_rx_bytes_rate, avg_lte2_tx_bytes_rate, avg_lte3_rx_bytes_rate, avg_lte3_tx_bytes_rate, 
			 avg_sdwan_rx_bytes_rate, avg_sdwan_tx_bytes_rate, 
			 avg_wan1_latency, avg_wan1_jitter, avg_wan2_latency, avg_wan2_jitter, 
			 avg_lte1_latency, avg_lte1_jitter, avg_lte2_latency, avg_lte2_jitter, avg_lte3_latency, avg_lte3_jitter, 
			 avg_sdwan_latency, avg_sdwan_jitter,  
			 sum_link_status_wan_up_count, sum_link_status_wan2_up_count, 
			 sum_link_status_lte1_up_count, sum_link_status_lte2_up_count, sum_link_status_lte3_up_count, 
			 sum_link_status_sdwan_up_count, log_timestamp  
	       from smoad_device_consolidated_stats_log where device_serialnumber=\"$G_device_serialnumber\" 
	       and year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1]
	       order by log_timestamp desc limit $limitstart".",$G_items_per_page";
	        
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
		   $sum_lan_rx_bytes = api_net_stats_get_disp($row['sum_lan_rx_bytes']);   $sum_lan_tx_bytes = api_net_stats_get_disp($row['sum_lan_tx_bytes']);
			$sum_wan1_rx_bytes = api_net_stats_get_disp($row['sum_wan1_rx_bytes']); $sum_wan1_tx_bytes = api_net_stats_get_disp($row['sum_wan1_tx_bytes']);
			$sum_wan2_rx_bytes = api_net_stats_get_disp($row['sum_wan2_rx_bytes']); $sum_wan2_tx_bytes = api_net_stats_get_disp($row['sum_wan2_tx_bytes']);
			$sum_lte1_rx_bytes = api_net_stats_get_disp( $row['sum_lte1_rx_bytes']); $sum_lte1_tx_bytes = api_net_stats_get_disp($row['sum_lte1_tx_bytes']);
			$sum_lte2_rx_bytes = api_net_stats_get_disp($row['sum_lte2_rx_bytes']); $sum_lte2_tx_bytes = api_net_stats_get_disp($row['sum_lte2_tx_bytes']);
			$sum_lte3_rx_bytes = api_net_stats_get_disp($row['sum_lte3_rx_bytes']); $sum_lte3_tx_bytes = api_net_stats_get_disp( $row['sum_lte3_tx_bytes']);
			$sum_sdwan_rx_bytes = api_net_stats_get_disp($row['sum_sdwan_rx_bytes']); $sum_sdwan_tx_bytes = api_net_stats_get_disp($row['sum_sdwan_tx_bytes']);
			$avg_lan_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lan_rx_bytes_rate']); $avg_lan_tx_bytes_rate =api_net_rate_stats_get_disp( $row['avg_lan_tx_bytes_rate']);
			$avg_wan1_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_wan1_rx_bytes_rate']); $avg_wan1_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_wan1_tx_bytes_rate']);
			$avg_wan2_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_wan2_rx_bytes_rate']); $avg_wan2_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_wan2_tx_bytes_rate']);
			$avg_lte1_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lte1_rx_bytes_rate']); $avg_lte1_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lte1_tx_bytes_rate']);
			$avg_lte2_rx_bytes_rate =api_net_rate_stats_get_disp( $row['avg_lte2_rx_bytes_rate']); $avg_lte2_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lte2_tx_bytes_rate']);
			$avg_lte3_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lte3_rx_bytes_rate']); $avg_lte3_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_lte3_tx_bytes_rate']);
			$avg_sdwan_rx_bytes_rate = api_net_rate_stats_get_disp($row['avg_sdwan_rx_bytes_rate']); $avg_sdwan_tx_bytes_rate = api_net_rate_stats_get_disp($row['avg_sdwan_tx_bytes_rate']);
			$avg_wan1_latency = $row['avg_wan1_latency']; $avg_wan1_jitter = $row['avg_wan1_jitter'];
			$avg_wan2_latency = $row['avg_wan2_latency']; $avg_wan2_jitter = $row['avg_wan2_jitter'];
			$avg_lte1_latency = $row['avg_lte1_latency']; $avg_lte1_jitter = $row['avg_lte1_jitter'];
			$avg_lte2_latency = $row['avg_lte2_latency']; $avg_lte2_jitter = $row['avg_lte2_jitter'];
			$avg_lte3_latency = $row['avg_lte3_latency']; $avg_lte3_jitter = $row['avg_lte3_jitter'];
			$avg_sdwan_latency = $row['avg_sdwan_latency']; $avg_sdwan_jitter = $row['avg_sdwan_jitter'];
			$sum_link_status_wan_up_count = $row['sum_link_status_wan_up_count'];
			$sum_link_status_wan2_up_count = $row['sum_link_status_wan2_up_count'];
			$sum_link_status_lte1_up_count = $row['sum_link_status_lte1_up_count'];
			$sum_link_status_lte2_up_count = $row['sum_link_status_lte2_up_count'];
			$sum_link_status_lte3_up_count = $row['sum_link_status_lte3_up_count'];
			$sum_link_status_sdwan_up_count = $row['sum_link_status_sdwan_up_count'];
			$timestamp = $row['log_timestamp'];
			
	
			
			print "<tr><td>$id</td>";
		
			
			print "<td><strong>WAN1:</strong><br>Rx $sum_wan1_rx_bytes<br>Tx $sum_wan1_tx_bytes<br>
					 <strong>WAN2:</strong><br>Rx $sum_wan2_rx_bytes<br>Tx $sum_wan2_tx_bytes<br>
			       <strong>LTE1:</strong><br>Rx $sum_lte1_rx_bytes<br>Tx $sum_lte1_tx_bytes<br>
			       <strong>LTE2:</strong><br>Rx $sum_lte2_rx_bytes<br>Tx $sum_lte1_tx_bytes<br>
			       <strong>LTE3:</strong><br>Rx $sum_lte3_rx_bytes<br>Tx $sum_lte3_tx_bytes<br>
			       <strong>SD-WAN:</strong><br>Rx $sum_lte3_rx_bytes<br>Tx $sum_sdwan_tx_bytes<br></td>";
			
			print "<td><strong>WAN1:</strong><br>Rx $avg_wan1_rx_bytes_rate<br>Tx $avg_wan1_tx_bytes_rate<br>
			       <strong>WAN2:</strong><br>Rx $avg_wan2_rx_bytes_rate<br>Tx $avg_wan2_tx_bytes_rate<br>
			       <strong>LTE1:</strong><br>Rx $avg_lte1_rx_bytes_rate<br>Tx $avg_lte1_tx_bytes_rate<br>
		       	 <strong>LTE2:</strong><br>Rx $avg_lte2_rx_bytes_rate<br>Tx $avg_lte2_tx_bytes_rate<br>
			       <strong>LTE3:</strong><br>Rx $avg_lte3_rx_bytes_rate<br>Tx $avg_lte3_tx_bytes_rate<br>
			       <strong>SD-WAN:</strong><br>Rx $avg_sdwan_rx_bytes_rate<br>Tx $avg_sdwan_tx_bytes_rate<br></td>";
			
			print "<td><strong>WAN1:</strong> $sum_link_status_wan_up_count<br><br><br><strong>WAN2:</strong> $sum_link_status_wan2_up_count<br><br><br>
			       <strong>LTE1:</strong> $sum_link_status_lte1_up_count<br><br><br><strong>LTE2:</strong> $sum_link_status_lte2_up_count<br><br><br>
			       <strong>LTE3:</strong> $sum_link_status_lte3_up_count<br><br><br>
			       <strong>SD-WAN:</strong> $sum_link_status_sdwan_up_count<br><br><br></td>";
			
		   print "<td><strong>WAN1:</strong> $avg_wan1_latency ms<br><br><br><strong>WAN2:</strong> $avg_wan2_latency ms<br><br><br>
			       <strong>LTE1:</strong> $avg_lte1_latency ms<br><br><br><strong>LTE2:</strong> $avg_lte2_latency ms<br><br><br>
			       <strong>LTE3:</strong> $avg_lte3_latency ms<br><br><br>
			       <strong>SD-WAN:</strong> $avg_sdwan_latency ms<br><br><br></td>";
			
	   	print "<td><strong>WAN1:</strong> $avg_wan1_jitter ms<br><br><br><strong>WAN2:</strong> $avg_wan2_jitter ms<br><br><br>
			       <strong>LTE1:</strong> $avg_lte1_jitter ms<br><br><br><strong>LTE2:</strong> $avg_lte2_jitter ms<br><br><br>
			       <strong>LTE3:</strong> $avg_lte3_jitter ms<br><br><br>
			       <strong>SD-WAN:</strong> $avg_sdwan_jitter ms<br><br><br></td>";
			


			print "<td>$timestamp</td>";
			
			print "</tr>";
		}
	}
?>
</table>

