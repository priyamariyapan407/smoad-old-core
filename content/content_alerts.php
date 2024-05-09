<?php
$type = $_GET['type']; 
$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

//include("c/c_db_access.php");
if($_POST['command']=="del_log") 
{	$_id = $_POST['id']; 
	if($_id>0)
	{	$query = "delete from smoad_alerts where id=$_id"; 
		$db->query($query);		
	}
}
elseif($_POST['command']=="dismiss_log") 
{	$_id = $_POST['id']; 
	if($_id>0)
	{	$query = "update smoad_alerts set status='dismiss' where id=$_id"; 
		$db->query($query);
	}
}
elseif($_POST['command']=="undo_dismiss_log") 
{	$_id = $_POST['id']; 
	if($_id>0)
	{	$query = "update smoad_alerts set status='new' where id=$_id"; 
		$db->query($query);
	}
}

if($_POST['command']!=null) { print "<meta HTTP-EQUIV=\"REFRESH\" content=\"0; url=$curr_page\">"; }

$_date = $_GET['date'];
$date = explode('-', $_date);


if($type!=null) { $where_clause_type = " type=\"$type\" "; }
if($_date!=null) { $where_clause_date=" year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1] "; }

if($where_clause_type!=null) 
{ if($where_clause==null) { $where_clause=" where "; } //first where
  $where_clause .= $where_clause_type;
}
	
if($where_clause_date!=null) 
{  if($where_clause==null) { $where_clause=" where "; } else { $where_clause.=" and "; } //following/first where
	$where_clause .= $where_clause_date; 
}

$total_items=0; $total_pages=0;
api_ui_pagination_get_total_items_total_pages($db, 'smoad_alerts', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, $curr_page);
$limitstart = ($_page-1)*$G_items_per_page;
?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Status</th><th>Title</th><th>Timestamp</th><th></th><th></th><th></th></tr> 

<?php

 
$query = "select id, title, details, status, log_timestamp from smoad_alerts $where_clause order by id desc limit $limitstart".",$G_items_per_page "; 
	if($res = $db->query($query))
	{	
	   while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$title = $row['title'];
			$details = $row['details'];
			$timestamp = $row['log_timestamp'];
			$status = $row['status'];
			
		   //if($status=="dismiss") { $bg_style="style=\"background-color:#acdeaa;font-weight:bold;\""; }
			//else if($status=="new") { $bg_style="style=\"background-color:#f67280;font-weight:bold;\""; }
		   
			print "<tr><td $bg_style>$id</td>";
			if($status=="new") {	print "<td $bg_style>New</td>"; } else {	print "<td $bg_style>Dismissed</td>"; } 
			print "<td $bg_style>$title</td>";
			print "<td $bg_style>$timestamp</td>";

			print "<td $bg_style><form method=\"POST\" action=\"index.php?page=alert_details&skey=$session_key\" >
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Details\" class=\"top_title_icons\" />
					</form></td>";
			
			if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
			{
				if($status=="new")
				{	print "<td $bg_style><form method=\"POST\" action=\"$curr_page\" >
							<input type=\"hidden\" name=\"command\" value=\"dismiss_log\" />
							<input type=\"hidden\" name=\"id\" value=\"$id\" />
							<input type=\"image\" src=\"i/dismiss.png\" alt=\"Dismiss\" title=\"Dismiss\" class=\"top_title_icons\" />
							</form></td>";
				}
				else 
				{	print "<td $bg_style><form method=\"POST\" action=\"$curr_page\" >
							<input type=\"hidden\" name=\"command\" value=\"undo_dismiss_log\" />
							<input type=\"hidden\" name=\"id\" value=\"$id\" />
							<input type=\"image\" src=\"i/undo-dismiss.png\" alt=\"Undo Dismiss\" title=\"Undo Dismiss\" class=\"top_title_icons\" />
							</form></td>";
				}
			}
			else { print "<td></td>"; }

			if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
			{	print "<td $bg_style><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_log\" />
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />
					</form></td>";
			}
			else { print "<td></td>"; }
		
			print "</tr>";
		}
	}



?>
</table>
<br><br>