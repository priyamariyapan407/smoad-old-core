<?php 
error_reporting(5);
include('c_db_access.php');
$table = 'smoad_sdwan_servers';

$color_red = 'rgba(216,68,48,0.98)';
$color_blue = 'rgba(41,129,228,0.9)';
$color_yellow = 'rgba(255, 195, 0, 0.9)';
$color_green = 'rgba(33, 145, 80,0.9)';
$color_gray = 'rgba(200, 200, 200, 0.9)';
$color_pink = 'rgba(250, 78, 171,0.98)';
$color_purple = 'rgba(160, 24, 245,0.98)';


$wg=0;
$query = "select count(*) qty FROM $table where sdwan_proto='wg'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$wg = $row['qty']; }	
}

$notset=0;
$query = "select count(*) qty FROM $table where sdwan_proto='notset'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$notset = $row['qty']; }	
}

$mptcp=0;
$query = "select count(*) qty FROM $table where sdwan_proto='mptcp'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$mptcp = $row['qty']; }	
}

$val1 = $wg; $val2 = $mptcp; $val3 = $notset; 
$val1_name = 'SD-WAN'; $val2_name = 'MPTCP'; $val3_name = 'Not Set';

$title = '';

$color1 = $color_blue; $color2 = $color_red; $color3 = $color_gray;



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
     {label: "'.$val3_name.' '.$val3.'", color: "'.$color3.'", data:'.$val3.'}
  ];
  var options = { series: { pie: {show: true, innerRadius:0.36,radius:48,stroke:{width: 0.1}}},legend: {position:"se",borderColor:"#ffffff"} };
  $.plot($("#flotcontainerlan"), lan_data, options);
      
});</script>';
?>
</head><body style="font-family:'Gilroy', arial;font-size:11px;color:#555555;font-weight:bold;">
<div id="flotcontainerlan"><?php print $title; ?></div></body></html>
