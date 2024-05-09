<?php
include("c/c_api_remote_sdwan_server.php");

$_search_serialnumber = $_POST['search_serialnumber'];
$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

$page_redirect=false;

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


if($_POST['command']=="add_device") 
{	$_details = $_POST['details']; $_license = $_POST['license']; 
	//$_serialnumber = $_POST['serialnumber']; 
	$_model_and_variant = $_POST['model_and_variant']; $_os = $_POST['os']; $_area = $_POST['area']; $_serialnumber = randHash(16);
	$_model = ""; $_model_variant = "";
	$_details = api_prevent_sql_injection($db, $_details);
	$_license = api_prevent_sql_injection($db, $_license);
	$_area = api_prevent_sql_injection($db, $_area);

	if($_model_and_variant=='spider_l2') { $_model = "spider"; $_model_variant = "l2"; }
	else if($_model_and_variant=='spider_l2w1l2') { $_model = "spider"; $_model_variant = "l2w1l2"; }
	else if($_model_and_variant=='spider_l3') { $_model = "spider"; $_model_variant = "l3"; }
	else if($_model_and_variant=='spider_mptcp') { $_model = "spider"; $_model_variant = "mptcp"; }
	else if($_model_and_variant=='spider2_l2') { $_model = "spider2"; $_model_variant = "l2"; }
	else if($_model_and_variant=='spider2_l3') { $_model = "spider2"; $_model_variant = "l3"; }
	else if($_model_and_variant=='beetle_l2') { $_model = "beetle"; $_model_variant = "l2"; }
	else if($_model_and_variant=='beetle_l3') { $_model = "beetle"; $_model_variant = "l3"; }
	else if($_model_and_variant=='bumblebee_l2') { $_model = "bumblebee"; $_model_variant = "l2"; }
	else if($_model_and_variant=='bumblebee_l3') { $_model = "bumblebee"; $_model_variant = "l3"; }
	else if($_model_and_variant=='wasp1_l2') { $_model = "wasp1"; $_model_variant = "l2"; }
	else if($_model_and_variant=='wasp2_l2') { $_model = "wasp2"; $_model_variant = "l2"; }
	else if($_model_and_variant=='vm_l2') { $_model = "vm"; $_model_variant = "l2"; }
	else if($_model_and_variant=='vm_l3') { $_model = "vm"; $_model_variant = "l3"; }
	else if($_model_and_variant=='vm_mptcp') { $_model = "vm"; $_model_variant = "mptcp"; }
	else if($_model_and_variant=='soft_client') { $_model = "soft_client"; $_model_variant = "soft_client"; }
	
	if($_details!="" && $_license!="" && $_serialnumber!="" && $_area!="" && $_model!="" && $_model_variant!="" )
	{	$_create=1;
		$query = "select serialnumber from smoad_devices where serialnumber=\"$_serialnumber\""; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	print "<pre>ERROR: Found already matching device Serial Number. So not creating this new device !</pre>"; $_create=0;
			}
		}
		
		if($_create) 
		{	api_generate_device_api_keys($api_prikey, $api_pubkey);
			api_generate_device_api_keys($api_device_prikey, $api_device_pubkey);
			
			$root_password = random_bytes(6); 
			$root_password = bin2hex($root_password);
			
			$superadmin_password = random_bytes(6); 
			$superadmin_password = bin2hex($superadmin_password);
			
			$query = "insert into smoad_devices 
					(details, license, serialnumber, model, model_variant, os, root_password, superadmin_password, area, 
					   sdwan_server_ipaddr, vlan_id,
						api_prikey, api_pubkey, api_device_prikey, api_device_pubkey, 
						api_prikey_new, api_pubkey_new, api_device_prikey_new, api_device_pubkey_new) 
					values (\"$_details\", \"$_license\", \"$_serialnumber\", \"$_model\", 
					\"$_model_variant\", \"$_os\", \"$root_password\", \"$superadmin_password\", \"$_area\", \"notset\", 0,  
					\"$api_prikey\", \"$api_pubkey\", \"$api_device_prikey\", \"$api_device_pubkey\", 
					\"$api_prikey\", \"$api_pubkey\", \"$api_device_prikey\", \"$api_device_pubkey\" )";
			$db->query($query);
			
			$query = "insert into smoad_device_network_cfg (device_serialnumber) 
					values (\"$_serialnumber\")";
			$db->query($query);
			
			print "<pre>SUCCESS: Device $_serialnumber added successfully !</pre>";
			/*print "<pre>NOTE: will be updated in the SDWAN Server shortly !</pre>";*/
		}
	}
	else 
	{	print "<pre>ERROR: Invalid input(s).</pre>"; 
	}
}
else if($_POST['command']=="del_device") 
{	$_device_id = $_POST['device_id']; 
	$_device_serialnumber = $_POST['device_serialnumber'];
	$_device_sdwan_server_ipaddr = $_POST['device_sdwan_server_ipaddr'];
	
	
	if($_device_id>0)
	{	$query = "delete from smoad_devices where id=$_device_id"; 
		$db->query($query);
			
		$query = "delete from smoad_device_network_cfg where device_serialnumber =\"$_device_serialnumber\"";
		$db->query($query);
			
		if($G_device_serialnumber==$_device_serialnumber) 
		{ $_SESSION['ztp_dev_sn'] = $_SESSION['ztp_dev_id'] = 'notset';
			$db->query("update profile set ztp_device_serialnumber=\"notset\" where id=1");
			$db->query("update profile set ztp_device_id=\"notset\" where id=1");
			$db->query("update profile set ztp_device_details=\"notset\" where id=1");  
		}
		
		//delete any stray device jobs !
		sm_ztp_del_jobs($db, $_device_serialnumber);
			
		print "<pre>SUCCESS: Device $_device_serialnumber deleted successfully !</pre>";
		/*print "<pre>NOTE: will be updated in the SDWAN Server shortly !</pre>";*/
	}
}
else if($_POST['command']=="config_device") 
{	$_device_id = $_POST['device_id']; 
	$_device_serialnumber = $_POST['device_serialnumber'];
	$_device_details = $_POST['device_details'];
	$_device_model = $_POST['device_model'];
	$_device_model_variant = $_POST['device_model_variant'];
	$_device_os = $_POST['device_os'];
	
	if(strlen($_device_details)>=12) { $_device_details = substr("$_device_details", 0, 10)." ..."; }
	
	$_SESSION['ztp_dev_sn'] = $_device_serialnumber;
	$_SESSION['ztp_dev_id'] = $_device_id;
	$_SESSION['ztp_dev_details'] = $_device_details;
	$_SESSION['ztp_dev_model'] = $_device_model;
	$_SESSION['ztp_dev_model_variant'] = $_device_model_variant;
	$_SESSION['ztp_dev_os'] = $_device_os;
	
	//$db->query("update profile set ztp_device_serialnumber=\"$_device_serialnumber\" where id=1");
	//$db->query("update profile set ztp_device_id=\"$_device_id\" where id=1");
	//$db->query("update profile set ztp_device_details=\"$_device_details\" where id=1");
	
	$page_redirect=true;
	print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=index.php?page=ztp_dev_home&skey=$session_key\">";
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


if($login_type=='root' || $login_type=='admin')
{	print '<p><strong>Add Device:</strong></p>';
	print '<p>';
	api_form_post($curr_page);
	api_input_hidden("command", "add_device");
	print '<table class="list_items2" style="width:1024px;font-size:10px;">';
	api_ui_config_option_text("Details", null, "details", null, null);
	api_ui_config_option_text("License", "default", "license", null, null);
	
	print '
	<tr><td>Model & Variant</td><td>
	<select name="model_and_variant">
	<option value="spider_l2" >SMOAD Spider - L2 SD-WAN</option>
	<option value="spider_l2w1l2" >SMOAD Spider - L2 SD-WAN (L2W1L2)</option>
	<option value="spider_l3" >SMOAD Spider - L3 SD-WAN</option>
	<option value="spider_mptcp" >SMOAD Spider - MPTCP</option>
	<option value="spider2_l2" >SMOAD Spider2 (Supermicro) - L2 SD-WAN</option>
	<option value="spider2_l3" >SMOAD Spider2 (Supermicro) - L3 SD-WAN</option>
	<option value="beetle_l2" >SMOAD Beetle - L2 SD-WAN</option>
	<option value="beetle_l3" >SMOAD Beetle - L3 SD-WAN</option>
	<option value="bumblebee_l2" >SMOAD BumbleBee - L2 SD-WAN</option>
	<option value="bumblebee_l3" >SMOAD BumbleBee - L3 SD-WAN</option>
	<option value="wasp1_l2" >SMOAD Wasp1 - L2 SD-WAN</option>
	<option value="wasp2_l2" >SMOAD Wasp2 - L2 SD-WAN</option>
	<option value="vm_l2" >SMOAD VM - L2 SD-WAN</option>
	<option value="vm_l3" >SMOAD VM - L3 SD-WAN</option>
	<option value="vm_mptcp" >SMOAD VM - MPTCP</option>
	<option value="soft_client" >SMOAD Soft-client</option>
	</select></td></tr>';
	
	print '
	<tr><td>OS</td><td>
	<select name="os">
	<option value="openwrt" selected >OpenWRT</option>
	<option value="ubuntu" >Ubuntu</option>
	</select></td></tr>';
	
	api_ui_config_option_text("Area", null, "area", null, null);
	api_ui_config_option_add(null);
	print '</table>';
	print '</form></p>';
	print '<hr id="hr_style">';
}



	print '<p><strong>Search Device:</strong></p>';
	print '<p>';
	api_form_post($curr_page);
	print '<table class="list_items2" style="width:1024px;font-size:10px;">';
	api_ui_config_option_text("Serial Number", null, "search_serialnumber", null, null);
	api_ui_config_option_search(null);
	print '</table>';
	print '</form></p>';
	print '<hr id="hr_style">';


	$where_clause=$where_clause_customer=$where_clause_search_serialnumber=null;
	if($login_type=='customer') { $where_clause_customer=" customer_id = $id_customer "; }
	if($_search_serialnumber!=null) { $where_clause_search_serialnumber=" serialnumber = \"$_search_serialnumber\" "; }
	
	if($where_clause_customer!=null) 
	{ if($where_clause==null) { $where_clause=" where "; } //first where
	  $where_clause .= $where_clause_customer;
	}
	
	if($where_clause_search_serialnumber!=null) 
	{  if($where_clause==null) { $where_clause="where "; } else { $where_clause.=" and "; } //following/first where
		$where_clause .= $where_clause_search_serialnumber; 
	}

	$total_items=0; $total_pages=0;
	api_ui_pagination_get_total_items_total_pages($db, 'smoad_devices', $where_clause, $G_items_per_page, $total_items, $total_pages);
	
	print "<p><strong>SMOAD Devices: $total_items</strong></p>";
	api_ui_pagination_get_pagination_table($db, $_page, $total_pages, "index.php?page=devices&skey=".$session_key);
	$limitstart = ($_page-1)*$G_items_per_page;
?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Details</th><th>License</th><th>Serial Number</th><th>Model - Variant</th><th>OS</th><th>Area</th><th>Gateway</th><th></th><th></th><th></th></tr>

<?php
if($page_redirect==false)
{	$query = "select id, details, license, serialnumber, model, model_variant, os, area, 
				sdwan_server_ipaddr, vlan_id, enable, updated, status, job_read_timestamp 
				from smoad_devices $where_clause order by id desc limit $limitstart".",$G_items_per_page "; 

	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$model = $row['model'];
			$model_variant = $row['model_variant'];
			$os = $row['os'];
			$area = $row['area'];
			$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
			$vlan_id = $row['vlan_id'];
			$enable = $row['enable'];
			$updated = $row['updated'];
			$status = $row['status'];
			$job_read_timestamp = $row['job_read_timestamp'];
		
			if($model=="spider") { $_model="Spider"; }
			else if($model=="spider2") { $_model="Spider2"; }
			else if($model=="beetle") { $_model="Beetle"; }
			else if($model=="bumblebee") { $_model="BumbleBee"; }
			else if($model=="wasp1") { $_model="Wasp1"; }
			else if($model=="wasp2") { $_model="Wasp2"; }
			else if($model=="vm") { $_model="VM"; }
			else if($model=="soft_client") { $_model="Soft-client"; }
		
			//if($status=='up') { $status="led-green"; } else { $status="led-red"; }
			if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
			else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
			else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
			else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
			
			if($os=="openwrt") { $_os="OpenWRT"; }
			else if($os=="ubuntu") { $_os="Ubuntu"; }
			print "<tr><td>$id</td><td>$details</td>
					 <td>$license</td><td>$serialnumber</td>
					 <td>$_model - $_model_variant</td><td>$_os</td>
					 <td>$area</td>";
					 
			if($sdwan_server_ipaddr!=null && $sdwan_server_ipaddr!="notset")
			{
				$query2 = "select id, details, license, serialnumber, ipaddr, area, type, sdwan_proto, status, enable, updated, job_read_timestamp from smoad_sdwan_servers
								where ipaddr=\"$sdwan_server_ipaddr\""; 
				if($res2 = $db->query($query2))
				{	while($row2 = $res2->fetch_assoc())
					{	$gw_server_id = $row2['id'];
						$gw_server_details = $row2['details'];
						$gw_server_serialnumber = $row2['serialnumber'];
						$gw_server_ipaddr = $row2['ipaddr'];
						$gw_server_area = $row2['area'];
						$gw_server_type = $row2['type'];
						
						print "<td> ";
						api_form_post($curr_page);
						api_input_hidden("command", "config_sds");
						api_input_hidden("sds_id", $gw_server_id);
						api_input_hidden("sds_serialnumber", $gw_server_serialnumber);
						api_input_hidden("sds_details", $gw_server_details);
						//print "<input type=\"submit\" name=\"submit_ok\" value=\"&#x2699; Configure\" style=\"border:0;\" class=\"a_button_blue\" />
						print "<input type=\"image\" src=\"i/gear.png\" alt=\"Configure\" title=\"Configure\" class=\"top_title_icons\" />";
						print " $gw_server_details";
						print "</form></td>";
					}
				}
				else { print "<td></td>"; }
			}
			else 
			{	print "<td></td>"; }
					 
			if($status=='up') { $status=1; } else { $status=0; }
			$status = api_ui_up_down_display_status($status, null);
			print "<td>$status</td>";
			
			if($model=="soft_client")
			{ print "<td></td>"; }
			else
			{	print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "config_device");
				api_input_hidden("device_id", $id);
				api_input_hidden("device_serialnumber", $serialnumber);
				api_input_hidden("device_details", $details);
				api_input_hidden("device_model", $model);
				api_input_hidden("device_model_variant", $model_variant);
				api_input_hidden("device_os", $os);
				//print "<input type=\"submit\" name=\"submit_ok\" value=\"&#x2699; Configure\" style=\"border:0;\" class=\"a_button_blue\" />";
				print "<input type=\"image\" src=\"i/gear.png\" alt=\"Configure\" title=\"Configure\" class=\"top_title_icons\" />";
				print "</form></td>";
			}
			
			
			if($sdwan_server_ipaddr=='notset' && ($login_type=='root' || $login_type=='admin'))
			{	print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "del_device");
				api_input_hidden("device_id", $id);
				api_input_hidden("device_sdwan_server_ipaddr", $sdwan_server_ipaddr);
				api_input_hidden("device_serialnumber", $serialnumber);
				//api_button_post("&#x292B; Delete", "Delete device: $details - $serialnumber ?", "red");
				print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />";
				print "</form></td>";
			}
			else { print "<td></td>"; }
		
			print "</tr>";
		}
	}
}
?>
</table>
<br><br>
