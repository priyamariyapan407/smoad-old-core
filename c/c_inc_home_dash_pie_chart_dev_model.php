<?php 
error_reporting(5);
include('c_db_access.php');
$table = 'smoad_devices';

$color_red = 'rgba(216,68,48,0.98)';
$color_blue = 'rgba(41,129,228,0.9)';
$color_blue2 = 'rgba(154, 197, 244, 1)';
$color_yellow = 'rgba(255, 195, 0, 0.9)';
$color_green = 'rgba(33, 145, 80,0.9)';
$color_green2 = 'rgba(211, 208, 79, 1)';
$color_gray = 'rgba(200, 200, 200, 0.9)';
$color_pink = 'rgba(250, 78, 171,0.98)';
$color_purple = 'rgba(160, 24, 245,0.98)';


$beetle=0;
$query = "select count(*) qty FROM $table where model='beetle'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$beetle = $row['qty']; }	
}

$spider=0;
$query = "select count(*) qty FROM $table where model='spider'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) { $spider = $row['qty']; }	
}

$spider2=0;
$query = "select count(*) qty FROM $table where model='spider2'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$spider2 = $row['qty']; } }

$bumblebee=0;
$query = "select count(*) qty FROM $table where model='bumblebee'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$bumblebee = $row['qty']; } }

$wasp1=0;
$query = "select count(*) qty FROM $table where model='wasp1'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$wasp1 = $row['qty']; } }

$wasp2=0;
$query = "select count(*) qty FROM $table where model='wasp2'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$wasp2 = $row['qty']; } }

$vm=0;
$query = "select count(*) qty FROM $table where model='vm'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$vm = $row['qty']; } }

$soft_client=0;
$query = "select count(*) qty FROM $table where model='soft_client'";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$soft_client = $row['qty']; } }

$total_dev=$beetle+$spider;

$val1 = $beetle; $val2 = $spider; $val3 = $spider2; $val4 = $bumblebee; $val5 = $wasp1; $val6 = $wasp2; $val7 = $vm; $val8 = $soft_client;
$val1_name = 'Beetle'; $val2_name = 'Spider'; $val3_name = 'Spider2'; $val4_name = 'BumbleBee'; $val5_name = 'Wasp1'; $val6_name = 'Wasp2'; $val7_name = 'VM'; $val8_name = 'Soft-client';

$title = '';

$color1 = $color_blue; $color2 = $color_yellow; $color3 = $color_red; $color4 = $color_purple; $color5 = $color_green2; $color6 = $color_blue2; $color7 = $color_green; $color8 = $color_pink;



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
     {label: "'.$val4_name.' '.$val4.'", color: "'.$color4.'", data:'.$val4.'},
     {label: "'.$val5_name.' '.$val5.'", color: "'.$color5.'", data:'.$val5.'},
     {label: "'.$val6_name.' '.$val6.'", color: "'.$color6.'", data:'.$val6.'},
     {label: "'.$val7_name.' '.$val7.'", color: "'.$color7.'", data:'.$val7.'},
     {label: "'.$val8_name.' '.$val8.'", color: "'.$color8.'", data:'.$val8.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.36,radius:48,stroke:{width: 0.1}}},legend: {position:"se",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-family:'Gilroy', arial;font-size:11px;color:#555555;font-weight:bold;">
<div id="flotcontainerlan"><?php print $title; ?></div></body></html>
