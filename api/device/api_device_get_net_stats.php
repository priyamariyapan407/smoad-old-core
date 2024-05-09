<?php 

//external API for: Device (CPE) <> SD-WAN Server

error_reporting(5); 
include('../../c/c_db_access.php');
include('c_include.php');

$_md5_serialnumber = $_POST['device']; //this is an md5 of device serialnumber
$_data = $_POST['data'];

//print "proceed further 1<br>"; 

$proceed_further=false;

$proceed_further = _api_do_initial_due_diligence($db, $_md5_serialnumber, $_data, $_serialnumber);

//_debug_api_test_to_db($db, "$_md5_serialnumber - $_data");


if($proceed_further==true)
{	$_data = chop($_data);
	
	//check if the decrypted data contains some signatures. If so, then parse !
	if(strpos($_data, 'serialnumber') !== false && strpos($_data, 'command') !== false)
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
					
					case 'command'	: { $_command=$valuepair[1];
											 if($_command!='api_device_get_net_stats') { $proceed_further=false; }	
											 break; 
										  }
				}
			}
		}
	}
}

//--------------- construct reply -------------------
$api_reply = "";

$api_reply .= '<style type="text/css">
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
</style>
<script src="c/flot/jquery.min.js"></script>
<script src="c/flot/excanvas.min.js"></script>
<script src="c/flot/jquery.flot.min.js"></script>
<script src="c/flot/jquery.flot.time.min.js"></script>';

//get oldest log-entry timestamp vs now timestamp diff and calibrate accordingly
$query = "select (".time()." - UNIX_TIMESTAMP(log_timestamp)) AS log_timestamp_diff 
		from smoad_device_network_stats_log where device_serialnumber=\"$_serialnumber\" order by id limit 1"; 
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

$api_reply .= '<script type="text/javascript">
var options = { xaxis: { mode:"time",'.$timeformat.',timezone:"browser",'.$ticksize.' },
         grid: { borderWidth: {top: 1, right: 1, bottom: 1, left: 1}, borderColor: {top:"#999", bottom:"#999", left:"#999", right:"#999"} },
         series: { lines: {show:true,'.$linewidth.',steps:false,fill:true,fillColor: {colors:[{opacity:0.6},{opacity:0.3}]} }, shadowSize:0},
       };';

$api_reply .= 
'var lan_rx_rate_data = [];
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
function GetData() {';

$unit="";
$unit_str="";
$output="";


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
			$_log_timestamp_diff = $row2['log_timestamp_diff'];
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

_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "lan", $lan_rx_bytes_rate, $lan_tx_bytes_rate, $lan_unit_rx_name, $lan_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "wan1", $wan1_rx_bytes_rate, $wan1_tx_bytes_rate, $wan1_unit_rx_name, $wan1_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "wan2", $wan2_rx_bytes_rate, $wan2_tx_bytes_rate, $wan2_unit_rx_name, $wan2_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "lte1", $lte1_rx_bytes_rate, $lte1_tx_bytes_rate, $lte1_unit_rx_name, $lte1_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "lte2", $lte2_rx_bytes_rate, $lte2_tx_bytes_rate, $lte2_unit_rx_name, $lte2_unit_tx_name);
_get_network_bytes_rate_per_port_from_db($db, $_serialnumber, "lte3", $lte3_rx_bytes_rate, $lte3_tx_bytes_rate, $lte3_unit_rx_name, $lte3_unit_tx_name);

$api_reply .= 'lan_rx_rate_data = ['.$lan_rx_bytes_rate.'];'."\n";
$api_reply .= 'lan_tx_rate_data = ['.$lan_tx_bytes_rate.'];'."\n";
$api_reply .= 'wan1_rx_rate_data = ['.$wan1_rx_bytes_rate.'];'."\n";
$api_reply .= 'wan1_tx_rate_data = ['.$wan1_tx_bytes_rate.'];'."\n";
$api_reply .= 'wan2_rx_rate_data = ['.$wan2_rx_bytes_rate.'];'."\n";
$api_reply .= 'wan2_tx_rate_data = ['.$wan2_tx_bytes_rate.'];'."\n";
$api_reply .= 'lte1_rx_rate_data = ['.$lte1_rx_bytes_rate.'];'."\n";
$api_reply .= 'lte1_tx_rate_data = ['.$lte1_tx_bytes_rate.'];'."\n";
$api_reply .= 'lte2_rx_rate_data = ['.$lte2_rx_bytes_rate.'];'."\n";
$api_reply .= 'lte2_tx_rate_data = ['.$lte2_tx_bytes_rate.'];'."\n";
$api_reply .= 'lte3_rx_rate_data = ['.$lte3_rx_bytes_rate.'];'."\n";
$api_reply .= 'lte3_tx_rate_data = ['.$lte3_tx_bytes_rate.'];'."\n";

$color_blue = 'color: "rgba(41,129,228,0.96)"';
$color_red = 'color: "rgba(189,0,0,0.98)"';
$color_gray = 'color: "rgba(184,184,184,0.9)"';
$color_graydark = 'color: "rgba(102,102,102,0.9)"';
$color_black = 'color: "rgba(0,0,0,0.8)"';
$color_green = 'color: "rgba( 46, 204, 113, 0.9)"';
$color_yellow = 'color: "rgba( 255, 195, 0, 0.9)"';
$color_rx = 'color: "rgba(33, 145, 80, 0.9)"';
$color_tx = $color_blue;


$api_reply .= 'lan_rx_rate_data = [
    {	label: "Rx '.$lan_unit_rx_name.'", data: lan_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'lan_tx_rate_data = [
    {	label: "Tx '.$lan_unit_tx_name.'", data: lan_tx_rate_data,fill: true, '.$color_tx.', }
    ];';


$api_reply .= 'wan1_rx_rate_data = [
    {	label: "Rx '.$wan1_unit_rx_name.'", data: wan1_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'wan1_tx_rate_data = [
    {	label: "Tx '.$wan1_unit_tx_name.'", data: wan1_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$api_reply .= 'wan2_rx_rate_data = [
    {	label: "Rx '.$wan2_unit_rx_name.'", data: wan2_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'wan2_tx_rate_data = [
    {	label: "Tx '.$wan2_unit_tx_name.'", data: wan2_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$api_reply .= 'lte1_rx_rate_data = [
    {	label: "Rx '.$lte1_unit_rx_name.'", data: lte1_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'lte1_tx_rate_data = [
    {	label: "Tx '.$lte1_unit_tx_name.'", data: lte1_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$api_reply .= 'lte2_rx_rate_data = [
    {	label: "Rx '.$lte2_unit_rx_name.'", data: lte2_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'lte2_tx_rate_data = [
    {	label: "Tx '.$lte2_unit_tx_name.'", data: lte2_tx_rate_data,fill: true, '.$color_tx.', }
    ];';

$api_reply .= 'lte3_rx_rate_data = [
    {	label: "Rx '.$lte3_unit_rx_name.'", data: lte3_rx_rate_data,fill: true, '.$color_rx.', }
    ];';
$api_reply .= 'lte3_tx_rate_data = [
    {	label: "Tx '.$lte3_unit_tx_name.'", data: lte3_tx_rate_data,fill: true, '.$color_tx.', }
    ];';
$api_reply .= '}';
        

$api_reply .= '$(document).ready(function() {';
$api_reply .= 'GetData();';
$api_reply .= '$.plot($("#lan_rx_rate"), lan_rx_rate_data,options);';
$api_reply .= '$.plot($("#lan_tx_rate"), lan_tx_rate_data, options);';
$api_reply .= '$.plot($("#wan1_rx_rate"), wan1_rx_rate_data,options);';
$api_reply .= '$.plot($("#wan1_tx_rate"), wan1_tx_rate_data, options);';
$api_reply .= '$.plot($("#wan2_rx_rate"), wan2_rx_rate_data,options);';
$api_reply .= '$.plot($("#wan2_tx_rate"), wan2_tx_rate_data, options);';
$api_reply .= '$.plot($("#lte1_rx_rate"), lte1_rx_rate_data,options);';
$api_reply .= '$.plot($("#lte1_tx_rate"), lte1_tx_rate_data, options);';
$api_reply .= '$.plot($("#lte2_rx_rate"), lte2_rx_rate_data,options);';
$api_reply .= '$.plot($("#lte2_tx_rate"), lte2_tx_rate_data, options);';
$api_reply .= '$.plot($("#lte3_rx_rate"), lte3_rx_rate_data,options);';
$api_reply .= '$.plot($("#lte3_tx_rate"), lte3_tx_rate_data, options);';
$api_reply .= '});';
$api_reply .= '</script>';


$api_reply .= '<h2><b>LAN</b></h2>';
$api_reply .= "Received (".$lan_unit_rx_name.")"; 
$api_reply .= '<div id="lan_rx_rate"></div>';
$api_reply .= "Transferred (".$lan_unit_tx_name.")";
$api_reply .= '<div id="lan_tx_rate"></div>
<br>
<h2><b>WAN1</b></h2>';
$api_reply .= "Received (".$wan1_unit_rx_name.")"; 
$api_reply .= '<div id="wan1_rx_rate"></div>';
$api_reply .= "Transferred (".$wan1_unit_tx_name.")";
$api_reply .= '<div id="wan1_tx_rate"></div>
<br>
<h2><b>WAN2</b></h2>';
$api_reply .= "Received (".$wan2_unit_rx_name.")"; 
$api_reply .= '<div id="wan2_rx_rate"></div>';
$api_reply .= "Transferred (".$wan2_unit_tx_name.")";
$api_reply .= '<div id="wan2_tx_rate"></div>
<br>
<h2><b>LTE1</b></h2>';
$api_reply .= "Received (".$lte1_unit_rx_name.")";
$api_reply .= '<div id="lte1_rx_rate"></div>';
$api_reply .= "Transferred (".$lte1_unit_tx_name.")";
$api_reply .= '<div id="lte1_tx_rate"></div>
<br>
<h2><b>LTE2</b></h2>';
$api_reply .= "Received (".$lte2_unit_rx_name.")";
$api_reply .= '<div id="lte2_rx_rate"></div>';
$api_reply .= "Transferred (".$lte2_unit_tx_name.")";
$api_reply .= '<div id="lte2_tx_rate"></div>
<br>
<h2><b>LTE3</b></h2>';
$api_reply .= "Received (".$lte3_unit_rx_name.")";
$api_reply .= '<div id="lte3_rx_rate"></div>';
$api_reply .= "Transferred (".$lte3_unit_tx_name.")";
$api_reply .= '<div id="lte3_tx_rate"></div>';

//--------------- reply back to device --------------

if($proceed_further==true)
{	$query = "select api_device_pubkey from smoad_devices where serialnumber=\"$_serialnumber\""; 
	if($res = $db->query($query)) 
	{ while($row = $res->fetch_assoc()) 
  	  { $api_device_pubkey = hex2bin($row['api_device_pubkey']); $proceed_further=true; } 
  	}
}
	

if($proceed_further==true)
{	//if(openssl_public_encrypt($print_string, $print_string_encrypt, $api_device_pubkey)!=false) { $print_string_encrypt = bin2hex($print_string_encrypt); $proceed_further=true; } else { $proceed_further=false; }
	if(api_encrypt_to_hex($api_reply, $api_reply_encrypted, $api_device_pubkey)==false) { $proceed_further=false; } else { $proceed_further=true; } 
}

if($proceed_further==true) { print "$api_reply_encrypted"; }

?>