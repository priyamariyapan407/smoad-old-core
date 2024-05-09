<?php

if($_POST['command']=="add_user") 
{	$_name = $_POST['name']; $_username = $_POST['username']; $_password = $_POST['password']; $_area = $_POST['area']; $_access_level = $_POST['access_level'];
	if($_name!="" && $_username!="" && $_password!="" && $_area!="" && $_access_level!="")
	{	
		$_create=1;
		$query = "select username from smoad_users"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$__username = $row['username'];
				if($_username==$__username) { print "<pre><b>Error:</b> Found already matching username. So not creating this new user!</pre>"; $_create=0; }
			}
		}
		
		if($_create) 
		{	$query = "insert into smoad_users (name, username, password, area, access_level) 
					values (\"$_name\", \"$_username\", \"$_password\", \"$_area\", \"$_access_level\" )";
			$db->query($query);
			//print "Query: $query <br>\n";
		}
	}
}
else if($_POST['command']=="del_user") 
{	$_user_id = $_POST['user_id']; 
	if($_user_id>0)
	{	$query = "delete from smoad_users where id=$_user_id"; 
		$db->query($query);
		//print "Query: $query <br>\n";
	}
}

?>


<p><strong>Add User:</strong></p>
<p>
<?php 
api_form_post($curr_page);
api_input_hidden("command", "add_user");
?>
<table class="list_items2" style="width:1024px;font-size:10px;">
<?php  
  api_ui_config_option_text(Name, null, name, null, null);
  api_ui_config_option_text(Username, null, username, null, null);
  api_ui_config_option_text(Password, null, password, null, null);
  api_ui_config_option_text(Area, null, area, null, null);
?>
<tr><td>Access</td><td>
<select name="access_level">
<option value="limited">LIMITED</option>
<option value="admin">ADMIN</option>
<option value="block">BLOCK</option>
</select>
</tr>
<tr><td></td><td><?php api_button_post("Add", "Create new user ?", "red"); ?></td></tr>
</table>
<br>
</form></p>

<hr id="hr_style">
<p><strong>Users:</strong></p>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Name</th><th>Username</th><th>Area</th><th>Access</th><th></th><th></th><th></th></tr>

<?php

	$query = "select id, name, username, area, access_level from smoad_users order by access_level desc"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$name = $row['name'];
			$username = $row['username'];
			$area = $row['area'];
			$access_level = $row['access_level'];
			
			//$access_level_details = db_api_get_value($db, "details", "smoad_user_access", $id_user_access);	
			
			if($access_level=='admin') { $_access_level='ADMIN'; }
			else if($access_level=='limited') { $_access_level='LIMITED'; }
			else if($access_level=='block') { $_access_level='BLOCK'; }
			print "<tr ><td >$id</td><td>$name</td><td>$username</td><td>$area</td><td>$_access_level</td>";
						
			print "<td>"; api_form_post("index.php?page=user_details&skey=$session_key");
			api_input_hidden("user_id", $id);
			print "<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Details\" class=\"top_title_icons\" />";
			print "</form></td>";
	
			print "<td>"; api_form_post("index.php?page=user_device_access_log&skey=$session_key");
			api_input_hidden("username", $username);
			print "<input type=\"image\" src=\"i/access-log.png\" alt=\"Access Log\" title=\"Access Log\" class=\"top_title_icons\" />";
			print "</form></td>";
		
			print "<td>"; api_form_post("$curr_page");
			api_input_hidden("command", "del_user");
			api_input_hidden("user_id", $id);
			print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete user: $username ?\" class=\"top_title_icons\" />";
			print "</form></td>";

			print "</tr>";
		}
	}
?>
</table>

