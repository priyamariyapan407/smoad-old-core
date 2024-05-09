<?php
$main_table = "smoad_customers";

db_api_set_value($db, $_POST['name'], "name", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['custname'], "custname", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['password'], "password", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['addr1'], "addr1", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['addr2'], "addr2", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['area'], "area", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['email1'], "email1", $main_table, $G_cust_id, "char");
//db_api_set_value($db, $_POST['engagement_date'], "engagement_date", $main_table, $G_cust_id, "char");
db_api_set_value($db, $_POST['id_user_access'], "id_user_access", $main_table, $G_cust_id, "num");


//Front end
	$query = "select id, name, custname, password, email1, addr1, addr2, area, id_user_access 
		from $main_table where id=$G_cust_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$name = $row['name'];
			$custname = $row['custname'];
			$password = $row['password'];
			$email1 = $row['email1'];
			$addr1 = $row['addr1'];
			$addr2 = $row['addr2'];
			$area = $row['area'];
			$id_user_access = $row['id_user_access'];
			
			api_form_post($curr_page);
         api_input_hidden("user_id", "$user_id");
			print '<table class="list_items" style="width:1024px;font-size:10px;">';
			api_ui_config_option_readonly("ID", $id, null);
			api_ui_config_option_text("Name", $name, "name", null, null);
			api_ui_config_option_text("CustomerName", $custname, "custname", null, null);
			api_ui_config_option_text("Password", $password, "password", null, null);
			api_ui_config_option_text("Email-1", $email1, "email1", null, null);
			api_ui_config_option_text("Address-1", $addr1, "addr1", null, null);
			api_ui_config_option_text("Address-2", $addr2, "addr2", null, null);
			api_ui_config_option_text("Area", $area, "area", null, null);
			
			print "<tr><td>Access Level</td><td>";
			print "<select name=\"id_user_access\">";
			$query2 = "select id, details from smoad_user_access"; 
			if($res2 = $db->query($query2))
			{	while($row2 = $res2->fetch_assoc())
				{	$access_id = $row2['id'];
					$access_details = $row2['details'];
					$selected = "";
					if($access_id==$id_user_access) { $selected="selected"; }
					print "<option value=\"$access_id\" $selected>$access_details</option>";
				}
			}

			print "</select>";
			print "</td></tr>";
			
			if($login_type=='root' || $login_type=='admin') { api_ui_config_option_update(null); }
			print '</table></form><br>';
		}
	}

?>


