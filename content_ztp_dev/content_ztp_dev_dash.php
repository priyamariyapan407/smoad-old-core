


<table style="width:100%;"><tr>
<td style="vertical-align:top;width:45%;">
<table width="512px;">
<tr><td>
<?php

$query = "select id, details, license, serialnumber, model, model_variant, firmware, area, sdwan_server_ipaddr, sdwan_proto, vlan_id, 
			customer_id, enable, updated, uptime 
			from smoad_devices where serialnumber='$G_device_serialnumber'"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			
			$model = $row['model']; $model_variant = $row['model_variant'];
			
		}
	}

if($model=='vm' && $model_variant=="l2") { include('content_ztp_dev_port_status_vm_l2.php'); }
	else if($model=='vm' && $model_variant=="l3") { include('content_ztp_dev_port_status_vm_l3.php'); }
	else if($model=='spider' && $model_variant=="l2") { include('content_ztp_dev_port_status_spider_l2.php'); }
	else if($model=='spider' && $model_variant=="l2w1l2") { include('content_ztp_dev_port_status_spider_l2w1l2.php'); }
	else if($model=='spider' && $model_variant=="l3") { include('content_ztp_dev_port_status_spider_l3.php'); }
	else if($model=='spider2' && $model_variant=="l2") { include('content_ztp_dev_port_status_spider2_l2.php'); }
	else if($model=='spider2' && $model_variant=="l3") { include('content_ztp_dev_port_status_spider2_l3.php'); }
	else if($model=='beetle' && $model_variant=="l2") { include('content_ztp_dev_port_status_beetle_l2.php'); }
	else if($model=='beetle' && $model_variant=="l3") { include('content_ztp_dev_port_status_beetle_l3.php'); }
	else if($model=='bumblebee' && $model_variant=="l2") { include('content_ztp_dev_port_status_bumblebee_l2.php'); }
	else if($model=='bumblebee' && $model_variant=="l3") { include('content_ztp_dev_port_status_bumblebee_l3.php'); }
	else if($model=='wasp1' && $model_variant=="l2") { include('content_ztp_dev_port_status_wasp1_l2.php'); }
	else if($model=='wasp2' && $model_variant=="l2") { include('content_ztp_dev_port_status_wasp2_l2.php'); }
?>
</td></tr>
</table></td>
<td style="vertical-align:top;width:45%;"><b>Internet usage breakup (past 24 hours)</b><br>
<?php print "<iframe src=\"../c/c_inc_device_dash_pie_chart.php?serialnumber=$G_device_serialnumber\" style=\"width:340px;height:160px;border:0px;\" scrolling=\"no\"></iframe>"; ?>
</td>
</tr></table>


<?php 
	$_port_nw_stats = $_POST['port_nw_stats'];
	$_port_device_stats = $_POST['port_device_stats'];
	if($_port_nw_stats!=null && $_port_device_stats!=null)
	{ print "<iframe src=\"stats/bytes-area-plot.php?port_nw_stats=".$_port_nw_stats."&port_device_stats=".$_port_device_stats."&sn=$G_device_serialnumber\" style=\"width:100%;height:1400px;border:0px;\" scrolling=\"no\"></iframe>"; }
?>

<?php 
	$_port_nw_qos_stats = $_POST['port_nw_qos_stats'];
	if($_port_nw_qos_stats=="port_nw_qos_stats")
	{ print "<iframe src=\"stats/edge_qos_stats.php?port_nw_stats=".$_port_nw_stats."&sn=$G_device_serialnumber\" style=\"width:100%;height:1400px;border:0px;\" scrolling=\"no\"></iframe>"; }
	else if($_port_nw_qos_stats=="port_nw_qos_stats_live")
	{ print "<iframe src=\"stats/edge_qos_stats_live.php?port_nw_stats=".$_port_nw_stats."&sn=$G_device_serialnumber\" style=\"width:100%;height:460%;border:0px;\" scrolling=\"no\"></iframe>"; } 
?>
<br><br>
<meta http-equiv="refresh" content="60">
