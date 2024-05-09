<?php $output = "";

$output .= '<style type="text/css">
#lan_rx_rate { width:98%;height:112px;background-color:white;}
#lan_tx_rate { width:98%;height:112px;background-color:white;}
#wan1_rx_rate { width:98%;height:112px;background-color:white;}
#wan1_tx_rate { width:98%;height:112px;background-color:white;}
#wan2_rx_rate { width:98%;height:112px;background-color:white;}
#wan2_tx_rate { width:98%;height:112px;background-color:white;}
#lte1_rx_rate { width:98%;height:112px;background-color:white;}
#lte1_tx_rate { width:98%;height:112px;background-color:white;}
#lte2_rx_rate { width:98%;height:112px;background-color:white;}
#lte2_tx_rate { width:98%;height:112px;background-color:white;}
#lte3_rx_rate { width:98%;height:112px;background-color:white;}
#lte3_tx_rate { width:98%;height:112px;background-color:white;}
</style>';

// since this include can be called outside, if this is not set, then set the default path
if($flot_base_path==null) { $flot_base_path = "c/flot/"; }

$output .= "<script src=\"$flot_base_path"."jquery.min.js\"></script>";
$output .= "<script src=\"$flot_base_path"."excanvas.min.js\"></script>";
$output .= "<script src=\"$flot_base_path"."jquery.flot.min.js\"></script>";
$output .= "<script src=\"$flot_base_path"."jquery.flot.time.min.js\"></script>";

//get oldest log-entry timestamp vs now timestamp diff and calibrate accordingly
$query = "select (".time()." - UNIX_TIMESTAMP(log_timestamp)) AS log_timestamp_diff 
		from smoad_device_network_stats_log where device_serialnumber=\"$G_device_serialnumber\" order by id limit 1"; 
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{ $log_timestamp_diff = $row['log_timestamp_diff'];
	  $log_timestamp_diff/=60; //convert to minutes
	}
}

//split into 20 sections (i.e only 20 ticks irrespective of total duration)
$calibrate_ticksize = round($log_timestamp_diff/20, 0);

$ticksize="tickSize:[".$calibrate_ticksize.",\"minute\"]"; //default
$timeformat="timeformat:\"%H:%M\"";
$linewidth="lineWidth:1";

$output .= '<script type="text/javascript">
			var options = { xaxis: { mode:"time",'.$timeformat.',timezone:"browser",'.$ticksize.' },
         	grid: { borderWidth: {top: 1, right: 1, bottom: 1, left: 1}, borderColor: {top:"#999", bottom:"#999", left:"#999", right:"#999"} },
         	series: { lines: {show:true,'.$linewidth.',steps:false,fill:true,fillColor: {colors:[{opacity:0.6},{opacity:0.3}]} }, shadowSize:0},
       		};

var lan_rx_rate_data = [];
var lan_tx_rate_data = [];
var wan1_rx_rate_data = [];
var wan1_tx_rate_data = [];
var wan2_rx_rate_data = [];
var wan2_tx_rate_data = [];
var lte1_rx_rate_data = [];
var lte1_tx_rate_data = [];
var lte2_rx_rate_data = [];
var lte2_tx_rate_data = [];
var lte3_rx_rate_data = [];
var lte3_tx_rate_data = [];

function GetData() {	';

function _get_network_bytes_rate_per_port_from_db($db, $serialnumber, $port, &$rx_bytes_rate, &$tx_bytes_rate, &$unit_rx_name, &$unit_tx_name)
{	//NOTE: stats already in KB
	//set the units
	$unit_rx=$unit_tx=1; 
	$unit_rx_name = $unit_tx_name = "Kb/s";
	$query = "select max(".$port."_rx_bytes_rate) max_bits_rx, max(".$port."_tx_bytes_rate) max_bits_tx from smoad_device_network_stats_log 
	 	where log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR) and device_serialnumber=\"$serialnumber\"";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{ $max_bits_rx = $row['max_bits_rx'];
		  $max_bits_tx = $row['max_bits_tx'];
		  $max_bits_rx = $max_bits_rx*8; //convert to bits
		  $max_bits_tx = $max_bits_tx*8; //convert to bits
		  if($max_bits_rx>1100) { $unit_rx=1000; $unit_rx_name = "Mb/s"; $max_bits_rx/=1000; } //Mb
		  if($max_bits_tx>1100) { $unit_tx=1000; $unit_tx_name = "Mb/s"; $max_bits_tx/=1000; } //Mb
		  if($max_bits_rx>1100) { $unit_rx=1000000; $unit_rx_name = "Gb/s"; $max_bits_rx/=1000000; } //Gb
		  if($max_bits_tx>1100) { $unit_tx=1000000; $unit_tx_name = "Gb/s"; $max_bits_tx/=1000000; } //Gb
		}
	}


	$rx_bytes_rate = ""; $tx_bytes_rate = ""; 
	$count=0;
	$query2 = "SELECT UNIX_TIMESTAMP(log_timestamp)*1000 log_timestamp,
			    ".$port."_rx_bytes_rate rx_bytes_rate, ".$port."_tx_bytes_rate tx_bytes_rate 
			FROM smoad_device_network_stats_log
			WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR) ORDER BY id DESC ";
	//print "$query2 <br>";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$_log_timestamp = $row2['log_timestamp'];
			$_rx_bytes_rate = ($row2['rx_bytes_rate']*8)/$unit_rx;
			$_tx_bytes_rate = ($row2['tx_bytes_rate']*8)/$unit_tx;
			
			if($count>0) 
 	  		{ $rx_bytes_rate.=",";
		 	  $tx_bytes_rate.=",";
 	  		}
			
			$count++;
			$rx_bytes_rate.="[$_log_timestamp, $_rx_bytes_rate]";
	  		$tx_bytes_rate.="[$_log_timestamp, $_tx_bytes_rate]";
		}	
	}
}

_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "lan", $lan_rx_bytes_rate, $lan_tx_bytes_rate, $lan_unit_rx_name, $lan_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "wan1", $wan1_rx_bytes_rate, $wan1_tx_bytes_rate, $wan1_unit_rx_name, $wan1_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "wan2", $wan2_rx_bytes_rate, $wan2_tx_bytes_rate, $wan2_unit_rx_name, $wan2_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "lte1", $lte1_rx_bytes_rate, $lte1_tx_bytes_rate, $lte1_unit_rx_name, $lte1_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "lte2", $lte2_rx_bytes_rate, $lte2_tx_bytes_rate, $lte2_unit_rx_name, $lte2_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $G_device_serialnumber, "lte3", $lte3_rx_bytes_rate, $lte3_tx_bytes_rate, $lte3_unit_rx_name, $lte3_unit_tx_name);


$output .= "lan_rx_rate_data = [".$lan_rx_bytes_rate."];\n";
$output .= "lan_tx_rate_data = [".$lan_tx_bytes_rate."];\n";
$output .= "wan1_rx_rate_data = [".$wan1_rx_bytes_rate."];\n";
$output .= "wan1_tx_rate_data = [".$wan1_tx_bytes_rate."];\n";
$output .= "wan2_rx_rate_data = [".$wan2_rx_bytes_rate."];\n";
$output .= "wan2_tx_rate_data = [".$wan2_tx_bytes_rate."];\n";
$output .= "lte1_rx_rate_data = [".$lte1_rx_bytes_rate."];\n";
$output .= "lte1_tx_rate_data = [".$lte1_tx_bytes_rate."];\n";
$output .= "lte2_rx_rate_data = [".$lte2_rx_bytes_rate."];\n";
$output .= "lte2_tx_rate_data = [".$lte2_tx_bytes_rate."];\n";
$output .= "lte3_rx_rate_data = [".$lte3_rx_bytes_rate."];\n";
$output .= "lte3_tx_rate_data = [".$lte3_tx_bytes_rate."];\n";

$color_blue = 'color: "rgba(41,129,228,0.96)"';
$color_red = 'color: "rgba(189,0,0,0.98)"';
$color_gray = 'color: "rgba(184,184,184,0.9)"';
$color_graydark = 'color: "rgba(102,102,102,0.9)"';
$color_black = 'color: "rgba(0,0,0,0.8)"';
$color_green = 'color: "rgba( 46, 204, 113, 0.9)"';
$color_yellow = 'color: "rgba( 255, 195, 0, 0.9)"';
$color_rx = 'color: "rgba(33, 145, 80, 0.9)"';
$color_tx = $color_blue;


$output .= 'lan_rx_rate_data = [
    {	label: "Rx '.$lan_unit_rx_name.'", data: lan_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'lan_tx_rate_data = [
    {	label: "Tx '.$lan_unit_tx_name.'", data: lan_tx_rate_data,fill: true, '.$color_tx.', }
    ];';


$output .= 'wan1_rx_rate_data = [
    {	label: "Rx '.$wan1_unit_rx_name.'", data: wan1_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'wan1_tx_rate_data = [
    {	label: "Tx '.$wan1_unit_tx_name.'", data: wan1_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$output .= 'wan2_rx_rate_data = [
    {	label: "Rx '.$wan2_unit_rx_name.'", data: wan2_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'wan2_tx_rate_data = [
    {	label: "Tx '.$wan2_unit_tx_name.'", data: wan2_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$output .= 'lte1_rx_rate_data = [
    {	label: "Rx '.$lte1_unit_rx_name.'", data: lte1_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'lte1_tx_rate_data = [
    {	label: "Tx '.$lte1_unit_tx_name.'", data: lte1_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$output .= 'lte2_rx_rate_data = [
    {	label: "Rx '.$lte2_unit_rx_name.'", data: lte2_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'lte2_tx_rate_data = [
    {	label: "Tx '.$lte2_unit_tx_name.'", data: lte2_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$output .= 'lte3_rx_rate_data = [
    {	label: "Rx '.$lte3_unit_rx_name.'", data: lte3_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$output .= 'lte3_tx_rate_data = [
    {	label: "Tx '.$lte3_unit_tx_name.'", data: lte3_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$output .= '} /*GetData()*/ ';

$output .= '$(document).ready(function() {';
$output .= 'GetData();';
$output .= '$.plot($("#lan_rx_rate"), lan_rx_rate_data,options);';
$output .= '$.plot($("#lan_tx_rate"), lan_tx_rate_data, options);';
$output .= '$.plot($("#wan1_rx_rate"), wan1_rx_rate_data,options);';
$output .= '$.plot($("#wan1_tx_rate"), wan1_tx_rate_data, options);';
$output .= '$.plot($("#wan2_rx_rate"), wan2_rx_rate_data,options);';
$output .= '$.plot($("#wan2_tx_rate"), wan2_tx_rate_data, options);';
$output .= '$.plot($("#lte1_rx_rate"), lte1_rx_rate_data,options);';
$output .= '$.plot($("#lte1_tx_rate"), lte1_tx_rate_data, options);';
$output .= '$.plot($("#lte2_rx_rate"), lte2_rx_rate_data,options);';
$output .= '$.plot($("#lte2_tx_rate"), lte2_tx_rate_data, options);';
$output .= '$.plot($("#lte3_rx_rate"), lte3_rx_rate_data,options);';
$output .= '$.plot($("#lte3_tx_rate"), lte3_tx_rate_data, options);';
$output .= '});';
$output .= '</script>';


$output .= '<h2><b>LAN</b></h2>';
$output .= "Received (".$lan_unit_rx_name.")"; 
$output .= '<div id="lan_rx_rate"></div>';
$output .= "Transferred (".$lan_unit_tx_name.")"; 
$output .= '<div id="lan_tx_rate"></div>
<br>
<h2><b>WAN1</b></h2>';
$output .= "Received (".$wan1_unit_rx_name.")";
$output .= '<div id="wan1_rx_rate"></div>';
$output .= "Transferred (".$wan1_unit_tx_name.")";
$output .= '<div id="wan1_tx_rate"></div>
<br>
<h2><b>WAN2</b></h2>';
$output .= "Received (".$wan2_unit_rx_name.")";
$output .= '<div id="wan2_rx_rate"></div>';
$output .= "Transferred (".$wan2_unit_tx_name.")";
$output .= '<div id="wan2_tx_rate"></div>
<br>
<h2><b>LTE1</b></h2>';
$output .= "Received (".$lte1_unit_rx_name.")";
$output .= '<div id="lte1_rx_rate"></div>';
$output .= "Transferred (".$lte1_unit_tx_name.")";
$output .= '<div id="lte1_tx_rate"></div>
<br>
<h2><b>LTE2</b></h2>';
$output .= "Received (".$lte2_unit_rx_name.")";
$output .= '<div id="lte2_rx_rate"></div>';
$output .= "Transferred (".$lte2_unit_tx_name.")";
$output .= '<div id="lte2_tx_rate"></div>
<br>
<h2><b>LTE3</b></h2>';
$output .= "Received (".$lte3_unit_rx_name.")";
$output .= '<div id="lte3_rx_rate"></div>';
$output .= "Transferred (".$lte3_unit_tx_name.")";
$output .= '<div id="lte3_tx_rate"></div>';
?>

