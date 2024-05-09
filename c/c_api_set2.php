<?php 

//UI preset APIs
function api_ui_config_option_readonly($name, $value, $help=null)
{	if($help!=null) { $help="title=\"$help\""; }
	print "<tr><td>$name</td><td $help>$value</td></tr>";
}


function api_ui_config_option_text($name, $value, $tbox_name, $access, $help=null)
{	if($help!=null) { $help="title=\"$help\""; }
	print "<tr><td>$name</td><td>";
   if($access=="access_level_limited") { print "$value"; }
   else { print "<input class=\"text_style\" style=\"width:240px;\" type=\"text\" $help name=\"$tbox_name\" value=\"$value\" />"; }
   print "</td></tr>";
}

function api_ui_config_option_password($name, $value, $tbox_name, $access, $help)
{	if($help!=null) { $help="title=\"$help\""; }
	print "<tr><td>$name</td><td>";
   if($access=="access_level_limited") { print "$value"; }
   else { print "<input class=\"text_style\" style=\"width:240px;\" type=\"password\" $help name=\"$tbox_name\" value=\"$value\" />"; }
   print "</td></tr>";
}

function api_ui_config_option_update($access)
{	if($access!="access_level_limited")
	{	print "<tr><td><input title=\"Update ?\" type=\"submit\" name=\"submit_ok\" value=\"&#x21E3; Update\" style=\"border:0;\" class=\"a_button_red\" onclick=\"return confirm('Are you sure?')\" />
		</td><td></td></tr>";
	}
}

function api_ui_config_option_add($access)
{	if($access!="access_level_limited")
	{	print "<tr><td><input title=\"Add new item ?\" type=\"submit\" name=\"submit_ok\" value=\"&#x271B; Add\" style=\"border:0;\" class=\"a_button_green\" onclick=\"return confirm('Are you sure?')\" />
		</td><td></td></tr>";
	}
}

function api_ui_config_option_search($access)
{	if($access!="access_level_limited")
	{	print "<tr><td><input title=\"Search item ?\" type=\"submit\" name=\"submit_ok\" value=\"&#x260C; Search\" style=\"border:0;\" class=\"a_button_blue\" />
		</td><td></td></tr>";
	}
}

function api_ui_config_option_update_firmware($access)
{	if($access!="access_level_limited")
	{	print "<tr><td><input title=\"Update Firmware ?\" type=\"submit\" name=\"submit_ok\" value=\"&#x2707; Update Firmware\" style=\"border:0;\" class=\"a_button_red\" onclick=\"return confirm('Are you sure?')\" />
		</td><td></td></tr>";
	}
}

function api_ui_up_down_display_status($status, $value)
{	/*
	else if($status==1)
	{	return "<div class=\"led-green2\" title=\"Status Up\">&#x1F845; $value</div>";
	}
	else if($status==0)
	{	return "<div class=\"led-red2\" title=\"Status Down\">&#x1F847; $value</div>";
	}*/
	if($status==2) { return "<img src=\"i/up-waiting.png\" title=\"Up Waiting\" /> $value"; }
	else if($status==1) { return "<img src=\"i/up.png\" title=\"Up\" /> $value"; }
	else if($status==0) { return "<img src=\"i/down.png\" title=\"Down\" /> $value"; }
}

?>