<?php

if($_POST['command']=="del_log") 
{	$_log_id = $_POST['log_id']; 
	if($_log_id>0)
	{	$query = "delete from smoad_user_device_access_log where id=$_log_id"; 
		$db->query($query);
	}
}

$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

$_date = $_GET['date'];
$date = explode('-', $_date);

if($_date!=null) { $where_clause_date=" year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1] "; }

if($where_clause_date!=null) 
{  if($where_clause==null) { $where_clause=" where "; } //first where
	$where_clause .= $where_clause_date; 
}

$total_items=0; $total_pages=0;
api_ui_pagination_get_total_items_total_pages($db, 'smoad_user_device_access_log', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, $curr_page);
$limitstart = ($_page-1)*$G_items_per_page;

?>


<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Username</th><th>Auth Status</th><th>Access Type</th><th>Timestamp</th><th></th></tr>

<?php

$query = "select id, access_type, username, device_serialnumber, auth_status, access_timestamp, id_rand_key from 
	smoad_user_device_access_log where device_serialnumber=\"$G_device_serialnumber\"
	and year(access_timestamp) = $date[0] and month(access_timestamp) = $date[1] 
	order by id desc limit $limitstart".",$G_items_per_page"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$access_type = $row['access_type'];
			$username = $row['username'];
			$auth_status = $row['auth_status'];
			$timestamp = $row['access_timestamp'];
			$id_rand_key = $row['id_rand_key'];
			
			print "<tr><td>$id</td><td>$username</td>";
			print "<td>$auth_status</td><td>$access_type</td><td>$timestamp</td>";

			if($login_type=='root')
			{		print "<td><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_log\" />
					<input type=\"hidden\" name=\"log_id\" value=\"$id\" />";
					print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />";
					print "</form></td>";
			}
			else { print "<td></td>"; }
		
			print "</tr>";
		}
	}
?>
</table>


