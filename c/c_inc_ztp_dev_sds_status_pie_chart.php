<?php 
error_reporting(5);
include('c_db_access.php');

$type=$_GET['type'];
if($type=='ztp_dev' || $type=='ztp_dev_type' || $type=='ztp_dev_sdwan_assigned') { $table = 'smoad_devices'; }
else if($type=='ztp_sds' || $type=='ztp_sds_type') { $table = 'smoad_sdwan_servers'; }

$color_red = 'rgba(216,68,48,0.98)';
$color_blue = 'rgba(41,129,228,0.9)';
$color_yellow = 'rgba(255, 195, 0, 0.9)';
$color_green = 'rgba(33, 145, 80,0.9)';
$color_gray = 'rgba(200, 200, 200, 0.9)';

if($type=='ztp_dev' || $type=='ztp_sds')
{
	$up_dev_sds=0;
	$query = "select count(*) qty FROM $table where status='up'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$up_dev_sds = $row['qty']; }	
	}
	
	$total_dev_sds=0;
	$query = "select count(*) qty FROM $table";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$total_dev_sds = $row['qty']; }	
	}
	$down_dev_sds = $total_dev_sds-$up_dev_sds;
	
	$val1 = $up_dev_sds; $val2 = $down_dev_sds;
	
	$val1_name = 'Up'; $val2_name = 'Down';
	$title = '';
	
	$color1 = $color_green; $color2 = 'rgba(200, 200, 200, 0.9)';
}
else if($type=='ztp_dev_type')
{	$beetle=0;
	$query = "select count(*) qty FROM $table where model='beetle'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$beetle = $row['qty']; }	
	}
	
	$spider=0;
	$query = "select count(*) qty FROM $table where model='spider'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$spider = $row['qty']; }	
	}
	
	$total_dev=$beetle+$spider;
	
	$val1 = $beetle; $val2 = $spider;
	$val1_name = 'Beetle'; $val2_name = 'Spider';
	
	$title = '';

	$color1 = $color_blue; $color2 = $color_yellow;
}
else if($type=='ztp_dev_sdwan_assigned')
{	$assigned_dev=0;
	$query = "select count(*) qty FROM $table where sdwan_server_ipaddr<>'notset'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$assigned_dev = $row['qty']; }	
	}
	
	$unassigned_dev=0;
	$query = "select count(*) qty FROM $table where sdwan_server_ipaddr='notset'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$unassigned_dev = $row['qty']; }	
	}
	
	$total_dev=$assigned_dev+$unassigned_dev;
	
	$val1 = $assigned_dev; $val2 = $unassigned_dev;
	$val1_name = 'SDWAN Assigned'; $val2_name = 'Unassigned';
	
	$title = '';

	$color1 = $color_blue; $color2 = $color_red;
}
else if($type=='ztp_sds_type')
{	$l2=0;
	$query = "select count(*) qty FROM $table where type='l2'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$l2 = $row['qty']; }	
	}
	
	$l3=0;
	$query = "select count(*) qty FROM $table where type='l3'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$l3 = $row['qty']; }	
	}
	
	$total_sds=$l2+$l3;
	
	$val1 = $l2; $val2 = $l3;
	$val1_name = 'L2 Servers'; $val2_name = 'L3 Servers';
	
	$title = '';
	
	$color1 = $color_blue; $color2 = $color_yellow;
}

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
     {label: "'.$val2_name.' '.$val2.'", color: "'.$color2.'", data:'.$val2.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.36,radius:48,stroke:{width: 0.1}}},legend: {position:"se",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-family:'Gilroy', arial;font-size:11px;color:#555555;font-weight:bold;">
<div id="flotcontainerlan"><?php print $title; ?></div></body></html>
