<?php 
error_reporting(5);
include('../c/c_db_access.php');

$color_red = 'rgba(216,68,48,0.98)';
$color_blue = 'rgba(41,129,228,0.9)';
$color_yellow = 'rgba(255, 195, 0, 0.9)';
$color_green = 'rgba(33, 145, 80,0.9)';
$color_gray = 'rgba(200, 200, 200, 0.9)';


$pkt_drop_count_user=0;
$query = "select sum(pkt_count) pkt_drops FROM smoad_fw_log where action='drop' and type='user' and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$pkt_drop_count_user = $row['pkt_drops']; }	
}


$pkt_drop_count_ips=0;
$query = "select sum(pkt_count) pkt_drops FROM smoad_fw_log where action='drop' and type='ips' and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$pkt_drop_count_ips = $row['pkt_drops']; }	
}

$val1 = $pkt_drop_count_user; $val2 = $pkt_drop_count_ips;

$val1_name = 'User'; $val2_name = 'IPS';
$title = '';

$color1 = $color_blue; $color2 = $color_red;

?>

<html><head>
<style type="text/css">
#flotcontainerlan {width:220px;height:120px;background-color:white;font-size:11px;color:#444;font-weight:bold;}
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

<script src="../c/flot/jquery.min.js"></script>
<script src="../c/flot/excanvas.min.js"></script>
<script src="../c/flot/jquery.flot.min.js"></script>
<script src="../c/flot/jquery.flot.pie.min.js"></script>
<?php
print '<script type="text/javascript">$(function () {
  var lan_data = [
     {label: "'.$val1_name.' '.$val1.'", color: "'.$color1.'", data:'.$val1.'},
     {label: "'.$val2_name.' '.$val2.'", color: "'.$color2.'", data:'.$val2.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.36,radius:48,stroke:{width: 0.1}}},legend: {position:"se",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-family:'Gilroy', arial;font-size:12px;color:#555555;font-weight:bold;">
<div id="flotcontainerlan"><?php print $title; ?></div></body></html>
