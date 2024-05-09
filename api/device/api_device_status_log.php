<?php 

//external API for: Device (CPE) <> SD-WAN Server

error_reporting(5); 
include('../../c/c_db_access.php');
include('c_include.php');

$_md5_serialnumber = $_POST['device']; //this is an md5 of device serialnumber
$_data = $_POST['data'];


$proceed_further=false;

$proceed_further = _api_do_initial_due_diligence($db, $_md5_serialnumber, $_data, $_serialnumber);

//_debug_api_test_to_db($db, "$_md5_serialnumber - $_data");

$_boot_up_count = "0";
$_link_status_lan = "down";
$_link_status_wan = "down";
$_link_status_wan2 = "down";
$_link_status_wan3 = "down";
$_link_status_lte1 = "down";
$_link_status_lte2 = "down";
$_link_status_lte3 = "down";
$_link_status_sdwan = "down";

$_link_status_wan_up_count = 0;
$_link_status_wan2_up_count = 0;
$_link_status_wan3_up_count = 0;
$_link_status_lte1_up_count = 0;
$_link_status_lte2_up_count = 0;
$_link_status_lte3_up_count = 0;
$_link_status_sdwan_up_count = 0;

$_link_status_wan_down_count = 0;
$_link_status_wan2_down_count = 0;
$_link_status_wan3_down_count = 0;
$_link_status_lte1_down_count = 0;
$_link_status_lte2_down_count = 0;
$_link_status_lte3_down_count = 0;
$_link_status_sdwan_down_count = 0;

$_lan_rx_bytes=0;
$_lan_rx_bytes_rate=0;
$_lan_rx_pkts=0;
$_lan_rx_drop=0;
$_lan_tx_bytes=0;
$_lan_tx_bytes_rate=0;
$_lan_tx_pkts=0;
$_lan_tx_drop=0;

$_wan1_rx_bytes=0;
$_wan1_rx_bytes_rate=0;
$_wan1_rx_pkts=0;
$_wan1_rx_drop=0;
$_wan1_tx_bytes=0;
$_wan1_tx_bytes_rate=0;
$_wan1_tx_pkts=0;
$_wan1_tx_drop=0;

$_wan2_rx_bytes=0;
$_wan2_rx_bytes_rate=0;
$_wan2_rx_pkts=0;
$_wan2_rx_drop=0;
$_wan2_tx_bytes=0;
$_wan2_tx_bytes_rate=0;
$_wan2_tx_pkts=0;
$_wan2_tx_drop=0;

$_lte1_rx_bytes=0;
$_lte1_rx_bytes_rate=0;
$_lte1_rx_pkts=0;
$_lte1_rx_drop=0;
$_lte1_tx_bytes=0;
$_lte1_tx_bytes_rate=0;
$_lte1_tx_pkts=0;
$_lte1_tx_drop=0;

$_lte2_rx_bytes=0;
$_lte2_rx_bytes_rate=0;
$_lte2_rx_pkts=0;
$_lte2_rx_drop=0;
$_lte2_tx_bytes=0;
$_lte2_tx_bytes_rate=0;
$_lte2_tx_pkts=0;
$_lte2_tx_drop=0;

$_lte3_rx_bytes=0;
$_lte3_rx_bytes_rate=0;
$_lte3_rx_pkts=0;
$_lte3_rx_drop=0;
$_lte3_tx_bytes=0;
$_lte3_tx_bytes_rate=0;
$_lte3_tx_pkts=0;
$_lte3_tx_drop=0;

$_sdwan_rx_bytes=0;
$_sdwan_rx_bytes_rate=0;
$_sdwan_rx_pkts=0;
$_sdwan_rx_drop=0;
$_sdwan_tx_bytes=0;
$_sdwan_tx_bytes_rate=0;
$_sdwan_tx_pkts=0;
$_sdwan_tx_drop=0;

$_wan1_duplex=""; $_wan1_speed=0; 
$_wan2_duplex=""; $_wan2_speed=0;
$_wan3_duplex=""; $_wan3_speed=0;

$_wan1_bw_dist_pct=0;
$_wan2_bw_dist_pct=0;
$_wan3_bw_dist_pct=0;
$_lte1_bw_dist_pct=0;
$_lte2_bw_dist_pct=0; 

$_wan1_latency = 0; $_wan2_latency = 0; $_wan3_latency = 0; $_lte1_latency = 0; $_lte2_latency = 0; $_lte3_latency = 0; $_sdwan_latency = 0;
$_wan1_jitter = 0; $_wan2_jitter = 0; $_wan3_jitter = 0; $_lte1_jitter = 0; $_lte2_jitter = 0; $_lte3_jitter = 0; $_sdwan_jitter = 0;

if($proceed_further==true)
{	$_data = chop($_data);
	
	//check if the decrypted data contains some signatures. If so, then parse !
	if(strpos($_data, 'serialnumber') !== false && strpos($_data, 'link_status_wan') !== false)
	{	$lines = explode("&", $_data);
		foreach($lines as $line) 
		{  $line = chop($line);
			$valuepair = explode("=", $line);
			if($proceed_further==true)
			{	switch($valuepair[0]) 
				{	case 'serialnumber'		: { $_serialnumber_parsed=$valuepair[1];
														 
														 //bogus serial number ??
														 if($_serialnumber_parsed!=$_serialnumber) { $proceed_further=false; }
														 break;
													  }
					case 'id_rand_key'	: { $_id_rand_key=$valuepair[1]; break; }
					
					case 'boot_up_count'	: { $_boot_up_count=$valuepair[1]; break; }
					
					case 'link_status_lan'	: { $_link_status_lan=$valuepair[1]; break; }
					case 'link_status_wan'	: { $_link_status_wan=$valuepair[1]; break; }
					case 'link_status_wan2'	: { $_link_status_wan2=$valuepair[1]; break; }
					case 'link_status_lte1'	: { $_link_status_lte1=$valuepair[1]; break; }
					case 'link_status_lte2'	: { $_link_status_lte2=$valuepair[1]; break; }
					case 'link_status_lte3'	: { $_link_status_lte3=$valuepair[1]; break; }
					case 'link_status_sdwan': { $_link_status_sdwan=$valuepair[1]; break; }
					
					case 'signal_strength_lte1': { $_signalstrength_lte1=$valuepair[1]; break; }
					case 'signal_strength_lte2': { $_signalstrength_lte2=$valuepair[1]; break; }
					case 'link_switch_over'		: { $_link_switch_over=$valuepair[1]; break; }
					
					case 'ticket_subject'	: { $_ticket_subject=$valuepair[1]; break; }
					case 'ticket_body'		: { $_ticket_body=$valuepair[1]; break; }
					case 'ticket_prio'		: { $_ticket_prio=$valuepair[1]; break; }
					
					case 'lan_rx_bytes'		: { $_lan_rx_bytes=$valuepair[1]; break; }
					case 'lan_rx_pkts'		: { $_lan_rx_pkts=$valuepair[1]; break; }
					case 'lan_rx_drop'		: { $_lan_rx_drop=$valuepair[1]; break; }
					case 'lan_tx_bytes'		: { $_lan_tx_bytes=$valuepair[1]; break; }
					case 'lan_tx_pkts'		: { $_lan_tx_pkts=$valuepair[1]; break; }
					case 'lan_tx_drop'		: { $_lan_tx_drop=$valuepair[1]; break; }
					
					case 'wan1_rx_bytes'		: { $_wan1_rx_bytes=$valuepair[1]; break; }
					case 'wan1_rx_pkts'		: { $_wan1_rx_pkts=$valuepair[1]; break; }
					case 'wan1_rx_drop'		: { $_wan1_rx_drop=$valuepair[1]; break; }
					case 'wan1_tx_bytes'		: { $_wan1_tx_bytes=$valuepair[1]; break; }
					case 'wan1_tx_pkts'		: { $_wan1_tx_pkts=$valuepair[1]; break; }
					case 'wan1_tx_drop'		: { $_wan1_tx_drop=$valuepair[1]; break; }
					
					case 'wan2_rx_bytes'		: { $_wan2_rx_bytes=$valuepair[1]; break; }
					case 'wan2_rx_pkts'		: { $_wan2_rx_pkts=$valuepair[1]; break; }
					case 'wan2_rx_drop'		: { $_wan2_rx_drop=$valuepair[1]; break; }
					case 'wan2_tx_bytes'		: { $_wan2_tx_bytes=$valuepair[1]; break; }
					case 'wan2_tx_pkts'		: { $_wan2_tx_pkts=$valuepair[1]; break; }
					case 'wan2_tx_drop'		: { $_wan2_tx_drop=$valuepair[1]; break; }
					
					case 'wan3_rx_bytes'		: { $_wan3_rx_bytes=$valuepair[1]; break; }
					case 'wan3_rx_pkts'		: { $_wan3_rx_pkts=$valuepair[1]; break; }
					case 'wan3_rx_drop'		: { $_wan3_rx_drop=$valuepair[1]; break; }
					case 'wan3_tx_bytes'		: { $_wan3_tx_bytes=$valuepair[1]; break; }
					case 'wan3_tx_pkts'		: { $_wan3_tx_pkts=$valuepair[1]; break; }
					case 'wan3_tx_drop'		: { $_wan3_tx_drop=$valuepair[1]; break; }
					
					case 'lte1_rx_bytes'		: { $_lte1_rx_bytes=$valuepair[1]; break; }
					case 'lte1_rx_pkts'		: { $_lte1_rx_pkts=$valuepair[1]; break; }
					case 'lte1_rx_drop'		: { $_lte1_rx_drop=$valuepair[1]; break; }
					case 'lte1_tx_bytes'		: { $_lte1_tx_bytes=$valuepair[1]; break; }
					case 'lte1_tx_pkts'		: { $_lte1_tx_pkts=$valuepair[1]; break; }
					case 'lte1_tx_drop'		: { $_lte1_tx_drop=$valuepair[1]; break; }
					
					case 'lte2_rx_bytes'		: { $_lte2_rx_bytes=$valuepair[1]; break; }
					case 'lte2_rx_pkts'		: { $_lte2_rx_pkts=$valuepair[1]; break; }
					case 'lte2_rx_drop'		: { $_lte2_rx_drop=$valuepair[1]; break; }
					case 'lte2_tx_bytes'		: { $_lte2_tx_bytes=$valuepair[1]; break; }
					case 'lte2_tx_pkts'		: { $_lte2_tx_pkts=$valuepair[1]; break; }
					case 'lte2_tx_drop'		: { $_lte2_tx_drop=$valuepair[1]; break; }
					
					case 'lte3_rx_bytes'		: { $_lte3_rx_bytes=$valuepair[1]; break; }
					case 'lte3_rx_pkts'		: { $_lte3_rx_pkts=$valuepair[1]; break; }
					case 'lte3_rx_drop'		: { $_lte3_rx_drop=$valuepair[1]; break; }
					case 'lte3_tx_bytes'		: { $_lte3_tx_bytes=$valuepair[1]; break; }
					case 'lte3_tx_pkts'		: { $_lte3_tx_pkts=$valuepair[1]; break; }
					case 'lte3_tx_drop'		: { $_lte3_tx_drop=$valuepair[1]; break; }
					
					case 'sdwan_rx_bytes'	: { $_sdwan_rx_bytes=$valuepair[1]; break; }
					case 'sdwan_rx_pkts'		: { $_sdwan_rx_pkts=$valuepair[1]; break; }
					case 'sdwan_rx_drop'		: { $_sdwan_rx_drop=$valuepair[1]; break; }
					case 'sdwan_tx_bytes'	: { $_sdwan_tx_bytes=$valuepair[1]; break; }
					case 'sdwan_tx_pkts'		: { $_sdwan_tx_pkts=$valuepair[1]; break; }
					case 'sdwan_tx_drop'		: { $_sdwan_tx_drop=$valuepair[1]; break; }
					
					case 'wan1_duplex'		: { $_wan1_duplex=$valuepair[1]; break; }
					case 'wan1_speed'			: { $_wan1_speed=$valuepair[1]; break; }
					case 'wan2_duplex'		: { $_wan2_duplex=$valuepair[1]; break; }
					case 'wan2_speed'			: { $_wan2_speed=$valuepair[1]; break; }
					case 'wan3_duplex'		: { $_wan3_duplex=$valuepair[1]; break; }
					case 'wan3_speed'			: { $_wan3_speed=$valuepair[1]; break; }
					
					case 'wan1_bw_dist_pct'	: { $_wan1_bw_dist_pct=$valuepair[1]; break; }
					case 'wan2_bw_dist_pct'	: { $_wan2_bw_dist_pct=$valuepair[1]; break; }
					case 'wan3_bw_dist_pct'	: { $_wan3_bw_dist_pct=$valuepair[1]; break; }
					case 'lte1_bw_dist_pct'	: { $_lte1_bw_dist_pct=$valuepair[1]; break; }
					case 'lte2_bw_dist_pct'	: { $_lte2_bw_dist_pct=$valuepair[1]; break; }
					case 'lte3_bw_dist_pct'	: { $_lte3_bw_dist_pct=$valuepair[1]; break; }
					
					case 'wan1_latency'		: { $_wan1_latency=$valuepair[1]; break; }
					case 'wan2_latency'		: { $_wan2_latency=$valuepair[1]; break; }
					case 'wan3_latency'		: { $_wan3_latency=$valuepair[1]; break; }
					case 'lte1_latency'		: { $_lte1_latency=$valuepair[1]; break; }
					case 'lte2_latency'		: { $_lte2_latency=$valuepair[1]; break; }
					case 'lte3_latency'		: { $_lte3_latency=$valuepair[1]; break; }
					case 'sdwan_latency'		: { $_sdwan_latency=$valuepair[1]; break; }
					
					case 'wan1_jitter'		: { $_wan1_jitter=$valuepair[1]; break; }
					case 'wan2_jitter'		: { $_wan2_jitter=$valuepair[1]; break; }
					case 'wan3_jitter'		: { $_wan3_jitter=$valuepair[1]; break; }
					case 'lte1_jitter'		: { $_lte1_jitter=$valuepair[1]; break; }
					case 'lte2_jitter'		: { $_lte2_jitter=$valuepair[1]; break; }
					case 'lte3_jitter'		: { $_lte3_jitter=$valuepair[1]; break; }
					case 'sdwan_jitter'		: { $_sdwan_jitter=$valuepair[1]; break; }
				}
			}
		}
	}
}

if($proceed_further==true) { $proceed_further = _api_validate_id_rand_key_salt($db, "smoad_device_status_log", $_id_rand_key); }

if($proceed_further==true) //check if the serial number exists/valid ?
{	$row_count = 0;
	$query2 = "SELECT count(*) row_count FROM smoad_devices WHERE serialnumber=\"$_serialnumber\"";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$row_count = $row2['row_count'];
		}
	}
	if($row_count==0) { $proceed_further=false; }
}


if($proceed_further==true)
{	if($_ticket_subject!='notset' && $_ticket_body!='notset' && $_ticket_prio>0) { $ticket_generated="yes"; } else { $ticket_generated="no"; }
		
	
	//////////////////////////////////////////////
	$_up_down_processed=false;
	$query2 = "select link_status_wan, link_status_wan2, link_status_wan3,  link_status_lte1, link_status_lte2, link_status_lte3, link_status_sdwan 
 		from smoad_device_status_log where device_serialnumber=\"$_serialnumber\" order by id desc limit 1";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$_up_down_processed = true;
			$_link_status_wan_prev = $row2['link_status_wan'];
			$_link_status_wan2_prev = $row2['link_status_wan2'];
			$_link_status_wan3_prev = $row2['link_status_wan3'];
			$_link_status_lte1_prev = $row2['link_status_lte1'];
			$_link_status_lte2_prev = $row2['link_status_lte2'];
			$_link_status_lte3_prev = $row2['link_status_lte3'];
			$_link_status_sdwan_prev = $row2['link_status_sdwan'];
			
			if($_link_status_wan_prev=='down' && $_link_status_wan=='up') { $_link_status_wan_up_count=1; }
			if($_link_status_wan2_prev=='down' && $_link_status_wan2=='up') { $_link_status_wan2_up_count=1; }
			if($_link_status_wan3_prev=='down' && $_link_status_wan3=='up') { $_link_status_wan3_up_count=1; }
			if($_link_status_lte1_prev=='down' && $_link_status_lte1=='up') { $_link_status_lte1_up_count=1; }
			if($_link_status_lte2_prev=='down' && $_link_status_lte2=='up') { $_link_status_lte2_up_count=1; }
			if($_link_status_lte3_prev=='down' && $_link_status_lte3=='up') { $_link_status_lte3_up_count=1; }
			if($_link_status_sdwan_prev=='down' && $_link_status_sdwan=='up') { $_link_status_sdwan_up_count=1; }
			
			if($_link_status_wan_prev=='up' && $_link_status_wan=='down') { $_link_status_wan_down_count=1; }
			if($_link_status_wan2_prev=='up' && $_link_status_wan2=='down') { $_link_status_wan2_down_count=1; }
			if($_link_status_wan3_prev=='up' && $_link_status_wan3=='down') { $_link_status_wan3_down_count=1; }
			if($_link_status_lte1_prev=='up' && $_link_status_lte1=='down') { $_link_status_lte1_down_count=1; }
			if($_link_status_lte2_prev=='up' && $_link_status_lte2=='down') { $_link_status_lte2_down_count=1; }
			if($_link_status_lte3_prev=='up' && $_link_status_lte3=='down') { $_link_status_lte3_down_count=1; }
			if($_link_status_sdwan_prev=='up' && $_link_status_sdwan=='down') { $_link_status_sdwan_down_count=1; }
		}
	}
	if($_up_down_processed==false) //there are no previous rows to perform _prev vs now (as above)
	{
		if($_link_status_wan=='up') { $_link_status_wan_up_count=1; }
		if($_link_status_wan2=='up') { $_link_status_wan2_up_count=1; }
		if($_link_status_wan3=='up') { $_link_status_wan3_up_count=1; }
		if($_link_status_lte1=='up') { $_link_status_lte1_up_count=1; }
		if($_link_status_lte2=='up') { $_link_status_lte2_up_count=1; }
		if($_link_status_lte3=='up') { $_link_status_lte3_up_count=1; }
		if($_link_status_sdwan=='up') { $_link_status_sdwan_up_count=1; }
			
		if($_link_status_wan=='down') { $_link_status_wan_down_count=1; }
		if($_link_status_wan2=='down') { $_link_status_wan2_down_count=1; }
		if($_link_status_wan3=='down') { $_link_status_wan3_down_count=1; }
		if($_link_status_lte1=='down') { $_link_status_lte1_down_count=1; }
		if($_link_status_lte2=='down') { $_link_status_lte2_down_count=1; }
		if($_link_status_lte3=='down') { $_link_status_lte3_down_count=1; }
		if($_link_status_sdwan=='down') { $_link_status_sdwan_down_count=1; }
	}
	
	
	//log this event
	$query2="insert into smoad_device_status_log 
		(device_serialnumber, boot_up_count, 
		 link_status_lan, link_status_wan, link_status_wan2, link_status_lte1, link_status_lte2, link_status_lte3, link_status_sdwan, 
		 link_status_wan_up_count, link_status_wan2_up_count, link_status_wan3_up_count, link_status_lte1_up_count, link_status_lte2_up_count, link_status_lte3_up_count, link_status_sdwan_up_count,
		 link_status_wan_down_count, link_status_wan2_down_count, link_status_wan3_down_count, link_status_lte1_down_count, link_status_lte2_down_count, link_status_lte3_down_count, link_status_sdwan_down_count,  
		 signal_strength_lte1, signal_strength_lte2, signal_strength_lte3, 
		 link_switch_over, ticket_generated, wan1_duplex, wan1_speed, wan2_duplex, wan2_speed, wan3_duplex, wan3_speed, 
		 wan1_bw_dist_pct, wan2_bw_dist_pct, wan3_bw_dist_pct, lte1_bw_dist_pct, lte2_bw_dist_pct, lte3_bw_dist_pct, 
		 id_rand_key) 
		values ('$_serialnumber', '$_boot_up_count',
		'$_link_status_lan', '$_link_status_wan', '$_link_status_wan2', '$_link_status_lte1', '$_link_status_lte2', '$_link_status_lte3', '$_link_status_sdwan', 
		'$_link_status_wan_up_count', '$_link_status_wan2_up_count', '$_link_status_wan3_up_count', '$_link_status_lte1_up_count', '$_link_status_lte2_up_count', '$_link_status_lte3_up_count', '$_link_status_sdwan_up_count',
		'$_link_status_wan_down_count', '$_link_status_wan2_down_count', '$_link_status_wan3_down_count', '$_link_status_lte1_down_count', '$_link_status_lte2_down_count', '$_link_status_lte3_down_count', '$_link_status_sdwan_down_count',
		'$_signalstrength_lte1', '$_signalstrength_lte2', '$_signalstrength_lte3', '$_link_switch_over', '$ticket_generated',
		'$_wan1_duplex', '$_wan1_speed', '$_wan2_duplex', '$_wan2_speed', '$_wan3_duplex', '$_wan3_speed',
		'$_wan1_bw_dist_pct', '$_wan2_bw_dist_pct', '$_wan3_bw_dist_pct', '$_lte1_bw_dist_pct', '$_lte2_bw_dist_pct', '$_lte3_bw_dist_pct',  
		'$_id_rand_key')";
	$db->query($query2);
	
	//trigger alert - device boot and set device status up
	// - this is taken care now in the: /usr/local/smoad/script/reset_status
	
	//_debug_api_test_to_db($db, $query2);
	
	//calculate the rate
	// get the last entry, calculate the diff (if not valid i.e after a long shutdown and boot then populate 0)
	$query2 = "select TIME_TO_SEC(TIMEDIFF(NOW(),log_timestamp)) AS log_timestamp_diff 
		from smoad_device_network_stats_log where device_serialnumber=\"$_serialnumber\" order by id desc limit 1";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$_log_timestamp_diff = $row2['log_timestamp_diff'];
		}
	}

	//avoid div by zero hence, it is the first log of this device, hence assume its once in every 2 mins (120 secs)
	if(!($_log_timestamp_diff>0)) { $_log_timestamp_diff=120; }
	
	$_lan_rx_bytes_rate=$_lan_rx_bytes/$_log_timestamp_diff;
	$_lan_tx_bytes_rate=$_lan_tx_bytes/$_log_timestamp_diff;
	$_wan1_rx_bytes_rate=$_wan1_rx_bytes/$_log_timestamp_diff;
	$_wan1_tx_bytes_rate=$_wan1_tx_bytes/$_log_timestamp_diff;
	$_wan2_rx_bytes_rate=$_wan2_rx_bytes/$_log_timestamp_diff;
	$_wan2_tx_bytes_rate=$_wan2_tx_bytes/$_log_timestamp_diff;
	$_wan3_rx_bytes_rate=$_wan3_rx_bytes/$_log_timestamp_diff;
	$_wan3_tx_bytes_rate=$_wan3_tx_bytes/$_log_timestamp_diff;
	$_lte1_rx_bytes_rate=$_lte1_rx_bytes/$_log_timestamp_diff;
	$_lte1_tx_bytes_rate=$_lte1_tx_bytes/$_log_timestamp_diff;
	$_lte2_rx_bytes_rate=$_lte2_rx_bytes/$_log_timestamp_diff;
	$_lte2_tx_bytes_rate=$_lte2_tx_bytes/$_log_timestamp_diff;
	$_lte3_rx_bytes_rate=$_lte3_rx_bytes/$_log_timestamp_diff;
	$_lte3_tx_bytes_rate=$_lte3_tx_bytes/$_log_timestamp_diff;
	$_sdwan_rx_bytes_rate=$_sdwan_rx_bytes/$_log_timestamp_diff;
	$_sdwan_tx_bytes_rate=$_sdwan_tx_bytes/$_log_timestamp_diff;
	
	//log network stats
	$query2="insert into smoad_device_network_stats_log 
	(device_serialnumber, 
	 lan_rx_bytes,  lan_rx_bytes_rate,  lan_rx_pkts,  lan_rx_drop,  lan_tx_bytes,  lan_tx_bytes_rate,  lan_tx_pkts,  lan_tx_drop, 
	 wan1_rx_bytes, wan1_rx_bytes_rate, wan1_rx_pkts, wan1_rx_drop, wan1_tx_bytes, wan1_tx_bytes_rate, wan1_tx_pkts, wan1_tx_drop,
	 wan2_rx_bytes, wan2_rx_bytes_rate, wan2_rx_pkts, wan2_rx_drop, wan2_tx_bytes, wan2_tx_bytes_rate, wan2_tx_pkts, wan2_tx_drop,
	 wan3_rx_bytes, wan3_rx_bytes_rate, wan3_rx_pkts, wan3_rx_drop, wan3_tx_bytes, wan3_tx_bytes_rate, wan3_tx_pkts, wan3_tx_drop, 
	 lte1_rx_bytes, lte1_rx_bytes_rate, lte1_rx_pkts, lte1_rx_drop, lte1_tx_bytes, lte1_tx_bytes_rate, lte1_tx_pkts, lte1_tx_drop,
	 lte2_rx_bytes, lte2_rx_bytes_rate, lte2_rx_pkts, lte2_rx_drop, lte2_tx_bytes, lte2_tx_bytes_rate, lte2_tx_pkts, lte2_tx_drop,
	 lte3_rx_bytes, lte3_rx_bytes_rate, lte3_rx_pkts, lte3_rx_drop, lte3_tx_bytes, lte3_tx_bytes_rate, lte3_tx_pkts, lte3_tx_drop,
	 sdwan_rx_bytes, sdwan_rx_bytes_rate, sdwan_rx_pkts, sdwan_rx_drop, sdwan_tx_bytes, sdwan_tx_bytes_rate, sdwan_tx_pkts, sdwan_tx_drop,
	 wan1_latency, wan2_latency, wan3_latency, lte1_latency, lte2_latency, sdwan_latency, 
	 wan1_jitter, wan2_jitter, wan3_jitter, lte1_jitter, lte2_jitter, sdwan_jitter,
	 id_rand_key) 
	 values 
	( '$_serialnumber', 
	  $_lan_rx_bytes,  $_lan_rx_bytes_rate,  $_lan_rx_pkts,  $_lan_rx_drop,  $_lan_tx_bytes,  $_lan_tx_bytes_rate,  $_lan_tx_pkts,  $_lan_tx_drop, 
	  $_wan1_rx_bytes, $_wan1_rx_bytes_rate, $_wan1_rx_pkts, $_wan1_rx_drop, $_wan1_tx_bytes, $_wan1_tx_bytes_rate, $_wan1_tx_pkts, $_wan1_tx_drop,
	  $_wan2_rx_bytes, $_wan2_rx_bytes_rate, $_wan2_rx_pkts, $_wan2_rx_drop, $_wan2_tx_bytes, $_wan2_tx_bytes_rate, $_wan2_tx_pkts, $_wan2_tx_drop,
	  $_wan3_rx_bytes, $_wan3_rx_bytes_rate, $_wan3_rx_pkts, $_wan3_rx_drop, $_wan3_tx_bytes, $_wan3_tx_bytes_rate, $_wan3_tx_pkts, $_wan3_tx_drop,
	  $_lte1_rx_bytes, $_lte1_rx_bytes_rate, $_lte1_rx_pkts, $_lte1_rx_drop, $_lte1_tx_bytes, $_lte1_tx_bytes_rate, $_lte1_tx_pkts, $_lte1_tx_drop,
	  $_lte2_rx_bytes, $_lte2_rx_bytes_rate, $_lte2_rx_pkts, $_lte2_rx_drop, $_lte2_tx_bytes, $_lte2_tx_bytes_rate, $_lte2_tx_pkts, $_lte2_tx_drop,
	  $_lte3_rx_bytes, $_lte3_rx_bytes_rate, $_lte3_rx_pkts, $_lte3_rx_drop, $_lte3_tx_bytes, $_lte3_tx_bytes_rate, $_lte3_tx_pkts, $_lte3_tx_drop,
	  $_sdwan_rx_bytes, $_sdwan_rx_bytes_rate, $_sdwan_rx_pkts, $_sdwan_rx_drop, $_sdwan_tx_bytes, $_sdwan_tx_bytes_rate, $_sdwan_tx_pkts, $_sdwan_tx_drop,
	  $_wan1_latency, $_wan2_latency, $_wan3_latency, $_lte1_latency, $_lte2_latency, $_sdwan_latency, 
	  $_wan1_jitter, $_wan2_jitter, $_wan3_jitter, $_lte1_jitter, $_lte2_jitter, $_sdwan_jitter,
	  '$_id_rand_key' )";
	$db->query($query2);
	//_debug_api_test_to_db($db, "$query2");
	
	
	//------ Post a ticket ??
	if($ticket_generated=="yes")
	{	/* disable osticket jobs
		$_ip =  $_SERVER['REMOTE_ADDR'];
		$query2 = "insert into smoad_osticket_jobs (device_serialnumber, subject, message, ip, priority) values
			('$_serialnumber', '$_ticket_subject', '$_ticket_body', '$_ip', '$_ticket_prio')";
		$db->query($query2);
		*/
		//_debug_api_test_to_db($db, "$query2");
	}
	
	//trigger alert - sdwan down
	if($_link_status_sdwan_down_count==1)
	{	if(api_alert_check_alert_enabled($db, 'link_status_sdwan_down')=='TRUE')
		{	$query2 = "select customer_id, details FROM smoad_devices where serialnumber=\"$_serialnumber\"";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$customer_id = $row2['customer_id'];
					$details = $row2['details'];
				}
			}
		
			$alert = "EDGE SD-WAN network is down - $details !";
			$_id_rand_key = random_bytes(6);	$_id_rand_key = bin2hex($_id_rand_key);
			$edge_details = api_alert_get_dev_details_for_alert($db, $_serialnumber, $alert);
			$query2="insert into smoad_alerts (title, type, details, id_rand_key) 
						values ('$alert', 'edge', '$edge_details', '$_id_rand_key')";
			$db->query($query2);
			
			if(api_alert_check_alert_enabled($db, 'link_status_sdwan_down_mail')=='TRUE')
			{ api_send_mail_for_alert_to_customer($db, $alert, $edge_details, $customer_id); }
		}
	}
	
	//trigger alert - sdwan up
	if($_link_status_sdwan_up_count==1)
	{	if(api_alert_check_alert_enabled($db, 'link_status_sdwan_up')=='TRUE')
		{	$query2 = "select customer_id, details FROM smoad_devices where serialnumber=\"$_serialnumber\"";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$customer_id = $row2['customer_id'];
					$details = $row2['details'];
				}
			}
			
			$alert = "EDGE SD-WAN network is up - $details !";
			$_id_rand_key = random_bytes(6);	$_id_rand_key = bin2hex($_id_rand_key);
			$edge_details = api_alert_get_dev_details_for_alert($db, $_serialnumber, $alert);
			$query2="insert into smoad_alerts (title, type, details, id_rand_key) 
						values ('$alert', 'edge', '$edge_details', '$_id_rand_key')";
			$db->query($query2);
			
			if(api_alert_check_alert_enabled($db, 'link_status_sdwan_up_mail')=='TRUE')
			{ api_send_mail_for_alert_to_customer($db, $alert, $edge_details, $customer_id); }
		}
	}
	
	print "success"; 
}

?>