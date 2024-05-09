<br>
<?php 

$command = $_POST['command'];

function _ip_port_dec_to_hex($val)
{
	$len=strlen($val);
	$conv=base_convert($val, 10, 16);
	if($len==2){$conv = "00".$conv; }
	else if($len==3) {$conv = "0".$conv; }
	else { return "0000"; }
	return $conv;
}

if($command=="add_rule")
{ 
   $port = $_POST['port'];$src_mac = $_POST['src_mac'];$dst_mac = $_POST['dst_mac'];$src_ip = $_POST['src_ip'];$dst_ip = $_POST['dst_ip'];
   $proto = $_POST['proto'];$src_port = $_POST['src_port'];$dst_port = $_POST['dst_port']; $description = $_POST['description'];$action = $_POST['action'];
	
	if($port=="") { $port="*";}
	if($src_mac=="") { $src_mac="*";} if($dst_mac=="") { $dst_mac="*";}
	if($src_ip=="") { $src_ip="*";} if($dst_ip=="") { $dst_ip="*";}
	if($proto=="") { $proto="*";} 
	if($src_port=="") { $src_port="*";} else { $src_port=_ip_port_dec_to_hex($src_port); } 
	if($dst_port=="") { $dst_port="*";} else { $dst_port=_ip_port_dec_to_hex($dst_port); }
	if($description=="") { $description="SMOAD fw rule"; }
	$description = str_replace(",", ";", $description); //in description change "," to some ";"
	
	//if proto is icmp, set dport, sport to *
	if($proto=="1") { $src_port="*"; $dst_port="*"; }
	
	$id_rand_key = random_bytes(6); 
	$id_rand_key = bin2hex($id_rand_key);

	$query= "insert into smoad_fw_rules (type, port, src_mac, dst_mac, src_ip, dst_ip, proto, src_port, dst_port, description, action, id_rand_key)
	          values ('user', '$port', '$src_mac', '$dst_mac', '$src_ip', '$dst_ip', '$proto', '$src_port', '$dst_port', '$description', '$action', '$id_rand_key')"; 
	$db->query($query);
	
	$query="select id from smoad_fw_rules where id_rand_key='$id_rand_key'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
  		{ $id = $row['id']; }
  	}
	$kernel_cmd = "add,$id,$port,$src_mac,$dst_mac,$src_ip,$dst_ip,$proto,$src_port,$dst_port,$action,$description";
	$kernel_cmd = "echo \"$kernel_cmd\" > /proc/smoad_fw_rules";
	$query= "insert into smoad_jobs (job) values ('$kernel_cmd')"; $db->query($query);
}
else if($command=="del_rule")
{	$id = $_POST['id'];
	$kernel_cmd = $_POST['kernel_cmd'];
	
	$kernel_cmd = "echo \"$kernel_cmd\" > /proc/smoad_fw_rules";
	$query= "insert into smoad_jobs (job) values ('$kernel_cmd')"; $db->query($query);
	
	$query = "delete from smoad_fw_rules where id=$id"; $db->query($query);
}

?>

<?php 
if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
{
	print "<p><strong>Add New Firewall Rule:</strong></p>";
	
	api_form_post($curr_page);
	api_input_hidden("command", "add_rule");
	
	print "<table class=\"list_items2\" style=\"width:1024px;font-size:10px;\">";
	api_ui_config_option_text("Port", $port, "port", null, null);
	api_ui_config_option_text("Source MAC", $src_mac, "src_mac", null, null);
	api_ui_config_option_text("Destination MAC", $dst_mac, "dst_mac", null, null);
	api_ui_config_option_text("Source IP-Address", $src_ip, "src_ip", null, null);
	api_ui_config_option_text("Destination IP-Address", $dst_ip, "dst_ip", null, null);
	
	print '<tr><td>Protocol</td><td>
	<select name="proto">
	<option value="*" default >ANY</option>
	<option value="6" >TCP</option>
	<option value="17" >UDP</option>
	<option value="1" >ICMP</option>
	</select></td></tr>';
	 
	api_ui_config_option_text("Source Port", $src_port, "src_port", null, null);
	api_ui_config_option_text("Destination Port", $dst_port, "dst_port", null, null);
	
	
	print '<tr><td>Action</td><td>
	<select name="action">
	<option value="drop" >Drop</option>
	<option value="allow" >Allow</option>
	<option value="monitor" >Monitor</option>
	</select></td></tr>';
	
	api_ui_config_option_text("Description", $description, "description", null, null);
	
	
	print '<tr><td></td><td>';
	api_button_post("Add", "Add new rule ?", "red"); 
	print '</td></tr>';
	print '</table></form>';
}
?>

<hr id="hr_style">
<p><strong>Applied Firewall Rules:</strong></p>

<table class="list_items" style="width:99%;font-size:10px;">

<tr><th>ID</th><th>Type</th><th>Port</th><th>Source MAC</th><th>Destination MAC</th><th>Source IP-Address</th>
<th>Destination IP-Address</th><th>Protocol</th><th>Source Port</th><th>Destination Port</th><th><center>Action</center></th><th>Description</th>
<th></th></tr>

<?php

$query="select id, type, port, src_mac, dst_mac, src_ip, dst_ip, proto, src_port, dst_port, description, action from smoad_fw_rules order by id";
if($res = $db->query($query))
{	
  while($row = $res->fetch_assoc())
  {
  	   $id = $row['id'];
  	   $type = $row['type'];
		$port = $row['port'];
		$src_mac = $row['src_mac'];
		$dst_mac = $row['dst_mac'];
		$src_ip = $row['src_ip'];
		$dst_ip = $row['dst_ip'];
		$proto = $row['proto'];
		$src_port = $row['src_port'];
		$dst_port = $row['dst_port'];
		$description = $row['description'];
		$action = $row['action'];
		
	
		if($action=="allow") { $_action="ALLOW"; $bg_style="style=\"color:#2981e4;font-weight:bold;\""; }
			else if($action=="monitor") { $_action="MONITOR"; $bg_style="style=\"color:#4d916a;font-weight:bold;\""; }
			else if($action=="drop") { $_action="DROP"; $bg_style="style=\"color:#D84430;font-weight:bold;\""; }
		
		if($proto=="6") { $_proto="TCP"; }
		else if($proto=="17") { $_proto="UDP"; }
		else if($proto=="1") { $_proto="ICMP"; }
		else if($proto=="*") { $_proto="ANY"; }
		
		print "<tr><td >$id</td>";
		if($type=="user") { print "<td ><img src=\"i/user-red.png\" title=\"User defined\" /></td>"; } 
		else if($type=="ips") { print "<td ><img src=\"i/ai.png\" title=\"IPS (AI)\" /></td>"; } 
		else { print "<td ><img src=\"i/dismiss.png\" title=\"Unknown\" /></td>"; }
		
		print "<td >$port</td><td >$src_mac</td><td >$dst_mac</td>
				<td >$src_ip</td><td >$dst_ip</td>
		      <td >$_proto</td><td >$src_port</td><td >$dst_port</td><td $bg_style ><center>$_action</center></td>
		      <td >$description</td>";
		
		if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
		{	$kernel_cmd = "del,$id,$port,$src_mac,$dst_mac,$src_ip,$dst_ip,$proto,$src_port,$dst_port,$action,$description";
			print "<td ><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_rule\" />
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
					<input type=\"hidden\" name=\"kernel_cmd\" value=\"$kernel_cmd\" />
					<!--<input type=\"submit\" name=\"submit_ok\" value=\"Delete\" style=\"border:0;\" class=\"a_button_red\" />-->
					<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />
					</form></td>";
		}
		else { print "<td $bg_style></td>"; }
		
	   print "</tr>";		
  }
}

?>
</table>
