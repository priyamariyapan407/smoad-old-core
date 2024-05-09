<br>
<?php
$_id = $_POST['id'];

if($_POST['command']=="dismiss_log") 
{	if($_id>0)
	{	$query = "update smoad_alerts set status='dismiss' where id=$_id"; 
		$db->query($query);
	}
}
elseif($_POST['command']=="undo_dismiss_log") 
{	if($_id>0)
	{	$query = "update smoad_alerts set status='new' where id=$_id"; 
		$db->query($query);
	}
}


	$query = "select id, title, details, status, log_timestamp from smoad_alerts where id = $_id "; 
	if($res = $db->query($query))
	{	
	   while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$title = $row['title'];
			$details = $row['details'];
			$timestamp = $row['log_timestamp'];
			$status = $row['status'];
			if($status=="dismiss") { $_status="Dismissed"; }
			else if($status=="new") { $_status="New"; }
			
			print '<table class="config_settings" style="width:600px;">';
			api_ui_config_option_readonly("ID", $id);
			api_ui_config_option_readonly("Alert", $title);	
			api_ui_config_option_readonly("Timestamp", $timestamp);
			api_ui_config_option_readonly("Status", $_status);
			api_ui_config_option_text("Details", $details, "details", "access_level_limited", null);
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
			{	if($status=="new")
				{	print "<tr><td $bg_style><form method=\"POST\" action=\"$curr_page\" >
							<input type=\"hidden\" name=\"command\" value=\"dismiss_log\" />
							<input type=\"hidden\" name=\"id\" value=\"$id\" />
							<!--<input type=\"submit\" name=\"submit_ok\" value=\"Dismiss alert !\" style=\"border:0;\" class=\"a_button_blue\" />-->
							<input type=\"image\" src=\"i/dismiss.png\" alt=\"Dismiss\" title=\"Dismiss\" class=\"top_title_icons\" />
							</form></td><td></tr>";
				}
				else 
				{	print "<tr><td $bg_style><form method=\"POST\" action=\"$curr_page\" >
							<input type=\"hidden\" name=\"command\" value=\"undo_dismiss_log\" />
							<input type=\"hidden\" name=\"id\" value=\"$id\" />
							<!--<input type=\"submit\" name=\"submit_ok\" value=\"Undo dismiss alert !\" style=\"border:0;\" class=\"a_button_blue\" />-->
							<input type=\"image\" src=\"i/undo-dismiss.png\" alt=\"Undo Dismiss\" title=\"Undo Dismiss\" class=\"top_title_icons\" />
							</form></td><td></tr>";
				}

				print "<tr><td $bg_style><form method=\"POST\" action=\"index.php?page=alerts&skey=$session_key\" >
					<input type=\"hidden\" name=\"command\" value=\"del_log\" />
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<!--<input type=\"submit\" name=\"submit_ok\" value=\"Delete\" style=\"border:0;\" class=\"a_button_red\" />-->
					<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />
					</form></td><td></tr>";
			}
			
			print '</table><br>';
		}
	}
			

?>

