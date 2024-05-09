<?php

$page_redirect=false;

function _server_in_use_device_count($db, $server_ipaddr)
{	$query = "SELECT count(*) device_count FROM smoad_devices WHERE sdwan_server_ipaddr=\"$server_ipaddr\"";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { return $row['device_count']; } }
	return 0;
}

$_invalid_inputs = false;

if($_POST['command']=="add_server") 
{	$_details = $_POST['details']; $_license = $_POST['license']; $_serialnumber = $_POST['serialnumber']; $_ipaddr = $_POST['ipaddr']; 
	$_area = $_POST['area']; $_type = $_POST['type'];
	
	$_serialnumber = randHash(16);
	
	$_details = api_prevent_sql_injection($db, $_details);
	$_license = api_prevent_sql_injection($db, $_license);
	$_serialnumber = api_prevent_sql_injection($db, $_serialnumber);
	$_ipaddr = api_prevent_sql_injection($db, $_ipaddr);
	$_area = api_prevent_sql_injection($db, $_area);
	
	if($_type=="mptcp") { $_sdwan_proto="mptcp"; } else { $_sdwan_proto="wg"; } 

	if($_details!="" && $_license!="" && $_serialnumber!="" && $_ipaddr!="" && $_area!="" && $_type!="" && $_sdwan_proto!="")
	{	$_create=1;
	
		$query = "select serialnumber from smoad_sdwan_servers where serialnumber=\"$_serialnumber\""; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	print "<pre>ERROR: Found already matching device Serial Number. So not creating this new device !</pre>"; $_create=0;
			}
		}
		
		if($_create) 
		{	$query = "insert into smoad_sdwan_servers (details, license, serialnumber, ipaddr, area, type, sdwan_proto) 
					values (\"$_details\", \"$_license\", \"$_serialnumber\", \"$_ipaddr\", \"$_area\", \"$_type\", \"$_sdwan_proto\" )";
			$db->query($query);
			//print "Query: $query <br>\n";
			print "<pre>SUCCESS: Server added successfully !</pre>";
		}
	}
	else { $_invalid_inputs=true; }
}
else if($_POST['command']=="del_server") 
{	$_server_id = $_POST['server_id']; 
	$_server_ipaddr = $_POST['server_ipaddr'];
	$_server_serialnumber = $_POST['server_serialnumber'];
	if($_server_id>0)
	{	if(_server_in_use_device_count($db, $_server_ipaddr)>0)
		{ print "<pre>ERROR: Found one or more devices using this SD-WAN Server. So not deleting the same. Remove the associated devices and retry !</pre>"; }
		else
		{	$query = "delete from smoad_sdwan_servers where id=$_server_id"; 
			$db->query($query);
			
			if($G_sds_serialnumber==$_server_serialnumber) 
			{ $_SESSION['ztp_sds_sn'] = $_SESSION['ztp_sds_id'] = 'notset';
				$db->query("update profile set ztp_sds_serialnumber=\"notset\" where id=1");
				$db->query("update profile set ztp_sds_id=\"notset\" where id=1");
				$db->query("update profile set ztp_sds_details=\"notset\" where id=1");  
			}
			
			//delete any stray sdwan-server jobs !
			sm_ztp_sds_del_jobs($db, $_server_serialnumber);
			
			//print "Query: $query <br>\n";
			print "<pre>SUCCESS: Server removed successfully !</pre>";
		}
	}
	else { $_invalid_inputs=true; }
}
else if($_POST['command']=="config_sds") 
{	$_sds_id = $_POST['sds_id']; 
	$_sds_serialnumber = $_POST['sds_serialnumber'];
	$_sds_details = $_POST['sds_details'];
	
	if(strlen($_sds_details)>=12) { $_sds_details = substr("$_sds_details", 0, 12)." ..."; }
	
	$_SESSION['ztp_sds_sn'] = $_sds_serialnumber;
	$_SESSION['ztp_sds_id'] = $_sds_id;
	$_SESSION['ztp_sds_details'] = $_sds_details;
	//$db->query("update profile set ztp_sds_serialnumber=\"$_sds_serialnumber\" where id=1");
	//$db->query("update profile set ztp_sds_id=\"$_sds_id\" where id=1");
	//$db->query("update profile set ztp_sds_details=\"$_sds_details\" where id=1");
	$page_redirect=true;
	print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=index.php?page=ztp_sds_home&skey=$session_key\">";
}

//if($_invalid_inputs) { print "<pre>ERROR: Invalid input(s).</pre>"; } 


if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
{
	print '<p><strong>Add Server:</strong></p>
	<p>';
	api_form_post($curr_page);
	api_input_hidden("command", "add_server"); 
	
	print '<table class="list_items2" style="width:1024px;font-size:10px;">';
	api_ui_config_option_text("Details", null, "details", null, null);
	api_ui_config_option_text("License", "default", "license", null, null);
	//api_ui_config_option_text("Serial Number", null, "serialnumber", null, null);
	api_ui_config_option_text("IP Addr or DNS", null, "ipaddr", null, null);
	api_ui_config_option_text("Area", null, "area", null, null);
	
	
	print '<tr><td>Type</td><td>
	<select name="type">
	<option value="l2" >L2 SD-WAN</option>
	<option value="l3" >L3 SD-WAN</option>
	<option value="mptcp" >MPTCP</option>
	</select></td></tr>';
	 
	api_ui_config_option_add(null);
	print '</table>
	<br>
	</form></p>
	<hr id="hr_style">
	<p><strong>SD-WAN Servers:</strong></p>';
}

?>


<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Details</th><th>License</th><th>Serial Number</th><th>IP Addr (or DNS)</th><th>Type</th><th>Area</th>
<th>Assigned Devices</th><th></th><th></th><th></th></tr>

<?php


if($page_redirect==false)
{	$query = "select id, details, license, serialnumber, ipaddr, area, type, sdwan_proto, status, enable, updated, job_read_timestamp 
					from smoad_sdwan_servers order by id desc  "; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$ipaddr = $row['ipaddr'];
			$area = $row['area'];
			$type = $row['type'];
			$sdwan_proto = $row['sdwan_proto'];
			$status = $row['status'];
			$enable = $row['enable'];
			$updated = $row['updated'];
			$job_read_timestamp = $row['job_read_timestamp'];
			
			
			/*$query2 = "select count(*) qty from smoad_devices where sdwan_server_ipaddr='$ipaddr'"; 
			if($res2 = $db->query($query2)) { while($row2 = $res2->fetch_assoc()) { $qty = $row2['qty']; } }*/
			$qty = _server_in_use_device_count($db, $ipaddr);
		
			if($type=="l2") { $type="L2 SD-WAN"; }
			else if($type=="l3") { $type="L3 SD-WAN"; }
			else if($type=="mptcp") { $type="MPTCP"; }
			
			print "<tr><td>$id</td>";
			//print "<td>$details</td>";
			
			print "<td><a href=\"https://$ipaddr\" target=\"_blank\" class=\"a_button_green\" title=\"Access: $details - $ipaddr\" />&#x2197;</a> $details</td>";
			
			print "<td>$license</td><td>$serialnumber</td><td>$ipaddr</td>
					 <td>$type</td><td>$area</td><td>$qty</td>";
				
			if($status=='up') { $status=1; } else { $status=0; }
			$status = api_ui_up_down_display_status($status, null);
			print "<td>$status</td>";
			
			print "<td>";
			api_form_post($curr_page);
			api_input_hidden("command", "config_sds");
			api_input_hidden("sds_id", $id);
			api_input_hidden("sds_serialnumber", $serialnumber);
			api_input_hidden("sds_details", $details);
			print "<input type=\"image\" src=\"i/gear.png\" alt=\"Configure\" title=\"Configure\" class=\"top_title_icons\" />";
			print "</form></td>";
			
			if($qty==0 && ($login_type=='root' || $login_type=='admin' || $login_type=='customer'))
			{	print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "del_server");
				api_input_hidden("server_ipaddr", $ipaddr);
				api_input_hidden("server_serialnumber", $serialnumber);
				api_input_hidden("server_id", $id);
				print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />";
				print "</form></td>";
			}
			else 
			{	print "<td></td>"; }
		
			print "</tr>";
		}
	}
}
?>
</table>
