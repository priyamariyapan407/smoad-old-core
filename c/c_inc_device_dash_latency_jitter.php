<?php $output = "";

$output .= '<style type="text/css">
#wan1_latency { width:98%;height:112px;background-color:white;}
#wan1_jitter { width:98%;height:112px;background-color:white;}
#wan2_latency { width:98%;height:112px;background-color:white;}
#wan2_jitter { width:98%;height:112px;background-color:white;}
#lte1_latency { width:98%;height:112px;background-color:white;}
#lte1_jitter { width:98%;height:112px;background-color:white;}
#lte2_latency { width:98%;height:112px;background-color:white;}
#lte2_jitter { width:98%;height:112px;background-color:white;}
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

var wan1_latency_data = [];
var wan1_jitter_data = [];
var wan2_latency_data = [];
var wan2_jitter_data = [];
var lte1_latency_data = [];
var lte1_jitter_data = [];
var lte2_latency_data = [];
var lte2_jitter_data = [];

function GetData() {	';

function _get_network_latency_jitter_per_port_from_db($db, $serialnumber, $port, &$latency, &$jitter)
{	//NOTE: stats already in KB
	//set the units
	$unit_rx=$unit_tx=1; 
	$unit_rx_name = $unit_tx_name = "Kb/s";
	

	$latency = ""; $jitter = ""; 
	$count=0;
	$query2 = "SELECT UNIX_TIMESTAMP(log_timestamp)*1000 log_timestamp,
			    ".$port."_latency latency, ".$port."_jitter jitter 
			FROM smoad_device_network_stats_log
			WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR) ORDER BY id DESC ";
	//print "$query2 <br>";
	if($res2 = $db->query($query2))
	{	while($row2 = $res2->fetch_assoc())
		{	$_log_timestamp = $row2['log_timestamp'];
			$_latency = $row2['latency'];
			$_jitter = $row2['jitter'];
			
			if($count>0) 
 	  		{ $latency.=",";
		 	  $jitter.=",";
 	  		}
			
			$count++;
			$latency.="[$_log_timestamp, $_latency]";
	  		$jitter.="[$_log_timestamp, $_jitter]";
		}	
	}
}

_get_network_latency_jitter_per_port_from_db($db, $G_device_serialnumber, "wan1", $wan1_latency, $wan1_jitter);
_get_network_latency_jitter_per_port_from_db($db, $G_device_serialnumber, "wan2", $wan2_latency, $wan2_jitter);
_get_network_latency_jitter_per_port_from_db($db, $G_device_serialnumber, "lte1", $lte1_latency, $lte1_jitter);
_get_network_latency_jitter_per_port_from_db($db, $G_device_serialnumber, "lte2", $lte2_latency, $lte2_jitter);

$output .= "wan1_latency_data = [".$wan1_latency."];\n";
$output .= "wan1_jitter_data = [".$wan1_jitter."];\n";
$output .= "wan2_latency_data = [".$wan2_latency."];\n";
$output .= "wan2_jitter_data = [".$wan2_jitter."];\n";
$output .= "lte1_latency_data = [".$lte1_latency."];\n";
$output .= "lte1_jitter_data = [".$lte1_jitter."];\n";
$output .= "lte2_latency_data = [".$lte2_latency."];\n";
$output .= "lte2_jitter_data = [".$lte2_jitter."];\n";

$color_blue = 'color: "rgba(41,129,228,0.96)"';
$color_red = 'color: "rgba(189,0,0,0.98)"';
$color_gray = 'color: "rgba(184,184,184,0.9)"';
$color_graydark = 'color: "rgba(102,102,102,0.9)"';
$color_black = 'color: "rgba(0,0,0,0.8)"';
$color_green = 'color: "rgba( 46, 204, 113, 0.9)"';
$color_yellow = 'color: "rgba( 255, 195, 0, 0.9)"';
$color_latency = $color_blue;
$color_jitter = $color_red;


$output .= 'wan1_latency_data = [
    {	label: "latency ms", data: wan1_latency_data,fill: true, '.$color_latency.', }
    ];';
$output .= 'wan1_jitter_data = [
    {	label: "jitter ms", data: wan1_jitter_data,fill: true, '.$color_jitter.', }
    ];';

$output .= 'wan2_latency_data = [
    {	label: "latency ms", data: wan2_latency_data,fill: true, '.$color_latency.', }
    ];';
$output .= 'wan2_jitter_data = [
    {	label: "jitter ms", data: wan2_jitter_data,fill: true, '.$color_jitter.', }
    ];';
    
$output .= 'lte1_latency_data = [
    {	label: "latency ms", data: lte1_latency_data,fill: true, '.$color_latency.', }
    ];';
$output .= 'lte1_jitter_data = [
    {	label: "jitter ms", data: lte1_jitter_data,fill: true, '.$color_jitter.', }
    ];';
    
$output .= 'lte2_latency_data = [
    {	label: "latency ms", data: lte2_latency_data,fill: true, '.$color_latency.', }
    ];';
$output .= 'lte2_jitter_data = [
    {	label: "jitter ms", data: lte2_jitter_data,fill: true, '.$color_jitter.', }
    ];';

$output .= '} /*GetData()*/ ';

$output .= '$(document).ready(function() {';
$output .= 'GetData();';
$output .= '$.plot($("#wan1_latency"), wan1_latency_data, options);';
$output .= '$.plot($("#wan1_jitter"), wan1_jitter_data, options);';
$output .= '$.plot($("#wan2_latency"), wan2_latency_data, options);';
$output .= '$.plot($("#wan2_jitter"), wan2_jitter_data, options);';
$output .= '$.plot($("#lte1_latency"), lte1_latency_data, options);';
$output .= '$.plot($("#lte1_jitter"), lte1_jitter_data, options);';
$output .= '$.plot($("#lte2_latency"), lte2_latency_data, options);';
$output .= '$.plot($("#lte2_jitter"), lte2_jitter_data, options);';
$output .= '});';
$output .= '</script>';


$output .= '<h2><b>WAN1</b></h2>';
$output .= "Latency ms";
$output .= '<div id="wan1_latency"></div>';
$output .= "Jitter ms";
$output .= '<div id="wan1_jitter"></div>
<br>
<h2><b>WAN1</b></h2>';
$output .= "Latency ms";
$output .= '<div id="wan2_latency"></div>';
$output .= "Jitter ms";
$output .= '<div id="wan2_jitter"></div>
<br>
<h2><b>LTE1</b></h2>';
$output .= "Latency ms";
$output .= '<div id="lte1_latency"></div>';
$output .= "Jitter ms";
$output .= '<div id="lte1_jitter"></div>
<br>
<h2><b>LTE2</b></h2>';
$output .= "Latency ms";
$output .= '<div id="lte2_latency"></div>';
$output .= "Jitter ms";
$output .= '<div id="lte2_jitter"></div>';

?>

