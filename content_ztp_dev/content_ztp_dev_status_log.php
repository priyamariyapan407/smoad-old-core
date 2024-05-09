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
api_ui_pagination_get_total_items_total_pages($db, 'smoad_device_status_log', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, $curr_page);
$limitstart = ($_page-1)*$G_items_per_page;
?>



<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>WAN</th><th>WAN2</th><th>WAN3</th><th>LTE1</th><th>LTE2</th><th>LTE3</th>
<th>Switch-Over</th><th>Ticket Generated</th><th>Timestamp</th></tr>

<?php

function _get_link_status_symbol($link_status)
{
	if($link_status=="up") { return "&#x2713;"; }
	else if($link_status=="down") { return "&#x2715;"; }
	return "&#x2715;";
}

function _get_ticket_generated_symbol($ticket_generated)
{
	if($ticket_generated=="yes") { return "&#x2713;"; }
	else if($ticket_generated=="no") { return "&#x2715;"; }
	return "&#x2715;";
}

function _signal_graph($signal_strength)
{	if($signal_strength=="excellent")
	{ print '<div style="display: inline-block;background-color:#00baad;width:26px;height:8px;border-radius:2px;"></div>'; }
	else if($signal_strength=="good")
	{ print '<div style="display: inline-block;background-color:#add45c;width:22px;height:8px;border-radius:2px;"></div>'; }
	else if($signal_strength=="fair")
	{ print '<div style="display: inline-block;background-color:#FF5733;width:18px;height:8px;border-radius:2px;"></div>'; }
	else if($signal_strength=="bad")
	{ print '<div style="display: inline-block;background-color:#C70039;width:14px;height:8px;border-radius:2px;"></div>'; }
	else { print "error"; }
}

$query = "select id, device_serialnumber, link_status_wan, link_status_wan2, link_status_wan3, link_status_lte1, link_status_lte2, link_status_lte3, 
				signal_strength_lte1, signal_strength_lte2, signal_strength_lte3, link_switch_over, ticket_generated, 
				wan1_duplex, wan1_speed, wan2_duplex, wan3_duplex, wan2_speed, wan3_speed, 
				wan1_bw_dist_pct, wan2_bw_dist_pct, wan3_bw_dist_pct, lte1_bw_dist_pct, lte2_bw_dist_pct, lte3_bw_dist_pct,  
				log_timestamp, id_rand_key, consolidated from smoad_device_status_log where device_serialnumber=\"$G_device_serialnumber\" 
				and year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1]
				order by id desc limit $limitstart".",$G_items_per_page"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$link_status_wan = $row['link_status_wan'];
			$link_status_wan2 = $row['link_status_wan2'];
			$link_status_wan3 = $row['link_status_wan3'];
			$link_status_lte1 = $row['link_status_lte1'];
			$link_status_lte2 = $row['link_status_lte2'];
			$link_status_lte3 = $row['link_status_lte3'];
			$signal_strength_lte1 = $row['signal_strength_lte1'];
			$signal_strength_lte2 = $row['signal_strength_lte2'];
			$signal_strength_lte3 = $row['signal_strength_lte3'];
			$link_switch_over = $row['link_switch_over'];
			$wan1_duplex = $row['wan1_duplex']; 
			$wan1_speed = $row['wan1_speed'];
			$wan2_duplex = $row['wan2_duplex'];
			$wan2_speed = $row['wan2_speed'];
			$wan3_duplex = $row['wan3_duplex'];
			$wan3_speed = $row['wan3_speed'];
			
			$wan1_bw_dist_pct = $row['wan1_bw_dist_pct'];
			$wan2_bw_dist_pct = $row['wan2_bw_dist_pct'];
			$wan3_bw_dist_pct = $row['wan3_bw_dist_pct'];
			$lte1_bw_dist_pct = $row['lte1_bw_dist_pct']; 
			$lte2_bw_dist_pct = $row['lte2_bw_dist_pct'];
			$lte3_bw_dist_pct = $row['lte3_bw_dist_pct'];
			
			$ticket_generated = $row['ticket_generated'];
			$timestamp = $row['log_timestamp'];
			$id_rand_key = $row['id_rand_key'];
			$consolidated = $row['consolidated'];
			if($consolidated==1) { $consolidated="<img src=\"i/archive.png\" alt=\"Consolidated log entry !\" title=\"Consolidated log entry !\" class=\"top_title_icons\" />"; } else { $consolidated=""; }

			print "<tr><td>$id</td>";
			$query2 = "select id, details, area from smoad_devices where serialnumber=\"$G_device_serialnumber\""; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$_id = $row2['id'];
					$_details = $row2['details'];
					$_area = $row2['area'];
					$title = "[$_id - $_details - $_area]";
				}
			}

			
			print "<td>Link: $link_status_wan<br>Duplex: $wan1_duplex<br>Speed: $wan1_speed<br>Bw %: $wan1_bw_dist_pct</td>";
			print "<td>Link: $link_status_wan2<br>Duplex: $wan2_duplex<br>Speed: $wan2_speed<br>Bw %: $wan2_bw_dist_pct</td>";
			print "<td>Link: $link_status_wan3<br>Duplex: $wan3_duplex<br>Speed: $wan3_speed<br>Bw %: $wan3_bw_dist_pct</td>";
			print "<td>Link: $link_status_lte1<br>Signal: ";
			_signal_graph($signal_strength_lte1);
			print "<br>Bw %: $lte1_bw_dist_pct";
			print "</td>";
			print "<td>Link: $link_status_lte2<br>Signal: ";
			_signal_graph($signal_strength_lte2);
			print "<br>Bw %: $lte2_bw_dist_pct";
			print "</td>";
			print "<td>Link: $link_status_lte3<br>Signal: ";
			_signal_graph($signal_strength_lte3);
			print "<br>Bw %: $lte3_bw_dist_pct";
			print "</td>";
			print "<td>$link_switch_over</td>";
			
			//$ticket_generated = _get_ticket_generated_symbol($ticket_generated);
			print "<td>$ticket_generated</td>";
			print "<td>$timestamp $consolidated</td>";
			
		
			print "</tr>";
		}
	}
?>
</table>


<!--<em>* page will auto refresh every 60 seconds !</em>
<meta HTTP-EQUIV="REFRESH" content="60; url=index.php?page=tunnels">-->




