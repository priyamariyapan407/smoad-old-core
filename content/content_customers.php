<?php

function _customer_in_use_device_count($db, $id)
{	$query = "SELECT count(*) device_count FROM smoad_devices WHERE customer_id=\"$id\"";
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { return $row['device_count']; } }
	return 0;
}


if($_POST['command']=="add_customer") 
{	$_name = $_POST['name']; $_custname = $_POST['custname']; $_password = $_POST['password']; 
   $_addr1 = $_POST['addr1']; $_addr2 = $_POST['addr2']; $_area = $_POST['area']; $_id_user_access = $_POST['id_user_access']; 
	if($_name!="" && $_custname!="")
	{	$_create=1;
		$query = "select custname from smoad_customers"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$__custname = $row['custname'];
				if($_custname==$__custname) { print "<pre><b>Error:</b> Found already matching customer name. So not creating this new customer!</pre>"; $_create=0; }
			}
		}
		
		if($_create) 
		{	$query = "insert into smoad_customers (name, custname, password, addr1, addr2, area) 
					values (\"$_name\", \"$_custname\", \"$_password\", \"$_addr1\", \"$_addr2\", \"$_area\" )";
			$db->query($query);
		}
	}
}
else if($_POST['command']=="del_customer")
{	
	$_cust_id = $_POST['cust_id']; 
	$_cust_name = $_POST['cust_name'];
	if($_cust_id>0)
	{	if(_customer_in_use_device_count($db, $_cust_id)>0)
		{ print "<pre>ERROR: Found one or more devices belongs to this customer. So not deleting the same. Remove the associated devices and retry !</pre>"; }
		else
		{	$query = "delete from smoad_customers where id=$_cust_id"; 
			$db->query($query);
			
			if($G_cust_id==$_cust_id) 
			{ $_SESSION['ztp_cust_id'] = $_SESSION['ztp_cust_name'] = 'notset';
			  $db->query("update profile set ztp_cust_id=\"notset\" where id=1");
			  $db->query("update profile set ztp_cust_name=\"notset\" where id=1");  
			}
			
			//print "Query: $query <br>\n";
			print "<pre>SUCCESS: Customer removed successfully !</pre>";
		}
	}
	else { $_invalid_inputs=true; }
}
else if($_POST['command']=="config_cust") 
{	$_cust_id = $_POST['cust_id']; 
	$_cust_name = $_POST['cust_name'];
	
	if(strlen($_sds_name)>=12) { $_sds_name = substr("$_sds_name", 0, 12)." ..."; }
	
	$_SESSION['ztp_cust_id'] = $_cust_id;
	$_SESSION['ztp_cust_name'] = $_cust_name;
	$db->query("update profile set ztp_cust_id=\"$_cust_id\" where id=1");
	$db->query("update profile set ztp_cust_name=\"$_cust_name\" where id=1");
	$page_redirect=true;
	print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=index.php?page=ztp_cust_home&skey=$session_key\">";
}

if($login_type=='root' || $login_type=='admin')
{
	print '<p><strong>Add Customer:</strong></p>
	<p>
	<table class="list_items2" style="width:1024px;font-size:10px;">';
	
	api_form_post($curr_page);
	api_input_hidden("command", "add_customer");
	api_ui_config_option_text("Name", $name, "name", null, null);
	api_ui_config_option_text("CustomerName", $custname, "custname", null, null);
	api_ui_config_option_password("Password", $password, "password", null, null);
	api_ui_config_option_text("Address-1", $addr1, "addr1", null, null);
	api_ui_config_option_text("Address-2", $addr2, "addr2", null, null);
	api_ui_config_option_text("Area", $area, "area", null, null);
	
	api_ui_config_option_add(null);
	print '</table>
	<br>
	</form></p><hr id="hr_style">';
}
?>

<p><strong>Customers List:</strong></p>


<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Name</th><th>CustomerName</th><th>Area</th><th>Access</th><th>Assigned Devices</th><th></th><th></th></tr>

<?php

	$query = "select id, name, custname, area, id_user_access from smoad_customers order by id_user_access desc"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$name = $row['name'];
			$custname = $row['custname'];
			$password = $row['password'];
			$area = $row['area'];
			$id_user_access = $row['id_user_access'];
			
			$access_level_details = db_api_get_value($db, "details", "smoad_user_access", $id_user_access);
			
			$qty = _customer_in_use_device_count($db, $id);

			print "<tr><td >$id</td><td>$name</td><td>$custname</td><td>$area</td><td>$access_level_details</td><td>$qty</td>";
						
			print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "config_cust");
				api_input_hidden("cust_id", $id);
				api_input_hidden("cust_name", $name);
				print "<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Details\" class=\"top_title_icons\" />";
			print "</form></td>";
		
			print "<td>";
			if($login_type=='root' || $login_type=='admin')
			{
				api_form_post($curr_page);
				api_input_hidden("command", "del_customer");
				api_input_hidden("cust_id", $id);
				api_input_hidden("cust_name", $name);
				//api_button_post("Delete", "Delete customer: $custname ?", "red");
				print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete customer: $custname ?\" class=\"top_title_icons\" />";
				print "</form>";
			}
			print "</td>";
		
			print "</tr>";
		}
	}
?>
</table>

