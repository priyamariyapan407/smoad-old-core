<br>
<?php

$main_table = "smoad_device_network_cfg";

$_config_update=false;
$_id = $_POST['id'];
$_ssid = $_POST['ssid']; $_ssid_before = $_POST['ssid_before'];
if($_ssid_before!=$_ssid && $_id!=null) 
{ db_api_set_value($db, $_ssid, "wireless_ssid", $main_table, $_id, "char");
  $job = "uci set wireless.default_radio0.ssid=^".$_ssid."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $_config_update=true; 
}

$_key = $_POST['key']; $_key_before = $_POST['key_before'];
if($_key_before!=$_key && $_id!=null) 
{ db_api_set_value($db, $_key, "wireless_key", $main_table, $_id, "char");
  $job = "uci set wireless.default_radio0.key=^".$_key."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $_config_update=true; 
}

$_wireless_auth_server = $_POST['wireless_auth_server']; $_wireless_auth_server_before = $_POST['wireless_auth_server_before'];
if($_wireless_auth_server_before!=$_wireless_auth_server && $_id!=null) 
{ db_api_set_value($db, $_wireless_auth_server, "wireless_auth_server", $main_table, $_id, "char");
  $job = "uci set wireless.default_radio0.auth_server=^".$_wireless_auth_server."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $_config_update=true; 
}

$_wireless_auth_secret = $_POST['wireless_auth_secret']; $_wireless_auth_secret_before = $_POST['wireless_auth_secret_before'];
if($_wireless_auth_secret_before!=$_wireless_auth_secret && $_id!=null) 
{ db_api_set_value($db, $_wireless_auth_secret, "wireless_auth_secret", $main_table, $_id, "char");
  $job = "uci set wireless.default_radio0.auth_secret=^".$_wireless_auth_secret."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job);
  $_config_update=true; 
}

$_encryption = $_POST['encryption']; $_encryption_before = $_POST['encryption_before'];
if($_encryption_before!=$_encryption && $_id!=null) 
{ if($_encryption=="psk2" || $_encryption=="psk-mixed" || $_encryption=="psk") 
  { db_api_set_value($db, $_encryption, "wireless_encryption", $main_table, $_id, "char");
  	 $job = "uci set wireless.default_radio0.encryption=^".$_encryption."^";
	 sm_ztp_add_job($db, $G_device_serialnumber, $job);
    $_config_update=true; 
  }
}


if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit network");
	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit wireless");
   sm_ztp_add_job($db, $G_device_serialnumber, "ifup lan");
}

$query = "select id, wireless_ssid, wireless_key, wireless_encryption, wireless_auth_server, wireless_auth_secret
		from $main_table where device_serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$ssid = $row['wireless_ssid'];
		$key = $row['wireless_key'];
		$encryption = $row['wireless_encryption'];
		$wireless_auth_server = $row['wireless_auth_server'];
		$wireless_auth_secret = $row['wireless_auth_secret'];
	}
}

	print "<form method=\"POST\" action=\"$curr_page\" >";
	api_input_hidden("ssid_before", $ssid);
	api_input_hidden("key_before", $key);
	api_input_hidden("encryption_before", $encryption);
	
	api_input_hidden("wireless_auth_server_before", $wireless_auth_server);
	api_input_hidden("wireless_auth_secret_before", $wireless_auth_secret);
	
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:800px;">';
			
	print "<tr><td align=right style=\"padding-right:40px;\">SSID</td><td>";
	if($_login_current_user_access=="access_level_limited") { print "$ssid"; }
	else { print "<input class=\"text_style\" style=\"width:180px;\" type=\"text\" name=\"ssid\" value=\"$ssid\" />"; }
	print "</td></tr>";

	print "<tr><td align=right style=\"padding-right:40px;\">Wireless Security (encryption)</td><td>";
	if($_login_current_user_access=="access_level_limited") 
	{	if($encryption=="psk2") { print "WPA2-PSK (strong security)"; }
		else if($encryption=="psk-mixed") { print "WPA-PSK/WPA2-PSK Mixed Mode (medium security)"; }
		else if($encryption=="psk") { print "WPA-PSK (medium security)"; } 
		else if($encryption=="wpa") { print "WPA-EAP (medium security)"; }
		else if($encryption=="wpa2") { print "WPA2-EAP (strong security)"; }
	}
	else 
	{	print "<select name=\"encryption\" id=\"encryption\">";
		if($encryption=="psk2") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"psk2\" $selected >WPA2-PSK (strong security)</option>";
		if($encryption=="psk-mixed") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"psk-mixed\" $selected >WPA-PSK/WPA2-PSK Mixed Mode (medium security)</option>";
		if($encryption=="psk") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"psk\" $selected >WPA-PSK (medium security)</option>";
		if($encryption=="wpa") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"wpa\" $selected >WPA-EAP (medium security)</option>";
		if($encryption=="wpa2") { $selected="selected"; } else { $selected=""; }
		print "<option value=\"wpa2\" $selected >WPA2-EAP (strong security)</option>";
		print "</select>";
		print "</td></tr>";
	}

	if($encryption=="wpa" || $encryption=="wpa2")
	{	print "<tr><td align=right style=\"padding-right:40px;\">RADIUS Authentication Server</td><td>";
   	if($_login_current_user_access=="access_level_limited") { print "$wireless_auth_server"; }
   	else { print "<input class=\"text_style\" style=\"width:180px;\" type=\"text\" name=\"wireless_auth_server\" value=\"$wireless_auth_server\" />"; }
   	print "</td></tr>";
   	
   	print "<tr><td align=right style=\"padding-right:40px;\">RADIUS Authentication Secret</td><td>";
   	if($_login_current_user_access=="access_level_limited") { print "$wireless_auth_secret"; }
   	else { print "<input class=\"text_style\" style=\"width:180px;\" type=\"text\" name=\"wireless_auth_secret\" value=\"$wireless_auth_secret\" />"; }
   	print "</td></tr>";
	}
	else
	{	print "<tr><td align=right style=\"padding-right:40px;\">Key</td><td>";
   	if($_login_current_user_access=="access_level_limited") { print "$key"; }
   	else { print "<input class=\"text_style\" style=\"width:180px;\" type=\"text\" name=\"key\" value=\"$key\" />"; }
   	print "</td></tr>";
	}

	if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
	{	api_ui_config_option_update($_login_current_user_access); }
			
	print '</table></form><br>';
			


?>

