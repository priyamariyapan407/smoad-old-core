<br>
<?php

$main_table = "smoad_alert_config";

$_edge_up_down = $_POST['edge_up_down']; $_edge_up_down_before = $_POST['edge_up_down_before'];
if($_edge_up_down!="TRUE") { $_edge_up_down="FALSE"; } //make binary
if(($_edge_up_down_before!=$_edge_up_down && $_edge_up_down_before!=null)) { db_api_set_value($db, $_edge_up_down, "edge_up_down", $main_table, 1, "char"); }

$_edge_up_down_mail = $_POST['edge_up_down_mail']; $_edge_up_down_mail_before = $_POST['edge_up_down_mail_before'];
if($_edge_up_down_mail!="TRUE") { $_edge_up_down_mail="FALSE"; } //make binary
if(($_edge_up_down_mail_before!=$_edge_up_down_mail && $_edge_up_down_mail_before!=null)) { db_api_set_value($db, $_edge_up_down_mail, "edge_up_down_mail", $main_table, 1, "char"); }

$_gw_up_down = $_POST['gw_up_down']; $_gw_up_down_before = $_POST['gw_up_down_before'];
if($_gw_up_down!="TRUE") { $_gw_up_down="FALSE"; } //make binary
if(($_gw_up_down_before!=$_gw_up_down && $_gw_up_down_before!=null)) { db_api_set_value($db, $_gw_up_down, "gw_up_down", $main_table, 1, "char"); }

$_gw_up_down_mail = $_POST['gw_up_down_mail']; $_gw_up_down_mail_before = $_POST['gw_up_down_mail_before'];
if($_gw_up_down_mail!="TRUE") { $_gw_up_down_mail="FALSE"; } //make binary
if(($_gw_up_down_mail_before!=$_gw_up_down_mail && $_gw_up_down_mail_before!=null)) { db_api_set_value($db, $_gw_up_down_mail, "gw_up_down_mail", $main_table, 1, "char"); }

$_fw_high_pkt_drop = $_POST['fw_high_pkt_drop']; $_fw_high_pkt_drop_before = $_POST['fw_high_pkt_drop_before'];
if($_fw_high_pkt_drop!="TRUE") { $_fw_high_pkt_drop="FALSE"; } //make binary
if(($_fw_high_pkt_drop_before!=$_fw_high_pkt_drop && $_fw_high_pkt_drop_before!=null)) { db_api_set_value($db, $_fw_high_pkt_drop, "fw_high_pkt_drop", $main_table, 1, "char"); }

$_fw_high_pkt_drop_mail = $_POST['fw_high_pkt_drop_mail']; $_fw_high_pkt_drop_mail_before = $_POST['fw_high_pkt_drop_mail_before'];
if($_fw_high_pkt_drop_mail!="TRUE") { $_fw_high_pkt_drop_mail="FALSE"; } //make binary
if(($_fw_high_pkt_drop_mail_before!=$_fw_high_pkt_drop_mail && $_fw_high_pkt_drop_mail_before!=null)) { db_api_set_value($db, $_fw_high_pkt_drop_mail, "fw_high_pkt_drop_mail", $main_table, 1, "char"); }

$_core_ui_user_login = $_POST['core_ui_user_login']; $_core_ui_user_login_before = $_POST['core_ui_user_login_before'];
if($_core_ui_user_login!="TRUE") { $_core_ui_user_login="FALSE"; } //make binary
if(($_core_ui_user_login_before!=$_core_ui_user_login && $_core_ui_user_login_before!=null)) { db_api_set_value($db, $_core_ui_user_login, "core_ui_user_login", $main_table, 1, "char"); }

$_core_ui_user_login_mail = $_POST['core_ui_user_login_mail']; $_core_ui_user_login_mail_before = $_POST['core_ui_user_login_mail_before'];
if($_core_ui_user_login_mail!="TRUE") { $_core_ui_user_login_mail="FALSE"; } //make binary
if(($_core_ui_user_login_mail_before!=$_core_ui_user_login_mail && $_core_ui_user_login_mail_before!=null)) { db_api_set_value($db, $_core_ui_user_login_mail, "core_ui_user_login_mail", $main_table, 1, "char"); }

$_link_status_sdwan_down = $_POST['link_status_sdwan_down']; $_link_status_sdwan_down_before = $_POST['link_status_sdwan_down_before'];
if($_link_status_sdwan_down!="TRUE") { $_link_status_sdwan_down="FALSE"; } //make binary
if(($_link_status_sdwan_down_before!=$_link_status_sdwan_down && $_link_status_sdwan_down_before!=null)) { db_api_set_value($db, $_link_status_sdwan_down, "link_status_sdwan_down", $main_table, 1, "char"); }

$_link_status_sdwan_down_mail = $_POST['link_status_sdwan_down_mail']; $_link_status_sdwan_down_mail_before = $_POST['link_status_sdwan_down_mail_before'];
if($_link_status_sdwan_down_mail!="TRUE") { $_link_status_sdwan_down_mail="FALSE"; } //make binary
if(($_link_status_sdwan_down_mail_before!=$_link_status_sdwan_down_mail && $_link_status_sdwan_down_mail_before!=null)) { db_api_set_value($db, $_link_status_sdwan_down_mail, "link_status_sdwan_down_mail", $main_table, 1, "char"); }

$_link_status_sdwan_up = $_POST['link_status_sdwan_up']; $_link_status_sdwan_up_before = $_POST['link_status_sdwan_up_before'];
if($_link_status_sdwan_up!="TRUE") { $_link_status_sdwan_up="FALSE"; } //make binary
if(($_link_status_sdwan_up_before!=$_link_status_sdwan_up && $_link_status_sdwan_up_before!=null)) { db_api_set_value($db, $_link_status_sdwan_up, "link_status_sdwan_up", $main_table, 1, "char"); }

$_link_status_sdwan_up_mail = $_POST['link_status_sdwan_up_mail']; $_link_status_sdwan_up_mail_before = $_POST['link_status_sdwan_up_mail_before'];
if($_link_status_sdwan_up_mail!="TRUE") { $_link_status_sdwan_up_mail="FALSE"; } //make binary
if(($_link_status_sdwan_up_mail_before!=$_link_status_sdwan_up_mail && $_link_status_sdwan_up_mail_before!=null)) { db_api_set_value($db, $_link_status_sdwan_up_mail, "link_status_sdwan_up_mail", $main_table, 1, "char"); }

$_sdwan_link_high_usage = $_POST['sdwan_link_high_usage']; $_sdwan_link_high_usage_before = $_POST['sdwan_link_high_usage_before'];
if($_sdwan_link_high_usage!="TRUE") { $_sdwan_link_high_usage="FALSE"; } //make binary
if(($_sdwan_link_high_usage_before!=$_sdwan_link_high_usage && $_sdwan_link_high_usage_before!=null)) { db_api_set_value($db, $_sdwan_link_high_usage, "sdwan_link_high_usage", $main_table, 1, "char"); }

$_sdwan_link_high_usage_mail = $_POST['sdwan_link_high_usage_mail']; $_sdwan_link_high_usage_mail_before = $_POST['sdwan_link_high_usage_mail_before'];
if($_sdwan_link_high_usage_mail!="TRUE") { $_sdwan_link_high_usage_mail="FALSE"; } //make binary
if(($_sdwan_link_high_usage_mail_before!=$_sdwan_link_high_usage_mail && $_sdwan_link_high_usage_mail_before!=null)) { db_api_set_value($db, $_sdwan_link_high_usage_mail, "sdwan_link_high_usage_mail", $main_table, 1, "char"); }

$_sdwan_link_high_latency = $_POST['sdwan_link_high_latency']; $_sdwan_link_high_latency_before = $_POST['sdwan_link_high_latency_before'];
if($_sdwan_link_high_latency!="TRUE") { $_sdwan_link_high_latency="FALSE"; } //make binary
if(($_sdwan_link_high_latency_before!=$_sdwan_link_high_latency && $_sdwan_link_high_latency_before!=null)) { db_api_set_value($db, $_sdwan_link_high_latency, "sdwan_link_high_latency", $main_table, 1, "char"); }

$_sdwan_link_high_latency_mail = $_POST['sdwan_link_high_latency_mail']; $_sdwan_link_high_latency_mail_before = $_POST['sdwan_link_high_latency_mail_before'];
if($_sdwan_link_high_latency_mail!="TRUE") { $_sdwan_link_high_latency_mail="FALSE"; } //make binary
if(($_sdwan_link_high_latency_mail_before!=$_sdwan_link_high_latency_mail && $_sdwan_link_high_latency_mail_before!=null)) { db_api_set_value($db, $_sdwan_link_high_latency_mail, "sdwan_link_high_latency_mail", $main_table, 1, "char"); }

$_sdwan_link_high_jitter = $_POST['sdwan_link_high_jitter']; $_sdwan_link_high_jitter_before = $_POST['sdwan_link_high_jitter_before'];
if($_sdwan_link_high_jitter!="TRUE") { $_sdwan_link_high_jitter="FALSE"; } //make binary
if(($_sdwan_link_high_jitter_before!=$_sdwan_link_high_jitter && $_sdwan_link_high_jitter_before!=null)) { db_api_set_value($db, $_sdwan_link_high_jitter, "sdwan_link_high_jitter", $main_table, 1, "char"); }

$_sdwan_link_high_jitter_mail = $_POST['sdwan_link_high_jitter_mail']; $_sdwan_link_high_jitter_mail_before = $_POST['sdwan_link_high_jitter_mail_before'];
if($_sdwan_link_high_jitter_mail!="TRUE") { $_sdwan_link_high_jitter_mail="FALSE"; } //make binary
if(($_sdwan_link_high_jitter_mail_before!=$_sdwan_link_high_jitter_mail && $_sdwan_link_high_jitter_mail_before!=null)) { db_api_set_value($db, $_sdwan_link_high_jitter_mail, "sdwan_link_high_jitter_mail", $main_table, 1, "char"); }


$query = "select edge_up_down, edge_up_down_mail, gw_up_down, gw_up_down_mail, 
		fw_high_pkt_drop, fw_high_pkt_drop_mail,  
		core_ui_user_login, core_ui_user_login_mail,
		link_status_sdwan_down, link_status_sdwan_down_mail,
		link_status_sdwan_up, link_status_sdwan_up_mail,
		sdwan_link_high_usage, sdwan_link_high_usage_mail,
		sdwan_link_high_latency, sdwan_link_high_latency_mail,
		sdwan_link_high_jitter, sdwan_link_high_jitter_mail from $main_table where id=1";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$edge_up_down = $row['edge_up_down'];
		$edge_up_down_mail = $row['edge_up_down_mail'];
		$gw_up_down = $row['gw_up_down'];
		$gw_up_down_mail = $row['gw_up_down_mail'];
		$fw_high_pkt_drop = $row['fw_high_pkt_drop'];
		$fw_high_pkt_drop_mail = $row['fw_high_pkt_drop_mail'];
		$core_ui_user_login = $row['core_ui_user_login'];
		$core_ui_user_login_mail = $row['core_ui_user_login_mail'];
		$link_status_sdwan_down = $row['link_status_sdwan_down'];
		$link_status_sdwan_down_mail = $row['link_status_sdwan_down_mail'];
		$link_status_sdwan_up = $row['link_status_sdwan_up'];
		$link_status_sdwan_up_mail = $row['link_status_sdwan_up_mail'];
		$sdwan_link_high_usage = $row['sdwan_link_high_usage'];
		$sdwan_link_high_usage_mail = $row['sdwan_link_high_usage_mail'];
		$sdwan_link_high_latency = $row['sdwan_link_high_latency'];
		$sdwan_link_high_latency_mail = $row['sdwan_link_high_latency_mail'];
		$sdwan_link_high_jitter = $row['sdwan_link_high_jitter'];
		$sdwan_link_high_jitter_mail = $row['sdwan_link_high_jitter_mail'];
	}
}

	api_form_post($curr_page);
	api_input_hidden("edge_up_down_before", $edge_up_down);
	api_input_hidden("edge_up_down_mail_before", $edge_up_down_mail);
	api_input_hidden("gw_up_down_before", $gw_up_down);
	api_input_hidden("gw_up_down_mail_before", $gw_up_down_mail);
	api_input_hidden("fw_high_pkt_drop_before", $fw_high_pkt_drop);
	api_input_hidden("fw_high_pkt_drop_mail_before", $fw_high_pkt_drop_mail);
	api_input_hidden("core_ui_user_login_before", $core_ui_user_login);
	api_input_hidden("core_ui_user_login_mail_before", $core_ui_user_login_mail);
	api_input_hidden("link_status_sdwan_down_before", $link_status_sdwan_down);
	api_input_hidden("link_status_sdwan_down_mail_before", $link_status_sdwan_down_mail);
	api_input_hidden("link_status_sdwan_up_before", $link_status_sdwan_up);
	api_input_hidden("link_status_sdwan_up_mail_before", $link_status_sdwan_up_mail);
	api_input_hidden("sdwan_link_high_usage_before", $sdwan_link_high_usage);
	api_input_hidden("sdwan_link_high_usage_mail_before", $sdwan_link_high_usage_mail);
	api_input_hidden("sdwan_link_high_latency_before", $sdwan_link_high_latency);
	api_input_hidden("sdwan_link_high_latency_mail_before", $sdwan_link_high_latency_mail);
	api_input_hidden("sdwan_link_high_jitter_before", $sdwan_link_high_jitter);
	api_input_hidden("sdwan_link_high_jitter_mail_before", $sdwan_link_high_jitter_mail);
	print '<table class="config_settings" style="width:600px;">';
	
	print "<tr><td><b>EDGE Alerts</b></td><td></td></tr>";
	print "<tr><td>EDGE is up or down/disconnected</td><td>";
	if($edge_up_down=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"edge_up_down\" id=\"edge_up_down\" title=\"EDGE is up or down !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>EDGE is up or down/disconnected - Mail Alert</td><td>";
	if($edge_up_down_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"edge_up_down_mail\" id=\"edge_up_down_mail\" title=\"EDGE is up or down - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";

	print "<tr><td>SD-WAN Network up</td><td>";
	if($link_status_sdwan_up=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"link_status_sdwan_up\" id=\"link_status_sdwan_up\" title=\"EDGE SD-WAN Network up incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN Network up - Mail Alert</td><td>";
	if($link_status_sdwan_up_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"link_status_sdwan_up_mail\" id=\"link_status_sdwan_up_mail\" title=\"EDGE SD-WAN Network up incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN Network down</td><td>";
	if($link_status_sdwan_down=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"link_status_sdwan_down\" id=\"link_status_sdwan_down\" title=\"EDGE SD-WAN Network down incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN Network down - Mail Alert</td><td>";
	if($link_status_sdwan_down_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"link_status_sdwan_down_mail\" id=\"link_status_sdwan_down_mail\" title=\"EDGE SD-WAN Network down incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high usage</td><td>";
	if($sdwan_link_high_usage=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_usage\" id=\"sdwan_link_high_usage\" title=\"EDGE SD-WAN link high usage incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high usage - Mail Alert</td><td>";
	if($sdwan_link_high_usage_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_usage_mail\" id=\"sdwan_link_high_usage_mail\" title=\"EDGE SD-WAN link high usage incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high latency</td><td>";
	if($sdwan_link_high_latency=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_latency\" id=\"sdwan_link_high_latency\" title=\"EDGE high latency incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high latency - Mail Alert</td><td>";
	if($sdwan_link_high_latency_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_latency_mail\" id=\"sdwan_link_high_latency_mail\" title=\"EDGE high latency incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high jitter</td><td>";
	if($sdwan_link_high_jitter=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_jitter\" id=\"sdwan_link_high_jitter\" title=\"EDGE high jitter incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>SD-WAN link high jitter - Mail Alert</td><td>";
	if($sdwan_link_high_jitter_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"sdwan_link_high_jitter_mail\" id=\"sdwan_link_high_jitter_mail\" title=\"EDGE high jitter incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td></td><td></td></tr>";	
	print "<tr><td><b>Gateway Alerts</b></td><td></td></tr>";
	print "<tr><td>GW is up or down/disconnected</td><td>";
	if($gw_up_down=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"gw_up_down\" id=\"gw_up_down\" title=\"GW is up or down !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>GW is up or down/disconnected - Mail Alert</td><td>";
	if($gw_up_down_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"gw_up_down_mail\" id=\"gw_up_down_mail\" title=\"GW is up or down - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td></td><td></td></tr>";
	print "<tr><td><b>Security Alerts</b></td><td></td></tr>";
	print "<tr><td>Firewall high packet drop</td><td>";
	if($fw_high_pkt_drop=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"fw_high_pkt_drop\" id=\"fw_high_pkt_drop\" title=\"Threshold greater than 100 packets !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>Firewall high packet drop - Mail Alert</td><td>";
	if($fw_high_pkt_drop_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"fw_high_pkt_drop_mail\" id=\"fw_high_pkt_drop_mail\" title=\"Threshold greater than 100 packets - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";

	print "<tr><td></td><td></td></tr>";
	print "<tr><td><b>CORE Server Alerts</b></td><td></td></tr>";
	print "<tr><td>CORE UI user login/logout</td><td>";
	if($core_ui_user_login=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"core_ui_user_login\" id=\"core_ui_user_login\" title=\"CORE UI user login/logout incident !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
	print "<tr><td>CORE UI user login/logout - Mail Alert</td><td>";
	if($core_ui_user_login_mail=="TRUE") { $checked="checked"; } else { $checked=""; }
	print "<input type=\"checkbox\" style=\"border: 0;\"  name=\"core_ui_user_login_mail\" id=\"core_ui_user_login_mail\" title=\"CORE UI user login/logout incident - Mail Alert !\" value=\"TRUE\" $checked />";
	print "</td></tr>";
	
   
	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{ api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';
			

?>

