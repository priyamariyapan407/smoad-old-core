<?php


	function get_port_cable_status_icon($cable_status)
	{	if($cable_status=='up') { $icon = "network-cable2.png"; } else { $icon = "ethernet.png"; }
		return "<img src=\"i/$icon\" style=\"width:20px;height:20px;\" />";
	}
	
	function get_link_status_icon($link_status)
	{	
		if($link_status=='up') { $link_status="led-green2"; } else { $link_status="led-red2"; }
		return "<div class=\"$link_status\">&#11044;</div>";
	}
	
	function get_link_latency($latency) { return $latency." ms"; }

	function get_link_jitter($jitter) { return $jitter." ms"; }

	function get_last_24_port_up_count($db, $serialnumber, $port)
	{	$port_up_count=0;
		$query = "SELECT sum(".$port.") port_up_count FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and log_timestamp > (NOW() - INTERVAL 24 hour)";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $port_up_count = $row['port_up_count']; } }
		if($port_up_count>0) { return $port_up_count; }
		return $port_up_count;
	}
	
	function get_last_24_port_down_count($db, $serialnumber, $port)
	{	$port_down_count=0;
		$query = "SELECT sum(".$port.") port_down_count FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and log_timestamp > (NOW() - INTERVAL 24 hour)";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $port_down_count = $row['port_down_count']; } }
		if($port_down_count>0) { return $port_down_count; }
		return $port_down_count;
	}
	
	function get_last_port_up_count_timestamp($db, $serialnumber, $port)
	{	$port_up_count_timestamp='-';
		$query = "SELECT log_timestamp port_up_count_timestamp FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and $port = 1 order by id desc limit 1";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $port_up_count_timestamp = $row['port_up_count_timestamp']; } }
		return $port_up_count_timestamp;
	}
	
	function get_last_port_down_count_timestamp($db, $serialnumber, $port)
	{	$port_down_count_timestamp='-';
		$query = "SELECT log_timestamp port_down_count_timestamp FROM smoad_device_status_log 
				where device_serialnumber=\"$serialnumber\" and $port = 1 order by id desc limit 1";
		if($res = $db->query($query))
		{ while($row = $res->fetch_assoc()) { $port_down_count_timestamp = $row['port_down_count_timestamp']; } }
		return $port_down_count_timestamp;
	}
	
	function get_stats_button($curr_page, $port_nw_stats, $port_device_stats)
	{
		print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("port_nw_stats", $port_nw_stats); api_input_hidden("port_device_stats", $port_device_stats);
		print "<input type=\"image\" src=\"i/combo-chart2.png\" alt=\"submit\" width=\"20\" height=\"20\" title=\"Live Charts\">";
		print "</form>";
	}
	
	function get_qos_stats_button($curr_page)
	{
		print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("port_nw_qos_stats", "port_nw_qos_stats");
		print "<input type=\"image\" src=\"i/piechart.png\" alt=\"submit\" width=\"20\" height=\"20\" title=\"Historic Charts\">";
		print "</form>";
		print "<form method=\"POST\" action=\"$curr_page\" >";
		api_input_hidden("port_nw_qos_stats", "port_nw_qos_stats_live");
		print "<input type=\"image\" src=\"i/combo-chart2.png\" alt=\"submit\" width=\"20\" height=\"20\" title=\"Live Charts\">";
		print "</form>";
	}

?>


