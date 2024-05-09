<br>
<?php 
$_id = $_POST['id'];
$main_table = "smoad_update_firmware_server";


db_api_set_value($db, $_POST['update_firmware_server_user'], "update_firmware_server_user", $main_table, $_id, "char");
db_api_set_value($db, $_POST['update_firmware_server_ipaddr'], "update_firmware_server_ipaddr", $main_table, $_id, "char");
db_api_set_value($db, $_POST['update_firmware_server_base_path'], "update_firmware_server_base_path", $main_table, $_id, "char");
db_api_set_value($db, $_POST['update_firmware_server_pass'], "update_firmware_server_pass", $main_table, $_id, "char");
db_api_set_value($db, $_POST['update_firmware_release_version'], "update_firmware_release_version", $main_table, $_id, "char");


//Front end

	$query = "select update_firmware_server_user, update_firmware_server_ipaddr, update_firmware_server_base_path, update_firmware_server_pass, 
					update_firmware_release_version from $main_table where id=1"; 

	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$update_firmware_server_user = $row['update_firmware_server_user'];
			$update_firmware_server_ipaddr = $row['update_firmware_server_ipaddr'];
			$update_firmware_server_base_path = $row['update_firmware_server_base_path'];
			$update_firmware_server_pass = $row['update_firmware_server_pass'];
			$update_firmware_release_version = $row['update_firmware_release_version'];
			
			api_form_post($curr_page);
			api_input_hidden("id", "1");
			
			print '<table class="list_items" style="width:1024px;font-size:10px;">';
		   
		   api_ui_config_option_text ("Name", $update_firmware_server_user, "update_firmware_server_user", null, null);
			api_ui_config_option_text ("IP Address", $update_firmware_server_ipaddr, "update_firmware_server_ipaddr", null, null);
			api_ui_config_option_text ("Basepath", $update_firmware_server_base_path, "update_firmware_server_base_path", null, null);
         api_ui_config_option_text ("Password", $update_firmware_server_pass, "update_firmware_server_pass", null, null);			
    		api_ui_config_option_text ("Firmware Version", $update_firmware_release_version, "update_firmware_release_version", null, null);			

			api_ui_config_option_update(null);

			print '</table></form><br>';
			

		}
	}
	
?>

