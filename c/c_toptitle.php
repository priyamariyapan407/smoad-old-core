<table summary="toptitle" width="100%" style="color:#3B3C3D;vertical-align:middle;background-color:#222;" >
<tr><td><img style="padding:8px;object-fit:cover;width:140px;" src="i/logo.png"></td><td>
<td style="font-size:16px;text-align:center;color:#eee;"><strong>SMOAD CORE - SD-WAN Orchestrator</strong></td>
<td>
<table cellpadding="4" style="color:#eee;width:100%;">
<tr><td style="vertical-align:middle;text-align:right;">
<?php error_reporting(5); session_start();

$query = "select count(*) total_alerts from smoad_alerts where status='new'";
	if($res = $db->query($query))
	{	while ($row = $res->fetch_assoc()) 
		{	$total_alerts = $row['total_alerts'];
		}
	}
	
	if($total_alerts==0) { $alert_msg = "You have no new alerts !"; $alert_icon="i/bell-white.png"; }
	else if($total_alerts==1) { $alert_msg = "You have $total_alerts new alert !"; $alert_icon="i/bell-red.png"; }
	else { $alert_msg = "You have $total_alerts new alerts !"; $alert_icon="i/bell-red.png"; }

print "<a href=\"index.php?page=alerts&skey=$session_key\" style=\"text-decoration:none;color:#fff;\">";	
print "<img class=\"top_title_icons\" src=\"$alert_icon\" title=\"$alert_msg\" />"; if($total_alerts>0) { print "<sup>$total_alerts</sup>"; }
print "</a>";
print "<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";

$username = $_SESSION['username'];
$hostname = $_SESSION['hostname'];
//print "Host: $hostname &nbsp; User: $_login_current_user";
if($login_type=='root') { $_login_type="Root"; }
else if($login_type=='customer') { $_login_type="Customer"; }
else if($login_type=='admin') { $_login_type="Admin"; }
else if($login_type=='limited') { $_login_type="Limited"; }
print "<img class=\"top_title_icons\" src=\"i/user.png\" title=\"User type: $_login_type\" /><sup>$username</sup>";
?> </td>
<td style="width:50px;text-align:right;"><a href="c/c_logout.php" title="Logout" ><img src="i/logout-small.png"  style="width:34px;height:34px;-webkit-transform: scaleX(-1);transform: scaleX(-1);" /></a></td>
</tr>
</table>

</td></tr></table>
