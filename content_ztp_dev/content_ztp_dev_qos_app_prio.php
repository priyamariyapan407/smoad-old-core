<br>
<?php

$main_table = "smoad_devices";

$_config_update=false;
$_id = $_POST['id'];

$_qos_microsoft_teams = $_POST['qos_microsoft_teams']; $_qos_microsoft_teams_before = $_POST['qos_microsoft_teams_before'];
if($_qos_microsoft_teams_before!=$_qos_microsoft_teams && $_qos_microsoft_teams!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_microsoft_teams, "qos_microsoft_teams", $main_table, $_id, "char");
  $job = "uci set smoad.qos.microsoft_teams=^".$_qos_microsoft_teams."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_youtube = $_POST['qos_youtube']; $_qos_youtube_before = $_POST['qos_youtube_before'];
if($_qos_youtube_before!=$_qos_youtube && $_qos_youtube!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_youtube, "qos_youtube", $main_table, $_id, "char");
  $job = "uci set smoad.qos.youtube=^".$_qos_youtube."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_zoom = $_POST['qos_zoom']; $_qos_zoom_before = $_POST['qos_zoom_before'];
if($_qos_zoom_before!=$_qos_zoom && $_qos_zoom!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_zoom, "qos_zoom", $main_table, $_id, "char");
  $job = "uci set smoad.qos.zoom=^".$_qos_zoom."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_iperf = $_POST['qos_iperf']; $_qos_iperf_before = $_POST['qos_iperf_before'];
if($_qos_iperf_before!=$_qos_iperf && $_qos_iperf!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_iperf, "qos_iperf", $main_table, $_id, "char");
  $job = "uci set smoad.qos.iperf=^".$_qos_iperf."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_voip = $_POST['qos_voip']; $_qos_voip_before = $_POST['qos_voip_before'];
if($_qos_voip_before!=$_qos_voip && $_qos_voip!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_voip, "qos_voip", $main_table, $_id, "char");
  $job = "uci set smoad.qos.voip=^".$_qos_voip."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_skype = $_POST['qos_skype']; $_qos_skype_before = $_POST['qos_skype_before'];
if($_qos_skype_before!=$_qos_skype && $_qos_skype!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_skype, "qos_skype", $main_table, $_id, "char");
  $job = "uci set smoad.qos.skype=^".$_qos_skype."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

$_qos_sdwan = $_POST['qos_sdwan']; $_qos_sdwan_before = $_POST['qos_sdwan_before'];
if($_qos_sdwan_before!=$_qos_sdwan && $_qos_sdwan!=null && $_id!=null) 
{ db_api_set_value($db, $_qos_sdwan, "qos_sdwan", $main_table, $_id, "char");
  $job = "uci set smoad.qos.sdwan=^".$_qos_sdwan."^";
  sm_ztp_add_job($db, $G_device_serialnumber, $job); 
  $_config_update=true;
}

if($_config_update) 
{	sm_ztp_add_job($db, $G_device_serialnumber, "uci commit smoad");
}

function _qos_app_prio_select($title, $value, $name)
{
	print "<tr><td>$title</td><td>";
	print "<select name=\"$name\" id=\"$name\">";
	if($value=="high") { $selected="selected"; } else { $selected=""; } 
	print "<option value=\"high\" $selected >High</option>";
	if($value=="medium") { $selected="selected"; } else { $selected=""; }
	print "<option value=\"medium\" $selected >Medium</option>";
	if($value=="low") { $selected="selected"; } else { $selected=""; }
	print "<option value=\"low\" $selected >Low</option>";
	print "</select>";
	print "</td></tr>";
}

$query = "select id, qos_microsoft_teams, qos_youtube, qos_iperf, qos_voip, qos_skype, qos_zoom, qos_sdwan from $main_table where serialnumber='$G_device_serialnumber'";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$id = $row['id'];
		$qos_microsoft_teams = $row['qos_microsoft_teams'];
		$qos_youtube = $row['qos_youtube'];
		$qos_iperf = $row['qos_iperf'];
		$qos_voip = $row['qos_voip'];
		$qos_skype = $row['qos_skype'];
		$qos_zoom = $row['qos_zoom'];
		$qos_sdwan = $row['qos_sdwan'];
	}
}

	api_form_post($curr_page);
	api_input_hidden("qos_microsoft_teams_before", $qos_microsoft_teams);
	api_input_hidden("qos_youtube_before", $qos_youtube);
	api_input_hidden("qos_iperf_before", $qos_iperf);
	api_input_hidden("qos_voip_before", $qos_voip);
	api_input_hidden("qos_skype_before", $qos_skype);
	api_input_hidden("qos_zoom_before", $qos_zoom);
	api_input_hidden("qos_sdwan_before", $qos_sdwan);
	api_input_hidden("id", $id);
	print '<table class="config_settings" style="width:600px;">';
	
	_qos_app_prio_select("Zoom Meetings", $qos_zoom, "qos_zoom");
	_qos_app_prio_select("Microsoft Teams", $qos_microsoft_teams, "qos_microsoft_teams");
	_qos_app_prio_select("Youtube", $qos_youtube, "qos_youtube");
	_qos_app_prio_select("Skype", $qos_skype, "qos_skype");
	_qos_app_prio_select("VOIP", $qos_voip, "qos_voip");
	_qos_app_prio_select("iperf", $qos_iperf, "qos_iperf");
	_qos_app_prio_select("SD-WAN", $qos_sdwan, "qos_sdwan");
	api_ui_config_option_update($_login_current_user_access);
			
	print '</table></form><br>';
			


?>

