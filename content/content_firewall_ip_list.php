<br>
<?php 

$command = $_POST['command'];

if($command=="add_ip")
{ 	$src_ip = $_POST['src_ip']; $description = $_POST['description']; $type = $_POST['type'];
	if($src_ip!="" && $type!="" && $description!="")
	{	$description = str_replace(",", ";", $description); //in description change "," to some ";"
		
		$id_rand_key = random_bytes(6); 
		$id_rand_key = bin2hex($id_rand_key);
	
		$query= "insert into smoad_fw_ip_list (type, src_ip, description, id_rand_key)
		          values ('$type', '$src_ip', '$description', '$id_rand_key')"; 
		$db->query($query);
	}
}
else if($command=="del_ip")
{	$id = $_POST['id'];
	
	$query = "delete from smoad_fw_ip_list where id=$id"; $db->query($query);
}

?>

<?php 
if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
{	print "<p><strong>Add New IP:</strong></p>";
	
	api_form_post($curr_page);
	api_input_hidden("command", "add_ip");
	
	print "<table class=\"list_items2\" style=\"width:1024px;font-size:10px;\">";
	api_ui_config_option_text("Source IP-Address", $src_ip, "src_ip", null, null);

	print '<tr><td>Type</td><td>
	<select name="type">
	<option value="whitelist" >Whitelist</option>
	<option value="blacklist" >Blacklist</option>
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

<tr><th>ID</th><th>Type</th><th>Source IP-Address</th><th>Description</th><th></th></tr>

<?php

$query="select id, type, src_ip, description from smoad_fw_ip_list order by id";
if($res = $db->query($query))
{	
  while($row = $res->fetch_assoc())
  {
  	   $id = $row['id'];
  	   $type = $row['type'];
		$src_ip = $row['src_ip'];
		$description = $row['description'];
		
		if($type=="whitelist") { $_type="WHITELIST"; $bg_style="style=\"color:#2981e4;font-weight:bold;\""; }
		else if($type=="blacklist") { $_type="BLACKLIST"; $bg_style="style=\"color:#D84430;font-weight:bold;\""; }
		
		print "<tr><td >$id</td><td $bg_style >$_type</td><td >$src_ip</td><td >$description</td>";
		
		if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
		{	print "<td ><form method=\"POST\" action=\"$curr_page\" >
					<input type=\"hidden\" name=\"command\" value=\"del_ip\" />
					<input type=\"hidden\" name=\"id\" value=\"$id\" />
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
