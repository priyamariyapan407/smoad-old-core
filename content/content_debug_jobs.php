<?php
if($_POST['command']=="del_log") 
{	$_table = $_POST['table']; 
	if($_table!=null)
	{	$query = "delete from $_table where 1=1"; $db->query($query);
		print "<pre>Deleted all jobs from the table: $_table !</pre>";		
	}
}

?>

<table class="config_settings" style="width:660px;">
<?php   

function _get_log_count($db, $table, $details)
{	$login_type = $GLOBALS['login_type'];
	$query = "select count(*) qty from $table";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$qty = $row['qty'];
			print "<tr><td align=right style=\"padding-right:40px;\">$details</td><td>$qty</td>";
			if($login_type=="root")
			{	print "<td $bg_style><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_log\" />
					<input type=\"hidden\" name=\"table\" value=\"$table\" />
					<input type=\"image\" src=\"i/trash.png\" alt=\"Delete Jobs\" title=\"Delete Jobs\" class=\"top_title_icons\" />
					</form></td>";
			}
			else { print "<td></td>"; }
			print "</tr>";
		}
	}
}

_get_log_count($db, "smoad_device_jobs", "Pending ZTP SMOAD Server -> Device Jobs");
_get_log_count($db, "smoad_server_jobs", "Pending ZTP Device/SDWAN-Server -> SMOAD Server Jobs");
_get_log_count($db, "smoad_sdwan_server_jobs", "Pending ZTP: SMOAD Server -> SDWAN Server Jobs");
_get_log_count($db, "smoad_jobs", "Pending Local SMOAD Server Jobs");
_get_log_count($db, "smoad_osticket_jobs", "Pending Local osTicket Jobs");

?>
</table>

