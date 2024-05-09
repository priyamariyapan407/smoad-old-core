<?php   
	/* for freebsd
  $boottime = shell_exec('sysctl kern.boottime');
  $a = explode (" ", $boottime);
  $b = str_replace(',', '', $a[4]);
  
  $uptime = time() - $b;
  $days = floor($uptime/60/60/24);
  $hours = $uptime /60/60%24;
  $mins = $uptime /60%60;
  $secs = $uptime % 60;

  // Display time
  $uptime = "$days days $hours hrs $mins min $secs sec";
  */
  $uptime = shell_exec("uptime -p");

//comment this section for FreeBSD (else the page wont load)
	
		  $current_mem_utilization = shell_exec("free -t | awk 'FNR == 2 {printf(\"%.2f %\"), $3/$2*100}'");
		  $current_cpu_utilization = shell_exec("top -b -n 1 | grep Cpu | awk '{print $8}'|cut -f 1 -d '.'");
		  $current_cpu_utilization = chop($current_cpu_utilization);
		  $current_cpu_utilization = 100-$current_cpu_utilization;
		  $current_cpu_utilization .= " %";
	
$kernel_version = shell_exec("uname -r");

include('c/c_version.php');


//-----------------------------

$widget_blue = "#4908FA";
$widget_red = "#F2385A";
$widget_green = "#06D6A0";
$widget_black = "#444";

print "<br>";
$title = "Server Specs";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>Server name</th><td align=left>$G_server_name</td><th>Server uptime</th><td align=left>$uptime</td></tr>";
$contents  .= "<tr><th>Version</th><td align=left>$version</td><th>Release</th><td align=left>$release_date</td></tr>";
$contents  .= "<tr><th>CPU usage</th><td align=left>$current_cpu_utilization</td><th>Memory usage</th><td align=left>$current_mem_utilization</td></tr>";
$contents  .= "<tr><th>Kernel version</th><td align=left>$kernel_version</td><th></th><td align=left></td></tr>";
$contents .= "</table>";
widget(780, $title, $contents);


$query = "SELECT AVG(wan1_rx_bytes_rate) wan1_rx, AVG(wan2_rx_bytes_rate) wan2_rx,
AVG(lte1_rx_bytes_rate) lte1_rx, AVG(lte2_rx_bytes_rate) lte2_rx,
AVG(wan1_tx_bytes_rate) wan1_tx, AVG(wan2_tx_bytes_rate) wan2_tx,
AVG(lte1_tx_bytes_rate) wan1_tx, AVG(lte2_tx_bytes_rate) lte2_tx  
FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$wan1_rx = $row['wan1_rx']; $wan1_tx = $row['wan1_tx']; 
		$wan2_rx = $row['wan2_rx']; $wan2_tx = $row['wan2_tx'];
		$lte1_rx = $row['lte1_rx']; $lte1_tx = $row['lte1_tx']; 
		$lte2_rx = $row['lte2_rx']; $lte2_tx = $row['lte2_tx'];
		$avg_wan_traffic = $wan1_rx + $wan1_tx + $wan2_rx + $wan2_tx + $lte1_rx + $lte1_tx + $lte2_rx + $lte2_tx ;
		$avg_wan_traffic = round($avg_wan_traffic/8, 1);
	} 
}

$query = "SELECT count(wan1_rx_drop) wan1_rx, count(wan2_rx_drop) wan2_rx,
count(lte1_rx_drop) lte1_rx, count(lte2_rx_drop) lte2_rx,
count(wan1_tx_drop) wan1_tx, count(wan2_tx_drop) wan2_tx,
count(lte1_tx_drop) wan1_tx, count(lte2_tx_drop) lte2_tx  
FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$wan1_rx = $row['wan1_rx']; $wan1_tx = $row['wan1_tx']; 
		$wan2_rx = $row['wan2_rx']; $wan2_tx = $row['wan2_tx'];
		$lte1_rx = $row['lte1_rx']; $lte1_tx = $row['lte1_tx']; 
		$lte2_rx = $row['lte2_rx']; $lte2_tx = $row['lte2_tx'];
		$total_wan_drops = $wan1_rx + $wan1_tx + $wan2_rx + $wan2_tx + $lte1_rx + $lte1_tx + $lte2_rx + $lte2_tx ;
		$total_wan_drops = round($total_wan_drops, 0);
	} 
}

$query = "SELECT AVG(wan1_latency) wan1, AVG(wan2_latency) wan2, AVG(lte1_latency) lte1, AVG(lte2_latency) lte2
   FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$wan1 = $row['wan1']; $wan2 = $row['wan2']; 
		$lte1 = $row['lte1']; $lte2 = $row['lte2']; 
		$avg_wan_latency = $wan1 + $wan2 + $lte1 + $lte2 ;
		$port_count = 0;
		if($wan1>0) { $port_count++; }
		if($wan2>0) { $port_count++; }
		if($lte1>0) { $port_count++; }
		if($lte2>0) { $port_count++; }
		if($port_count==0) { $avg_wan_latency=0; }
		else { $avg_wan_latency = round($avg_wan_latency/$port_count, 0); }
	} 
}

$query = "SELECT AVG(wan1_jitter) wan1, AVG(wan2_jitter) wan2, AVG(lte1_jitter) lte1, AVG(lte2_jitter) lte2
   FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$wan1 = $row['wan1']; $wan2 = $row['wan2']; 
		$lte1 = $row['lte1']; $lte2 = $row['lte2']; 
		$avg_wan_jitter = $wan1 + $wan2 + $lte1 + $lte2 ;
		$port_count = 0;
		if($wan1>0) { $port_count++; }
		if($wan2>0) { $port_count++; }
		if($lte1>0) { $port_count++; }
		if($lte2>0) { $port_count++; }
		if($port_count==0) { $avg_wan_jitter=0; }
		else { $avg_wan_jitter = round($avg_wan_jitter/$port_count, 0); }
	} 
}		


$query = "SELECT AVG(sdwan_rx_bytes_rate) sdwan_rx, AVG(sdwan_tx_bytes_rate) sdwan_tx  
FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$sdwan_rx = $row['sdwan_rx']; $sdwan_tx = $row['sdwan_tx']; 
		$avg_sdwan_traffic = $sdwan_rx + $sdwan_tx ;
		$avg_sdwan_traffic = round($avg_sdwan_traffic/2, 1);
	} 
}	

$query = "SELECT count(sdwan_rx_drop) sdwan_rx, count(sdwan_tx_drop) sdwan_tx
FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$sdwan_rx = $row['sdwan_rx']; $sdwan_tx = $row['sdwan_tx']; 
		$total_sdwan_drops = $sdwan_rx + $sdwan_tx ;
		$total_sdwan_drops = round($total_sdwan_drops, 0);
	} 
}	

$query = "SELECT AVG(sdwan_latency) sdwan FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$avg_sdwan_latency = $row['sdwan']; 
		$avg_sdwan_latency = round($avg_sdwan_latency, 0);
	} 
}	

$query = "SELECT AVG(sdwan_jitter) sdwan FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) 
	{	$avg_sdwan_jitter = $row['sdwan']; 
		$avg_sdwan_jitter = round($avg_sdwan_jitter, 0);
	} 
}


if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
{	$title = "Network Stats";
	$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
	$contents  .= "<tr><th>EDGE</th><td></td><th>SD-WAN</th><td></td></tr>";
	$contents  .= "<tr><th>Avg WAN Traffic</th><td align=left>$avg_wan_traffic Kb/s</td><th>Avg Traffic</th><td align=left>$avg_sdwan_traffic Kb/s</td></tr>";
	$contents  .= "<tr><th>WAN Pkt Drops</th><td align=left>$total_wan_drops</td><th>Pkt Drops</th><td align=left>$total_sdwan_drops</td></tr>";
	$contents  .= "<tr><th>Avg WAN Latency</th><td align=left>$avg_wan_latency ms</td><th>Avg SD-WAN Latency</th><td align=left>$avg_sdwan_latency ms</td></tr>";
	$contents  .= "<tr><th>Avg WAN Jitter</th><td align=left>$avg_wan_jitter ms</td><th>Avg Jitter</th><td align=left>$avg_sdwan_latency ms</td></tr>";
	$contents .= "</table>";
	widget(780, $title, $contents);
}

if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
{	$sm_gw_count = sm_ztp_dev_get_gw_count($db, null); $title = "SMOAD Gateways: $sm_gw_count";
	$contents  = "<table><tr>";
	$contents .= "<td><iframe src=\"c/c_inc_ztp_dev_sds_status_pie_chart.php?type=ztp_sds&skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "<td><iframe src=\"c/c_inc_ztp_dev_sds_status_pie_chart.php?type=ztp_sds_type&skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "<td><iframe src=\"c/c_inc_home_dash_pie_chart_sds_sdwan_proto.php?skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	
	$contents .= "</tr></table>";
	widget(780, $title, $contents);
}

if($login_type=='root' || $login_type=='admin' || $login_type=='limited') //temporarily disable the same for customer login
{	$sm_edge_count = sm_ztp_dev_get_dev_count($db, null, $login_type); $title = "SMOAD Edge Devices: $sm_edge_count";
	$contents  = "<table><tr>";
	$contents .= "<td><iframe src=\"c/c_inc_ztp_dev_sds_status_pie_chart.php?type=ztp_dev&skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "<td><iframe src=\"c/c_inc_home_dash_pie_chart_dev_model.php?skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "<td><iframe src=\"c/c_inc_home_dash_pie_chart_dev_connectivity.php?skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	
	$contents .= "</tr></table>";
	widget(780, $title, $contents);
}

if($login_type=='root' || $login_type=='admin' || $login_type=='limited') //temporarily disable the same for customer login
{	$title = "SMOAD Edge Devices GW Status";
	$contents  = "<table><tr>";
	$contents .= "<td><iframe src=\"c/c_inc_home_dash_pie_chart_dev_sdwan_proto.php?skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "<td><iframe src=\"c/c_inc_home_dash_pie_chart_dev_gw_assigned.php?skey=$session_key\" style=\"width:245px;height:140px;border:0px;\" scrolling=\"no\"></iframe></td>";
	$contents .= "</tr></table>";
	widget(780, $title, $contents);
}

/*
$title = "EDGE Jitter Tracking (past 1 hour)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Jitter</th></tr>";
$query = "SELECT device_serialnumber, jitter FROM 
				( SELECT device_serialnumber, AVG(wan1_jitter) + AVG(wan2_jitter) + AVG(wan3_jitter) + AVG(lte1_jitter) + AVG(lte2_jitter) + AVG(lte3_jitter) jitter
   			FROM smoad_device_network_stats_log where log_timestamp > (NOW() - INTERVAL 60 MINUTE) 
				GROUP BY device_serialnumber ) AS jit ORDER BY jitter DESC LIMIT 5";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$device_serialnumber = $row['device_serialnumber'];	
			$jitter = $row['jitter']/6;
			$jitter = round($jitter,1); $jitter = number_format($jitter, 1);
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
			$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id'];
					$details = $row2['details'];
					$license = $row2['license'];
					$serialnumber = $row2['serialnumber'];
					$model = $row2['model'];
					$area = $row2['area'];
					if($model=="spider") { $model="Spider"; }
					else if($model=="beetle") { $model="Beetle"; }
					else if($model=="vm") { $model="VM"; }
					$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$jitter ms</td></tr>";
				}
			}
		}
	}
$contents .= "</table>";
widget(780, $title, $contents);*/

/*
$title = "EDGE Latency Tracking (past 1 hour)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Latency</th></tr>";
$query = "SELECT device_serialnumber, latency FROM 
				( SELECT device_serialnumber, AVG(wan1_latency) + AVG(wan2_latency) + AVG(wan3_latency) + AVG(lte1_latency) + AVG(lte2_latency) + AVG(lte3_latency) latency
   			FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE) 
				GROUP BY device_serialnumber ) AS lat ORDER BY latency DESC LIMIT 5";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$device_serialnumber = $row['device_serialnumber'];	
			$latency = $row['latency']/6;
			$latency = round($latency,1); $latency = number_format($latency, 1);
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
			$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id'];
					$details = $row2['details'];
					$license = $row2['license'];
					$serialnumber = $row2['serialnumber'];
					$model = $row2['model'];
					$area = $row2['area'];
					if($model=="spider") { $model="Spider"; }
					else if($model=="beetle") { $model="Beetle"; }
					else if($model=="vm") { $model="VM"; }
					$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$latency ms</td></tr>";
				}
			}
		}
	}
$contents .= "</table>";
widget(780, $title, $contents);
*/


if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
{	$title = "Circuit Summary";
	$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
	$contents  .= "<tr><th>Gateway</th><th>Total Circuits</th><th>Link Status Up</th><th>Link Status Down</th></tr>";
	$query = "SELECT serialnumber, ipaddr, details from smoad_sdwan_servers";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$serialnumber = $row['serialnumber'];
			$details = $row['details'];
			$ipaddr = $row['ipaddr'];
			$total_circuits = $link_status_up = $link_status_down = 0;
			$query2 = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$serialnumber\"";
			if($res2 = $db->query($query2)) { while($row2 = $res2->fetch_assoc()) {	$total_circuits = $row2['row_count']; } }
			$query2 = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$serialnumber\" and sdwan_link_status=\"UP\"";
			if($res2 = $db->query($query2)) { while($row2 = $res2->fetch_assoc()) {	$link_status_up = $row2['row_count']; } }
			$query2 = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$serialnumber\" and sdwan_link_status=\"DOWN\"";
			if($res2 = $db->query($query2)) { while($row2 = $res2->fetch_assoc()) {	$link_status_down = $row2['row_count']; } }
			
			$link_status_up = api_ui_up_down_display_status(1, $link_status_up);
			$link_status_down = api_ui_up_down_display_status(0, $link_status_down);
			$contents  .= "<tr><td title=\"IP Addr: $ipaddr\">$details</td><td>$total_circuits</td><td>$link_status_up</td><td>$link_status_down</td></tr>";
			
		}
	}
	$contents  .= "</table>";
	widget(780, $title, $contents);
}

if($login_type=='root' || $login_type=='admin' || $login_type=='limited')
{	$title = "Firmware Update Summary";
	$query = "SELECT update_firmware_release_version from smoad_update_firmware_server";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$update_firmware_release_version = $row['update_firmware_release_version'];
			$temp = explode(".", $update_firmware_release_version);
			$update_firmware_release_version = $temp[0].".".$temp[1].".".$temp[2];
		}
	}
	$sm_edge_count_update_firmware_complete = 0;
	$sm_edge_count_update_firmware_pending = $sm_edge_count;
	$query = "SELECT firmware from smoad_devices";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$firmware = $row['firmware'];
			$temp = explode(".", $firmware);
			$firmware = $temp[0].".".$temp[1].".".$temp[2];
			if($firmware == $update_firmware_release_version) { $sm_edge_count_update_firmware_complete++; }
		}
	}
	$sm_edge_count_update_firmware_pending-=$sm_edge_count_update_firmware_complete;
	$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
	$contents  .= "<tr><th>Updated</th><th>Pending</th></tr>";
	$contents  .= "<tr><td>$sm_edge_count_update_firmware_complete</td><td>$sm_edge_count_update_firmware_pending</td></tr>";
	$contents  .= "</table>";
	widget(780, $title, $contents);
}

$title = "Link Reliability (past 24 hours) - Up Count";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Up Count</th></tr>";
$query = "SELECT device_serialnumber, up_count FROM 
				( SELECT device_serialnumber, sum(link_status_wan_up_count+link_status_wan2_up_count+link_status_lte1_up_count+link_status_lte2_up_count+link_status_lte3_up_count) up_count
   			FROM smoad_device_status_log where  log_timestamp > (NOW() - INTERVAL 24 HOUR) 
				GROUP BY device_serialnumber ) AS upc 
				order by up_count DESC LIMIT 10";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$device_serialnumber = $row['device_serialnumber'];	
		$up_count = $row['up_count'];
		$up_count = number_format($up_count, 0);
		
		if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
		$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
		if($res2 = $db->query($query2))
		{	while($row2 = $res2->fetch_assoc())
			{	$id = $row2['id'];
				$details = $row2['details'];
				$license = $row2['license'];
				$serialnumber = $row2['serialnumber'];
				$model = $row2['model'];
				$area = $row2['area'];
				if($model=="spider") { $model="Spider"; }
				else if($model=="beetle") { $model="Beetle"; }
				else if($model=="vm") { $model="VM"; }
				$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$up_count</td></tr>";
			}
		}
	}
}
$contents .= "</table>";
widget(780, $title, $contents);

$title = "Link Reliability (past 24 hours) - Down Count";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Down Count</th></tr>";
$query = "SELECT device_serialnumber, down_count FROM 
				( SELECT device_serialnumber, sum(link_status_wan_down_count+link_status_wan2_down_count+link_status_lte1_down_count+link_status_lte2_down_count+link_status_lte3_down_count) down_count
   			FROM smoad_device_status_log where  log_timestamp > (NOW() - INTERVAL 24 HOUR) 
				GROUP BY device_serialnumber ) AS upc 
				order by down_count DESC LIMIT 5";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$device_serialnumber = $row['device_serialnumber'];	
		$down_count = $row['down_count'];
		$down_count = number_format($down_count, 0);
		
		if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
		$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
		if($res2 = $db->query($query2))
		{	while($row2 = $res2->fetch_assoc())
			{	$id = $row2['id'];
				$details = $row2['details'];
				$license = $row2['license'];
				$serialnumber = $row2['serialnumber'];
				$model = $row2['model'];
				$area = $row2['area'];
				if($model=="spider") { $model="Spider"; }
				else if($model=="beetle") { $model="Beetle"; }
				else if($model=="vm") { $model="VM"; }
				$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$down_count</td></tr>";
			}
		}
	}
}
$contents .= "</table>";
widget(780, $title, $contents);

$title = "EDGE SD-WAN Latency Tracking (past 1 hour)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Latency</th></tr>";
$query = "SELECT device_serialnumber, latency FROM 
				( SELECT device_serialnumber, AVG(sdwan_latency) latency
   			FROM smoad_device_network_stats_log where  log_timestamp > (NOW() - INTERVAL 60 MINUTE) 
				GROUP BY device_serialnumber ) AS lat ORDER BY latency DESC LIMIT 5";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$device_serialnumber = $row['device_serialnumber'];	
			$latency = $row['latency'];
			$latency = round($latency,1); $latency = number_format($latency, 1);
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
			$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id'];
					$details = $row2['details'];
					$license = $row2['license'];
					$serialnumber = $row2['serialnumber'];
					$model = $row2['model'];
					$area = $row2['area'];
					if($model=="spider") { $model="Spider"; }
					else if($model=="beetle") { $model="Beetle"; }
					else if($model=="vm") { $model="VM"; }
					$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$latency ms</td></tr>";
				}
			}
		}
	}
$contents .= "</table>";
widget(780, $title, $contents);

$title = "EDGE SD-WAN Jitter Tracking (past 1 hour)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Jitter</th></tr>";
$query = "SELECT device_serialnumber, jitter FROM 
				( SELECT device_serialnumber, AVG(sdwan_jitter) jitter
   			FROM smoad_device_network_stats_log where log_timestamp > (NOW() - INTERVAL 60 MINUTE) 
				GROUP BY device_serialnumber ) AS jit ORDER BY jitter DESC LIMIT 5";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$device_serialnumber = $row['device_serialnumber'];	
			$jitter = $row['jitter'];
			$jitter = round($jitter,1); $jitter = number_format($jitter, 1);
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
			$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$device_serialnumber\" $where_clause"; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$id = $row2['id'];
					$details = $row2['details'];
					$license = $row2['license'];
					$serialnumber = $row2['serialnumber'];
					$model = $row2['model'];
					$area = $row2['area'];
					if($model=="spider") { $model="Spider"; }
					else if($model=="beetle") { $model="Beetle"; }
					else if($model=="vm") { $model="VM"; }
					$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td><td align=right>$jitter ms</td></tr>";
				}
			}
		}
	}
$contents .= "</table>";
widget(780, $title, $contents);


//high sdwan link usage
$title = "SD-WAN Link High usage (past 5 minutes)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>ID</th><th>Details</th><th>Serial Number</th><th>Model</th><th>Area</th><th style=\"text-align:right;\">Rx rate</th>
					<th style=\"text-align:right;\">Tx rate</th><th style=\"text-align:right;\">Threshold (Kb/s)</th></tr>";

$query = "select device_serialnumber, sdwan_link_status FROM smoad_device_network_cfg
				where sdwan_link_high_usage='high' and sdwan_link_status='up'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$serialnumber = $row['device_serialnumber'];
		
		$query2 = "SELECT AVG(sdwan_rx_bytes_rate) avg_sdwan_rx_bytes_rate, AVG(sdwan_tx_bytes_rate) avg_sdwan_tx_bytes_rate,
			SUM(sdwan_rx_bytes) sum_sdwan_rx_bytes, SUM(sdwan_tx_bytes) sum_sdwan_tx_bytes FROM smoad_device_network_stats_log
			WHERE time(log_timestamp) > TIME(NOW() - INTERVAL 5 MINUTE) and device_serialnumber=\"$serialnumber\"";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$avg_sdwan_rx_bytes_rate = $row2['avg_sdwan_rx_bytes_rate'];
					$avg_sdwan_tx_bytes_rate = $row2['avg_sdwan_tx_bytes_rate'];	
					$sum_sdwan_rx_bytes = $row2['sum_sdwan_rx_bytes'];
					$sum_sdwan_tx_bytes = $row2['sum_sdwan_tx_bytes'];
				}
			}
			
			$avg_sdwan_rx_bytes_rate=$avg_sdwan_rx_bytes_rate/1000;
			$avg_sdwan_tx_bytes_rate=$avg_sdwan_tx_bytes_rate/1000;
			$avg_sdwan_rx_bytes_rate = number_format($avg_sdwan_rx_bytes_rate, 2);
			$avg_sdwan_tx_bytes_rate = number_format($avg_sdwan_tx_bytes_rate, 2);
	
		if($login_type=='root' || $login_type=='admin' || $login_type=='limited') { $where_clause=""; } else { $where_clause="and customer_id = $id_customer "; }
		$query2 = "select id, details, serialnumber, model, area from smoad_devices where serialnumber=\"$serialnumber\" $where_clause"; 
		if($res2 = $db->query($query2))
		{	while($row2 = $res2->fetch_assoc())
			{	$id = $row2['id'];
				$details = $row2['details'];
				$license = $row2['license'];
				$serialnumber = $row2['serialnumber'];
				$model = $row2['model'];
				$area = $row2['area'];
				if($model=="spider") { $model="Spider"; }
				else if($model=="beetle") { $model="Beetle"; }
				else if($model=="vm") { $model="VM"; }
				
				$sdwan_link_high_usage_threshold = number_format($sdwan_link_high_usage_threshold, 2);
				$contents  .= "<tr><td>$id</td><td>$details</td><td>$serialnumber</td><td>$model</td><td>$area</td>
						<td align=right>$avg_sdwan_rx_bytes_rate Mb/s</td><td align=right>$avg_sdwan_tx_bytes_rate Mb/s</td>
						<td align=right>$sdwan_link_high_usage_threshold</td></tr>";
			}
			
		}
	}
}
$contents .= "</table>";
widget(780, $title, $contents);


?>
<br>


