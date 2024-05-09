<?php

$main_table = "smoad_sdwan_server_vlans";
$main_table2 = "smoad_sdwan_servers";


function _vlan_id_in_use_device_count($db, $server_id, $vlan_id)
{	$query = "SELECT ipaddr FROM smoad_sdwan_servers WHERE id=$server_id ";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $server_ipaddr = $row['ipaddr']; } }
	
	$query = "SELECT count(*) device_count FROM smoad_devices WHERE sdwan_server_ipaddr=\"$server_ipaddr\" and vlan_id=$vlan_id ";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { return $row['device_count']; } }
	return 0;
}


if($_POST['command']=="add_vlan") 
{	$_details = $_POST['details']; $_vlan_id = $_POST['vlan_id']; 
	if($_details!="" && $_vlan_id!="")
	{	$_create=1;
		$query = "select vlan_id from smoad_sdwan_server_vlans where id_smoad_sdwan_servers=\"$G_sds_id\" and vlan_id=$_vlan_id"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	print "<pre>ERROR: Found already matching device VLAN-ID for this server !</pre>"; $_create=0;
			}
		}
		
		if($_create) 
		{	$query = "insert into smoad_sdwan_server_vlans (details, id_smoad_sdwan_servers, vlan_id) 
						values (\"$_details\", $G_sds_id, $_vlan_id )";
			$db->query($query);
			
			$job = "vlan_add=^".$_vlan_id.",$_details"."^";
	 		sm_ztp_sds_add_job($db, $G_sds_serialnumber, $job); 
			
			print "<pre>SUCCESS: VLAN-ID $_vlan_id added successfully !</pre>";
			print "<pre>NOTE: will be updated in the SDWAN Server shortly !</pre>";
		}
	}
}
else if($_POST['command']=="del_vlan") 
{	$_vlan_id = $_POST['vlan_id']; 

	if(_vlan_id_in_use_device_count($db, $G_sds_id, $_vlan_id)>0)
	{ print "<pre>ERROR: Found one or more devices using this VLAN-ID. So not deleting the same. Remove the associated devices and retry !</pre>"; }
	else
	{	
		$query = "delete from smoad_sdwan_server_vlans where vlan_id=$_vlan_id"; 
			$db->query($query);
			
		$job = "vlan_del=^".$_vlan_id."^";
 		sm_ztp_sds_add_job($db, $G_sds_serialnumber, $job);
			
		print "<pre>SUCCESS: VLAN-ID $_vlan_id removed successfully !</pre>";
		print "<pre>NOTE: will be updated in the SDWAN Server shortly !</pre>";
	}
}

if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
{
	print '<p><strong>Add VLAN:</strong></p>
	<p>';
	
	api_form_post($curr_page);
	api_input_hidden("command", "add_vlan");
	
	print '<table class="list_items2" style="width:1024px;font-size:10px;">';
	
	api_ui_config_option_text("Details", null, "details", null, null);
	api_ui_config_option_text("VLAN-ID", null, "vlan_id", null, null);
	api_ui_config_option_add(null);
	
	print '</table>
	<br>
	</form></p><hr id="hr_style">';
}

?>

<p><strong>SD-WAN Server VLANs:</strong></p>

<table class="list_items2" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Details</th><th>VLAN-ID</th><th></th></tr>

<?php

$query = "select id, details, vlan_id from smoad_sdwan_server_vlans where id_smoad_sdwan_servers=$G_sds_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$vlan_id = $row['vlan_id'];
			
			print "<tr><td>$id</td><td>$details</td><td>$vlan_id</td>";
		
			$_vlan_id_in_use_device_count = _vlan_id_in_use_device_count($db, $G_sds_id, $vlan_id);
			if($_vlan_id_in_use_device_count>0)
			{	print "<td>Associated Devices: $_vlan_id_in_use_device_count</td>"; }
			else
			{	print "<td>";
				if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
				{ 
					api_form_post($curr_page);
					api_input_hidden("command", "del_vlan");
					api_input_hidden("vlan_id", $vlan_id);
					print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete server: $details - $vlan_id ?\" class=\"top_title_icons\" />";
					print "</form>";
				}
				print "</td>";
			}
			print "</tr>";
		}
	}
?>
</table>



