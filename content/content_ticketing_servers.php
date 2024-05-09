<?php


if($_POST['command']=="add_server") 
{	$_details = $_POST['details']; $_license = $_POST['license']; $_serialnumber = $_POST['serialnumber']; $_type = $_POST['type']; 
	$_ipaddr = $_POST['ipaddr']; $_area = $_POST['area']; $_api_key = $_POST['api_key'];
	if($_details!="" && $_license!="" && $_serialnumber!="" && $_type!="" && $_ipaddr!="" && $_area!="" && $_api_key!="")
	{	$_create=1;
		$query = "select serialnumber from smoad_ticketing_servers where serialnumber=\"$_serialnumber\""; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	print "<pre>ERROR: Found already matching device Serial Number. So not creating this new device !</pre>"; $_create=0;
			}
		}
		
		if($_create) 
		{	$query = "insert into smoad_ticketing_servers (details, license, serialnumber, type, ipaddr, area, api_key) 
					values (\"$_details\", \"$_license\", \"$_serialnumber\", \"$_type\", \"$_ipaddr\", \"$_area\", \"$_api_key\" )";
			$db->query($query);
			//print "Query: $query <br>\n";
			print "<pre>SUCCESS: Server added successfully !</pre>";
		}
	}
}
else if($_POST['command']=="del_server") 
{	$_server_id = $_POST['server_id']; 
	
	if($_server_id>0)
	{	$query = "delete from smoad_ticketing_servers where id=$_server_id"; 
		$db->query($query);
		//print "Query: $query <br>\n";
		print "<pre>SUCCESS: Server removed successfully !</pre>";
	}
}

?>

<p><strong>Add a Ticket Server:</strong></p>
<p>
<?php print "<form method=\"POST\" action=\"$curr_page\" >"; ?>
<input type="hidden" name="command" value="add_server" />
<table class="list_items2" style="width:1024px;font-size:10px;">
<tr><td>Details</td><td><input class="text_style" style="width:300px;"  type="text" name="details" value="" /></td></tr>
<tr><td>License</td><td><input class="text_style" style="width:120px;"  type="text" name="license" value="" /></td></tr>
<tr><td>Serial Number</td><td><input class="text_style" style="width:120px;"  type="text" name="serialnumber" value="" /></td></tr>
<tr><td>Type</td><td><input class="text_style" style="width:120px;"  type="text" name="type" value="osticket" readonly="readonly" /></td></tr>
<tr><td>IP Addr or DNS</td><td><input class="text_style" style="width:120px;"  type="text" name="ipaddr" value="" /></td></tr>
<tr><td>API Key</td><td><input class="text_style" style="width:400px;"  type="text" name="api_key" value="" /></td></tr>
<tr><td>Area</td><td><input class="text_style" style="width:400px;"  type="text" name="area" value="" /></td></tr>
<tr><td></td><td><?php api_button_post("Add", "Add new server ?", "red"); ?></td></tr>
</table>
<br>
</form></p>

<hr id="hr_style">
<p><strong>Ticketing Servers:</strong></p>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Details</th><th>License</th><th>Serial Number</th><th>Type</th><th>IP Addr (or DNS)</th><th>API Key</th>
<th>Area</th><!--<th>Status</th>
<th>Enable</th>-->
<th></th><th></th></tr>

<?php


$query = "select id, details, license, serialnumber, type, ipaddr, api_key, area, status, enable, updated from smoad_ticketing_servers"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$type = $row['type'];
			$ipaddr = $row['ipaddr'];
			$api_key = $row['api_key'];
			$area = $row['area'];
			$status = $row['status'];
			$enable = $row['enable'];
			$updated = $row['updated'];
		
			/*
			$bg_style="style=\"background-color:#D6FA89;\"";
			
			if($enable=="TRUE") { $bg_style="style=\"background-color:#D6FA89;\""; }
			else { $bg_style="style=\"background-color:#FF707D;\""; }
			
			if($status=="UP") { $bg_style="style=\"background-color:#D6FA89;\""; }
			else if($status=="DOWN") { $bg_style="style=\"background-color:#F77575;\""; }
			else if($status=="UP_WAITING") { $bg_style="style=\"background-color:#ebcc34;\""; }*/
			

			print "<tr ><td $bg_style >$id</td><td $bg_style>$details</td>
					 <td $bg_style>$license</td><td $bg_style>$serialnumber</td>
					 <td $bg_style>$type</td><td $bg_style>$ipaddr</td><td $bg_style>$api_key</td>
					 <td $bg_style>$area</td>";
			       
			/*
			if($status=="UP") { print "<td $bg_style style=\"color:green;\">&#11044; - $status</td>"; }
			else if($status=="DOWN") { print "<td $bg_style style=\"color:red;\">&#11044; - $status</td>"; }
			else if($status=="UP_WAITING") { print "<td $bg_style style=\"color:gray;\">&#11044; - $status</td>"; }
			
			if($enable=="TRUE") { print "<td style=\"background-color:#D6FA89;\">enabled</td>"; }
			else { print "<td $bg_style >disabled</td>"; }
			*/
			
			print "<td $bg_style><form method=\"POST\" action=\"index.php?page=ticketing_server_details&skey=$session_key\" >
					<input type=\"hidden\" name=\"server_id\" value=\"$id\" />
					<input type=\"submit\" name=\"submit_ok\" value=\"Details\" style=\"border:0;\" class=\"a_button_blue\" />
					</form></td>";
			
			print "<td $bg_style><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_server\" />
					<input type=\"hidden\" name=\"server_ipaddr\" value=\"$ipaddr\" />
					<input type=\"hidden\" name=\"server_id\" value=\"$id\" />";
					api_button_post("Delete", "Delete ticketing server: $details - $serialnumber ?", "red");
			print "</form></td>";
			
			print "</tr>";
			
		}
	}
?>
</table>


<!--<em>* page will auto refresh every 60 seconds !</em>
<meta HTTP-EQUIV="REFRESH" content="60; url=index.php?page=tunnels">-->




