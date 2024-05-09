<br>
<?php 
$main_table = "smoad_sdwan_servers";

$_mtu = $_POST['mtu']; $_mtu_before = $_POST['mtu_before'];
if($_mtu_before!=$_mtu)
{	db_api_set_value($db, $_mtu, "mtu", $main_table, $G_sds_id, "num"); }

//Front end

$query = "select mtu from $main_table where id=$G_sds_id"; 
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$mtu = $row['mtu'];
	}
}
	
api_form_post($curr_page);
api_input_hidden("mtu_before", $mtu);
?>
<table class="config_settings" style="width:660px;">
<?php
	api_ui_config_option_text("MTU", $mtu, "mtu", $_login_current_user_access, null);
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{ api_ui_config_option_update($_login_current_user_access); }
?>
</table></form>
