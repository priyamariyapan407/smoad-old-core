<?php 
error_reporting(5);
include('c_db_access.php');
$table = 'smoad_devices';

$color_red = 'rgba(216,68,48,0.98)';
$color_blue = 'rgba(41,129,228,0.9)';
$color_yellow = 'rgba(255, 195, 0, 0.9)';
$color_green = 'rgba(33, 145, 80,0.9)';
$color_gray = 'rgba(200, 200, 200, 0.9)';
$color_pink = 'rgba(250, 78, 171,0.98)';
$color_purple = 'rgba(160, 24, 245,0.98)';

$wan_up=0;
$query = "select count(*) qty from smoad_device_network_cfg 
where (wan_link_status='up' or wan2_link_status='up' or wan3_link_status='up') 
and (lte1_link_status='down' or lte1_link_status='notset') 
and (lte2_link_status='down' or lte2_link_status='notset') 
and (lte3_link_status='down' or lte3_link_status='notset')";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$wan_up = $row['qty']; }	
}

$lte_up=0;
$query = "select count(*) qty from smoad_device_network_cfg 
where (lte1_link_status='up' or lte2_link_status='up' or lte3_link_status='up') 
and (wan_link_status='down' or wan_link_status='notset') 
and (wan2_link_status='down' or wan2_link_status='notset') 
and (wan3_link_status='down' or wan3_link_status='notset')";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$lte_up = $row['qty']; }	
}


$wan_lte_up=0;
$query = "select count(*) qty from smoad_device_network_cfg 
where (wan_link_status='up' or wan2_link_status='up' or wan3_link_status='up') 
and (lte1_link_status='up' or lte2_link_status='up' or lte3_link_status='up')";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$wan_lte_up = $row['qty']; }	
}

$wan_lte_down=0;
$query = "select count(*) qty from smoad_device_network_cfg 
where (wan_link_status='down' or wan_link_status='notset') 
and (wan2_link_status='down' or wan2_link_status='notset') 
and (wan3_link_status='down' or wan3_link_status='notset')
and (lte1_link_status='down' or lte1_link_status='notset') 
and (lte2_link_status='down' or lte2_link_status='notset') 
and (lte3_link_status='down' or lte3_link_status='notset')";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$wan_lte_down = $row['qty']; }	
}

$val1 = $wan_up; $val2 = $lte_up; $val3 = $wan_lte_up; $val4 = $wan_lte_down;
$val1_name = 'WAN Up'; $val2_name = 'LTE Up'; $val3_name = 'WAN LTE Up'; $val4_name = 'WAN LTE Down';

$title = '';

$color1 = $color_blue; $color2 = $color_yellow; $color3 = $color_green; $color4 = $color_gray;

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

<script src="flot/jquery.min.js"></script>
<script src="flot/excanvas.min.js"></script>
<script src="flot/jquery.flot.min.js"></script>
<script src="flot/jquery.flot.pie.min.js"></script>
<?php
print '<script type="text/javascript">$(function () {
  var lan_data = [
     {label: "'.$val1_name.' '.$val1.'", color: "'.$color1.'", data:'.$val1.'},
     {label: "'.$val2_name.' '.$val2.'", color: "'.$color2.'", data:'.$val2.'},  
     {label: "'.$val3_name.' '.$val3.'", color: "'.$color3.'", data:'.$val3.'},
     {label: "'.$val4_name.' '.$val4.'", color: "'.$color4.'", data:'.$val4.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.36,radius:48,stroke:{width: 0.1}}},legend: {position:"se",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-family:'Gilroy', arial;font-size:11px;color:#555555;font-weight:bold;">
<div id="flotcontainerlan"><?php print $title; ?></div></body></html>
