<br><br>
<b>Packet Drops (Past 24 hours)</b><br>
<iframe src="stats/fw-drop-pkt-area-plot.php" style="width:90%;height:200px;border:0px;" scrolling="no"></iframe><br>
<br>

<b>Packet Drops - User defined vs IPS (AI) (Past 24 hours)</b><br>
<iframe src="content/content_firewall_dash_status_pie_chart.php" style="width:90%;height:160px;border:0px;" scrolling="no"></iframe><br>


<?php
$title = "Dropped Packets - IP Addr Tracking (past 24 hour)";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>Source IP-Address</th><th>Dropped Packets</th></tr>";

$query = "SELECT src_ip FROM smoad_fw_log WHERE action='drop' and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR) GROUP BY src_ip";
if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$src_ip = $row['src_ip'];	
			$query2 = "SELECT count(*) pkt_drop_count FROM smoad_fw_log WHERE action='drop' and src_ip='$src_ip' and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$pkt_drop_count = $row2['pkt_drop_count'];	
					
				}
			}
			$contents  .= "<tr><td>$src_ip</td><td>$pkt_drop_count</td></tr>";
		}
	}
$contents .= "</table>";
widget(780, $title, $contents);



?>