<?php 
error_reporting(5);
include('c_db_access.php');

//$title = "WAN ;
$serialnumber = $_GET['serialnumber'];
$query = "select 
	avg(wan1_rx_bytes_rate) avg_wan1_rx_bytes_rate, avg(wan1_tx_bytes_rate) avg_wan1_tx_bytes_rate,
	avg(wan2_rx_bytes_rate) avg_wan2_rx_bytes_rate, avg(wan2_tx_bytes_rate) avg_wan2_tx_bytes_rate,
	avg(wan3_rx_bytes_rate) avg_wan3_rx_bytes_rate, avg(wan3_tx_bytes_rate) avg_wan3_tx_bytes_rate,
	avg(lte1_rx_bytes_rate) avg_lte1_rx_bytes_rate, avg(lte1_tx_bytes_rate) avg_lte1_tx_bytes_rate,
	avg(lte2_rx_bytes_rate) avg_lte2_rx_bytes_rate, avg(lte2_tx_bytes_rate) avg_lte2_tx_bytes_rate,
	avg(lte3_rx_bytes_rate) avg_lte3_rx_bytes_rate, avg(lte3_tx_bytes_rate) avg_lte3_tx_bytes_rate
	from smoad_device_network_stats_log where device_serialnumber = \"$serialnumber\" and 
	log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)
	"; 
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$avg_wan1_bytes_rate = $row['avg_wan1_rx_bytes_rate'] + $row['avg_wan1_tx_bytes_rate'];
		$avg_wan2_bytes_rate = $row['avg_wan2_rx_bytes_rate'] + $row['avg_wan2_tx_bytes_rate'];
		$avg_wan3_bytes_rate = $row['avg_wan3_rx_bytes_rate'] + $row['avg_wan3_tx_bytes_rate'];
		$avg_lte1_bytes_rate = $row['avg_lte1_rx_bytes_rate'] + $row['avg_lte1_tx_bytes_rate'];
		$avg_lte2_bytes_rate = $row['avg_lte2_rx_bytes_rate'] + $row['avg_lte2_tx_bytes_rate'];
		$avg_lte3_bytes_rate = $row['avg_lte3_rx_bytes_rate'] + $row['avg_lte3_tx_bytes_rate'];
	}	
}

$total = $avg_wan1_bytes_rate+$avg_wan2_bytes_rate+$avg_wan3_bytes_rate+$avg_lte1_bytes_rate+$avg_lte2_bytes_rate+$avg_lte3_bytes_rate;
$avg_wan1_bytes_rate_pct = number_format($avg_wan1_bytes_rate/$total*100, 1);
$avg_wan2_bytes_rate_pct = number_format($avg_wan2_bytes_rate/$total*100, 1);
$avg_wan3_bytes_rate_pct = number_format($avg_wan3_bytes_rate/$total*100, 1);
$avg_lte1_bytes_rate_pct = number_format($avg_lte1_bytes_rate/$total*100, 1);
$avg_lte2_bytes_rate_pct = number_format($avg_lte2_bytes_rate/$total*100, 1);
$avg_lte3_bytes_rate_pct = number_format($avg_lte3_bytes_rate/$total*100, 1);

//print "$avg_wan1_bytes_rate_pct $avg_lte1_bytes_rate_pct $avg_lte2_bytes_rate_pct ";

?>

<html><head>
<style type="text/css">
#flotcontainerlan {width:300px;height:190px;background-color:white;font-size:12px;color:#555555;font-weight:bold;}
/* geometric font */
@font-face {
    font-family: "Gilroy";
    src: url("Gilroy-Regular.ttf");
}
@font-face {
    font-family: "Gilroy";
    src: url("Gilroy-Bold.ttf");
    font-weight: bold;
}
</style>

<script src="flot/jquery.min.js"></script>
<script src="flot/excanvas.min.js"></script>
<script src="flot/jquery.flot.min.js"></script>
<script src="flot/jquery.flot.pie.min.js"></script>
<?php
print '<script type="text/javascript">$(function () {
  var lan_data = [
     {label: "WAN1 '.$avg_wan1_bytes_rate_pct.'% ", color: "rgba(33, 145, 80,0.9)", data:'.$avg_wan1_bytes_rate.'},
     {label: "WAN2 '.$avg_wan2_bytes_rate_pct.'% ", color: "rgba(255, 195, 0, 0.9)", data:'.$avg_wan2_bytes_rate.'},
     {label: "WAN3 '.$avg_wan3_bytes_rate_pct.'% ", color: "rgba(75,0,130, 0.9)", data:'.$avg_wan3_bytes_rate.'},
     {label: "LTE1 '.$avg_lte1_bytes_rate_pct.'% ", color: "rgba(41,129,228,0.9)", data:'.$avg_lte1_bytes_rate.'},
     {label: "LTE2 '.$avg_lte2_bytes_rate_pct.'% ", color: "rgba(216,68,48,0.98)", data:'.$avg_lte2_bytes_rate.'},
     {label: "LTE3 '.$avg_lte3_bytes_rate_pct.'% ", color: "rgba(250, 78, 171,0.98)", data:'.$avg_lte3_bytes_rate.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.33,radius:50,stroke:{width: 0.1}}},legend: {position:"ne",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-size:12px;color:#555555;font-family:'Gilroy', arial;font-weight:bold;">
<div id="flotcontainerlan"></div></body></html>
