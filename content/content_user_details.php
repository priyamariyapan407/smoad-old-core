<br>
<?php 
$user_id = $_POST['user_id'];
$main_table = "smoad_users";


db_api_set_value($db, $_POST['name'], "name", $main_table, $user_id, "char");
db_api_set_value($db, $_POST['username'], "username", $main_table, $user_id, "char");
db_api_set_value($db, $_POST['password'], "password", $main_table, $user_id, "char");
db_api_set_value($db, $_POST['area'], "area", $main_table, $user_id, "char");
db_api_set_value($db, $_POST['access_level'], "access_level", $main_table, $user_id, "char");


//Front end
	$query = "select id, name, username, password, area, access_level from $main_table where id=$user_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$name = $row['name'];
			$username = $row['username'];
			$password = $row['password'];
			$area = $row['area'];
			$access_level = $row['access_level'];
			
			api_form_post($curr_page);
         api_input_hidden("user_id", $id);
			print '<table class="list_items" style="width:1024px;font-size:10px;">';
			api_ui_config_option_readonly("ID", $id, null);
	      api_ui_config_option_text("Name", $name, "name", null, null);
			api_ui_config_option_text("Username", $username, "username", null, null);
			api_ui_config_option_text("Password", $password, "password", null, null);
			
			print "<tr><td>Access Level</td><td>";
			print "<select name=\"access_level\">";
			$selected=""; if($access_level=='limited') { $selected="selected"; }
			print "<option value=\"limited\" $selected>LIMITED</option>";
			$selected=""; if($access_level=='admin') { $selected="selected"; }
			print "<option value=\"admin\" $selected>ADMIN</option>";
			$selected=""; if($access_level=='block') { $selected="selected"; }
			print "<option value=\"block\" $selected>BLOCK</option>";
			print "</select>";
			print "</td></tr>";
			
			api_ui_config_option_update(null);
			print '</table></form><br>';
		}
	}
?>

