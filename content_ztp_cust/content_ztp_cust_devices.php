<?php

include("c/c_api_remote_sdwan_server.php");

$query = "select ipaddr, sdwan_proto from smoad_sdwan_servers where serialnumber='$G_sds_serialnumber'"; 
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $sds_ipaddr = $row['ipaddr']; $sdwan_proto = $row['sdwan_proto']; } }

function _api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, $db_unit, &$elapsed_last_entry, &$elapsed_last_entry_unit)
{	$elapsed_last_entry = 0;
	$query2 = "select TIMESTAMPDIFF($db_unit, NOW(), log_timestamp) elapsed_last_entry FROM smoad_device_status_log
			WHERE id= $id"; 
	if($res2 = $db->query($query2)) { while($row2 = $res2->fetch_assoc()) { $elapsed_last_entry = abs($row2['elapsed_last_entry']); } }

	if($db_unit=="MONTH") { $elapsed_last_entry_unit = "Month(s)"; }
	else if($db_unit=="DAY") { $elapsed_last_entry_unit = "Day(s)"; }
	else if($db_unit=="HOUR") { $elapsed_last_entry_unit = "Hour(s)"; }
	else if($db_unit=="MINUTE") { $elapsed_last_entry_unit = "Minute(s)"; }
	else if($db_unit=="SECOND") { $elapsed_last_entry_unit = "Second(s)"; }
}

function _api_elapsed_log_timestamp_status_log_entry_device($db, $id, &$elapsed_last_entry, &$elapsed_last_entry_unit)
{	//get in months
	_api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, "MONTH", $elapsed_last_entry, $elapsed_last_entry_unit);
	
	//get in days
	if($elapsed_last_entry==0)
	{ _api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, "DAY", $elapsed_last_entry, $elapsed_last_entry_unit); }
	
	//else get in hours
	if($elapsed_last_entry==0)
	{ _api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, "HOUR", $elapsed_last_entry, $elapsed_last_entry_unit); }
	
	//else get in minutes
	if($elapsed_last_entry==0)
	{ _api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, "MINUTE", $elapsed_last_entry, $elapsed_last_entry_unit); }
	
	//else get in seconds
	if($elapsed_last_entry==0)
	{ _api_elapsed_log_timestamp_status_log_entry_device_core($db, $id, "SECOND", $elapsed_last_entry, $elapsed_last_entry_unit); }
}


if($_POST['command']=="assign_device") 
{	$_device_id = $_POST['device_id']; 
	$_device_serialnumber = $_POST['device_serialnumber'];
	
	if($_device_id>0)
	{	$query = "update smoad_devices set customer_id=$G_cust_id where id=$_device_id";
		$db->query($query);
			
		print "<pre>SUCCESS: Device $_device_serialnumber assigned to customer successfully !</pre>";
	}
}
else if($_POST['command']=="unassign_device") 
{	$_device_id = $_POST['device_id']; 
	$_device_serialnumber = $_POST['device_serialnumber'];
		
	
	if($_device_id>0)
	{	$query = "update smoad_devices set customer_id='notset' where id=$_device_id";
		$db->query($query);
		
		print "<pre>SUCCESS: Device $_device_serialnumber unassigned to customer successfully !</pre>";
	}
}


$query = "select type from smoad_sdwan_servers where serialnumber=\"$G_sds_serialnumber\""; 
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $type = $row['type']; } }


$query = "select count(*) qty from smoad_devices where customer_id='notset'"; 
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $qty = $row['qty']; } }
if($qty>0)
{	//filter and show only the EDGE devices matching the SDWAN Server type variant ( i.e L2<>L2, L3<>L3 )
	print "<p><strong>Unassigned Devices:</strong></p>";
	print "<table class=\"list_items\" style=\"width:99%;font-size:10px;\">";
	print "<tr><th>ID</th><th>Details</th><th>License</th><th>Serial Number</th><th>Model</th><th>Area</th><th></th></tr>";
	$query = "select id, details, license, serialnumber, model, model_variant, area 
		from smoad_devices where customer_id='notset'"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$id = $row['id'];
				$details = $row['details'];
				$license = $row['license'];
				$serialnumber = $row['serialnumber'];
				$model = $row['model']; $model_variant = $row['model_variant'];
				$area = $row['area'];
				
				if($model=="spider") { $model="Spider"; }
				else if($model=="beetle") { $model="Beetle"; }
				else if($model=="vm") { $model="VM"; }
				
				print "<tr><td>$id</td><td>$details</td>
						 <td>$license</td><td>$serialnumber</td>
						 <td>$model</td>
						 <td>$area</td>";
			
				print "<td $bg_style>";
				if($login_type=='root' || $login_type=='admin')
				{ 
					api_form_post($curr_page);
					api_input_hidden("command", "assign_device");
					api_input_hidden("device_id", $id);
					api_input_hidden("device_serialnumber", $serialnumber);
					api_input_hidden("device_details", $details);
					api_button_post("&#x2699; Assign", "nowarning", "green");
					print "</form>";
				}
				print "</td>";
				
				print "</tr>";
			}
		}
	print "</table>";
	print '<hr id="hr_style">';
}
?>

<p><strong>Assigned Devices:</strong></p>
<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Details</th><th>License</th><th>Serial Number</th><th>Model</th><th>Area</th><th style="text-align:center;">Status</th><th></th></tr>
<?php
$query = "select id, details, license, serialnumber, model, area, sdwan_server_ipaddr, vlan_id, sdwan_enable, enable, status 
			from smoad_devices where customer_id=$G_cust_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model = $row['model'];
			$area = $row['area'];
			$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
			$vlan_id = $row['vlan_id'];
			$sdwan_enable = $row['sdwan_enable'];
			$enable = $row['enable'];
			$status = $row['status'];
		
			if($model=="spider") { $model="Spider"; }
			else if($model=="beetle") { $model="Beetle"; }
			else if($model=="vm") { $model="VM"; }
			
			print "<tr><td>$id</td><td>$details</td>
					 <td>$license</td><td>$serialnumber</td>
					 <td>$model</td>
					 <td>$area</td>";
	
			if($status=='up') { $status=1; } else { $status=0; }
			$status = api_ui_up_down_display_status($status, null);
			print "<td>$status</td>";
			
			print "<td>";
			if($login_type=='root' || $login_type=='admin')
			{	api_form_post($curr_page);
				api_input_hidden("command", "unassign_device");
				api_input_hidden("device_id", $id);
				api_input_hidden("device_serialnumber", $serialnumber);
				api_input_hidden("device_details", $details);
				api_button_post("&#x2699; Unassign", "nowarning", "red");
				print "</form>";
			}
			print "</td>";
			print "</tr>";
		}
	}
	
?>
</table>
