<?php

$serialnumber = $_POST['device_serialnumber'];
$username = $_POST['username'];
if($username!=null) { print "User: $username<br>"; }

if($_POST['command']=="del_log") 
{	$_log_id = $_POST['log_id']; 
	if($_log_id>0)
	{	$query = "delete from smoad_user_device_access_log where id=$_log_id"; 
		$db->query($query);
	}
}

if($serialnumber!=null)
{	$query = "select id, details, serialnumber, area from smoad_devices where serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$serialnumber = $row['serialnumber'];
			$area = $row['area'];
		}	
	}
	print "<table class=\"config_settings\">";
	print "<tr><td align=right style=\"padding-right:10px;\">ID</td><td>$id</td></tr>";
	print "<tr><td align=right style=\"padding-right:10px;\">Details</td><td>$details</td></tr>";
	print "<tr><td align=right style=\"padding-right:10px;\">Serial Number</td><td>$serialnumber</td></tr>";
	print "<tr><td align=right style=\"padding-right:10px;\">Area</td><td>$area</td></tr>";
	print "</table>";
}
?>
	
<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th>
<?php 
if($username==null) { print "<th>Username</th>"; } //show user-name when searched by serial-number
if($serialnumber==null) { print "<th>Device Serial Number</th>"; } //show serial-number when searched by username
?>
<th>Auth Status</th>
<th>Access Type</th><th>Timestamp</th><!--<th id="table_shaded" >Rand Key</th>-->
<th id="table_shaded" ></th>
</tr>

<?php
print "<br><div style=\"font-size:12px;font-style: italic;\">* last 50 entries are shown here !</div>";
$limit_criteria = " limit 50 ";

$select_criteria = "";
if($serialnumber !=null) { $select_criteria = " where device_serialnumber=\"$serialnumber\" "; }
else if($username !=null) { $select_criteria = " where username=\"$username\" "; }

$query = "select id, access_type, username, device_serialnumber, auth_status, access_timestamp, id_rand_key from 
	smoad_user_device_access_log $select_criteria order by id desc $limit_criteria"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$access_type = $row['access_type'];
			$_username = $row['username'];
			$_serialnumber = $row['device_serialnumber'];
			$auth_status = $row['auth_status'];
			$timestamp = $row['access_timestamp'];
			$id_rand_key = $row['id_rand_key'];
		
			//if($auth_status=="fail") { $bg_style="style=\"background-color:#F77575;\""; } 
			//else { $bg_style="style=\"background-color:#D6FA89;\""; } //show green for both login, and logout
			
			print "<tr><td>$id</td>";
			if($serialnumber!=null) { print "<td>$_username</td>"; }
			if($username!=null) { print "<td>$_serialnumber</td>"; }
			print "<td $bg_style>$auth_status</td><td>$access_type</td><td>$timestamp</td>";
			//print "<td $bg_style>$id_rand_key</td>";

			print "<td $bg_style><form method=\"POST\" action=\"index.php?page=user_device_access_log\" >
					<input type=\"hidden\" name=\"command\" value=\"del_log\" />
					<input type=\"hidden\" name=\"log_id\" value=\"$id\" />
					<input type=\"submit\" name=\"submit_ok\" value=\"Delete\" style=\"border:0;\" class=\"a_button_red\" />
					</form></td>";
		
			print "</tr>";
		}
	}
?>
</table>


<!--<em>* page will auto refresh every 60 seconds !</em>
<meta HTTP-EQUIV="REFRESH" content="60; url=index.php?page=tunnels">-->




