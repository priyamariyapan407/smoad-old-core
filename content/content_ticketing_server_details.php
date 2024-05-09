<br>
<?php 
$server_id = $_POST['server_id'];
$main_table = "smoad_ticketing_servers";


db_api_set_value($db, $_POST['details'], "details", $main_table, $server_id, "char");
db_api_set_value($db, $_POST['license'], "license", $main_table, $server_id, "char");
db_api_set_value($db, $_POST['serialnumber'], "serialnumber", $main_table, $server_id, "char");
db_api_set_value($db, $_POST['ipaddr'], "ipaddr", $main_table, $server_id, "char");
db_api_set_value($db, $_POST['area'], "area", $main_table, $server_id, "char");
db_api_set_value($db, $_POST['api_key'], "api_key", $main_table, $server_id, "char");

//------ Post a ticket ??
if($_POST['generate_test_ticket']=="yes")
{	
	$query = "select ipaddr, api_key from smoad_ticketing_servers where type=\"osticket\" limit 1"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$ipaddr = $row['ipaddr'];
			$api_key = $row['api_key'];
			$api_url = "http://".$ipaddr."/osticket/api/http.php/tickets.json";
		}
	}
	
	//print "<br>$api_key <br> $api_url <br>\n";
	
	$data = array(
    'name'      =>      'SMOAD Server',
    'email'     =>      'noreply@smoadnetworks.com',
    'subject'   =>      "Test Ticket",
    'message'   =>      "This is a test ticket from the SMOAD Server",
    'ip'        =>      $_SERVER['REMOTE_ADDR'],
    'priority'  =>      1, //1, low, 2 normal 3 high, 4 emergency
    'attachments' => array(),
	);
	
	function_exists('curl_version') or die('CURL support required');
	function_exists('json_encode') or die('JSON support required');
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $api_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$api_key));
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result=curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	if($code != 201) { print "<pre>ERROR: Unable to create ticket: \n$result</pre>"; }
	else { print "<pre>SUCCESS: Ticket generated successfully: $result</pre>"; }
}

//Front end

$query = "select id, details, license, serialnumber, ipaddr, area, api_key, status, enable, updated from $main_table where id=$server_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$ipaddr = $row['ipaddr'];
			$area = $row['area'];
			$api_key = $row['api_key'];
			$status = $row['status'];
			$enable = $row['enable'];
			$updated = $row['updated'];
			
					
			if($enable=="TRUE") { $bg_style="style=\"background-color:#D6FA89;\""; }
			else { $bg_style="style=\"background-color:#FF707D;\""; }

			print "<form method=\"POST\" action=\"$curr_page\" >";
			print "<input type=\"hidden\" name=\"server_id\" value=\"$id\" />";
			print '<table class="grid_style" style="width:1024px;font-size:10px;">';
			print "<tr><td style=\"width:250px;\">ID</td><td>$id</td></tr>";
			
			print "<tr><td>Details</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"details\" value=\"$details\" />";
			print "</td></tr>";
			
			print "<tr><td>License</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"license\" value=\"$license\" />";
			print "</td></tr>";
			
			print "<tr><td>Serial Number</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"serialnumber\" value=\"$serialnumber\" />";
			print "</td></tr>";
			
			print "<tr><td>IP Addr or DNS</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"ipaddr\" value=\"$ipaddr\" />";
			print "</td></tr>";
			
			print "<tr><td>API Key</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"api_key\" value=\"$api_key\" />";
			print "</td></tr>";
			
			print "<tr><td>Area</td><td>";
			print "<input class=\"text_style\" style=\"width:300px;\"  type=\"text\" name=\"area\" value=\"$area\" />";
			print "</td></tr>";
			
			/*
			if($status=="UP") { $bg_style_status="style=\"background-color:#D6FA89;\""; }
			else if($status=="DOWN") { $bg_style_status="style=\"background-color:#F77575;\""; }
			else if($status=="UP_WAITING") { $bg_style_status="style=\"background-color:#ebcc34;\""; }
			
			print "<tr><td $bg_style_status>Status</td><td $bg_style_status>$status</td></tr>";
			
			print "<tr><td $bg_style >Enabled</td><td $bg_style >";
			if($enable=="TRUE") $checked = "checked"; else $checked = "";
			print "<input type=\"hidden\" name=\"enable_set\" value=\"1\" />";
			print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"enable\"  id=\"enable\" value=\"1\" $checked />";
			print "</td></tr>";
			*/			
			
			print "<tr><td></td>
				<td><input title=\"Update ?\" type=\"submit\" name=\"submit_ok\" value=\"Update\" style=\"border:0;\" class=\"a_button_red\" onclick=\"return confirm('Are you sure?')\" />
				</td></tr>";
			
			print '</table></form><br>';
		}
	}

	print "<form method=\"POST\" action=\"$curr_page\" >";
	print "<input type=\"hidden\" name=\"generate_test_ticket\" value=\"yes\" />";
	print "<input type=\"hidden\" name=\"server_id\" value=\"$id\" />";
	print '<table class="grid_style" style="width:1024px;font-size:10px;">';
			print "<tr><td style=\"width:250px;\">Test Ticket Server</td><td>
			<input title=\"Generate ?\" type=\"submit\" name=\"submit_ok\" value=\"Generate\" style=\"border:0;\" class=\"a_button_red\" onclick=\"return confirm('Are you sure?')\" />
			</td></tr>";
	print '</table></form><br>';
	
?>

