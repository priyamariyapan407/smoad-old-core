<?php

$main_table = "smoad_sdwan_servers";

$_id = $_POST['id'];
$_details = $_POST['details']; $_details_before = $_POST['details_before'];
if($_details_before!=$_details && $_details!=null && $_id!=null) 
{ db_api_set_value($db, $_details, "details", $main_table, $_id, "char");
  
  //update current selected ztp
  $_SESSION['ztp_sds_details']=$_details;
  db_api_set_value($db, $_details, "ztp_sds_details", "profile", 1, "char");
}

$_area = $_POST['area']; $_area_before = $_POST['area_before'];
if($_area_before!=$_area && $_area!=null && $_id!=null) 
{ db_api_set_value($db, $_area, "area", $main_table, $_id, "char"); }

$_ipaddr = $_POST['ipaddr']; $_ipaddr_before = $_POST['ipaddr_before'];
if($_ipaddr_before!=$_area && $_ipaddr!=null && $_id!=null) 
{ db_api_set_value($db, $_ipaddr, "ipaddr", $main_table, $_id, "char"); }



$query = "select id, details, license, serialnumber, ipaddr, area, status, enable, updated, type 
			from $main_table where id=$G_sds_id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$details = $row['details'];
			$license = $row['license'];
			$serialnumber = $row['serialnumber'];
			$ipaddr = $row['ipaddr'];
			$area = $row['area'];
			$type = $row['type'];
		}
	}	

$query = "select count(*) qty from smoad_devices where sdwan_server_ipaddr='$ipaddr'"; 
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $qty = $row['qty']; } }

print '<table class="config_settings" style="width:660px;">';		
api_form_post($curr_page);
api_input_hidden("details_before", $details);
api_input_hidden("ipaddr_before", $ipaddr);
api_input_hidden("area_before", $area);
api_input_hidden("id", $id);
api_ui_config_option_readonly("ID", $id);
api_ui_config_option_readonly("Serial Number", $G_sds_serialnumber);		
api_ui_config_option_text("Details", $details, "details", null, null);

if($qty>0) { api_ui_config_option_readonly("IP Addr or DNS", $ipaddr); }
{ api_ui_config_option_text("IP Addr or DNS", $ipaddr, "ipaddr", null, null); }

api_ui_config_option_text("Area", $area, "area", null, null);

	
if($type=="l2") { $type="L2 SD-WAN"; }
else if($type=="l3") { $type="L3 SD-WAN"; }
else if($type=="mptcp") { $type="MPTCP"; }
api_ui_config_option_readonly("Type", $type);		
api_ui_config_option_readonly("Assigned Devices", $qty);		

api_ui_config_option_update($_login_current_user_access);

print '</table></form><br>';

$title = "Circuit Summary";
$contents  = "<table class=\"list_items\" style=\"width:100%;font-size:10px;text-align:left;\">";
$contents  .= "<tr><th>Total Circuits</th><th>Link Status Up</th><th>Link Status Down</th></tr>";
$total_circuits = $link_status_up = $link_status_down = 0;
$query = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$G_sds_serialnumber\"";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$total_circuits = $row['row_count']; } }
$query = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$G_sds_serialnumber\" and sdwan_link_status=\"UP\"";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$link_status_up = $row['row_count']; } }
$query = "SELECT COUNT(*) row_count FROM smoad_sds_wg_peers WHERE serialnumber = \"$G_sds_serialnumber\" and sdwan_link_status=\"DOWN\"";
if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$link_status_down = $row['row_count']; } }

$link_status_up = api_ui_up_down_display_status(1, $link_status_up);
$link_status_down = api_ui_up_down_display_status(0, $link_status_down);
$contents  .= "<tr><td>$total_circuits</td><td>$link_status_up</td><td>$link_status_down</td></tr>";
$contents  .= "</table>";
widget(780, $title, $contents);

?>


