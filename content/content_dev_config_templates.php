<?php
include("c/c_api_remote_sdwan_server.php");

$_search_serialnumber = $_POST['search_serialnumber'];
$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

$page_redirect=false;


if($_POST['command']=="del_dev_config_template") 
{	$_device_id = $_POST['device_id']; 
	$_details = $_POST['device_details']; 
	
	if($_device_id>0)
	{	$query = "delete from smoad_device_templates where id=$_device_id"; 
		$db->query($query);
		
			
		print "<pre>SUCCESS: Device config template $_details deleted successfully !</pre>";
	}
}


	$where_clause=$where_clause_customer=null;
	if($login_type=='customer') { $where_clause_customer=" customer_id = $id_customer "; }
	if($_search_serialnumber!=null) { $where_clause_search_serialnumber=" serialnumber = \"$_search_serialnumber\" "; }
	
	if($where_clause_customer!=null) 
	{ if($where_clause==null) { $where_clause=" where "; } //first where
	  $where_clause .= $where_clause_customer;
	}
	
	$total_items=0; $total_pages=0;
	api_ui_pagination_get_total_items_total_pages($db, 'smoad_device_templates', $where_clause, $G_items_per_page, $total_items, $total_pages);
	
	print "<p><strong>Config Templates: $total_items</strong></p>";
	api_ui_pagination_get_pagination_table($db, $_page, $total_pages, "index.php?page=dev_config_templates&skey=".$session_key);
	$limitstart = ($_page-1)*$G_items_per_page;
?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Template Details</th><th>Details</th><th>Model - Variant</th><th>Area</th><th></th><th></th></tr>

<?php
if($page_redirect==false)
{	$query = "select id, template_details, details, model, model_variant, area, vlan_id, enable 
				from smoad_device_templates $where_clause order by id desc limit $limitstart".",$G_items_per_page "; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$template_details = $row['template_details'];
			$details = $row['details'];
			$model = $row['model'];
			$model_variant = $row['model_variant'];
			$area = $row['area'];
			$vlan_id = $row['vlan_id'];
			$enable = $row['enable'];

		
			if($model=="spider") { $_model="Spider"; }
			else if($model=="spider2") { $_model="Spider2"; }
			else if($model=="beetle") { $_model="Beetle"; }
			else if($model=="bumblebee") { $_model="BumbleBee"; }
			else if($model=="vm") { $_model="VM"; }
			else if($model=="soft_client") { $_model="Soft-client"; }
		
			//if($status=='up') { $status="led-green"; } else { $status="led-red"; }
			if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
			else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
			else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
			else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }
			print "<tr><td>$id</td><td>$template_details</td><td>$details</td><td>$_model - $_model_variant</td><td>$area</td>";
					 
			print "<td $bg_style><form method=\"POST\" action=\"index.php?page=dev_config_template_details&skey=$session_key\" >
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<input type=\"image\" src=\"i/details.png\" alt=\"Details\" title=\"Details\" class=\"top_title_icons\" />
					</form></td>";
			
			if($login_type=='root' || $login_type=='admin')
			{	print "<td>";
				api_form_post($curr_page);
				api_input_hidden("command", "del_dev_config_template");
				api_input_hidden("device_id", $id);
				api_input_hidden("device_details", $details);
				print "<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />";
				print "</form></td>";
			}
			else { print "<td></td>"; }
		
			print "</tr>";
		}
	}
}
?>
</table>
<br><br>
