<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Command</th><th>Job</th><th>TIMESTAMP</th></tr>
<?php

$query = "select id, command, job, cfg_timestamp from smoad_server_jobs where device_serialnumber=\"$G_device_serialnumber\""; 
//print "$query";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
		   $command = $row['command'];
			$job = $row['job'];
			$cfg_timestamp = $row['cfg_timestamp'];
			print "<tr><td>$id</td><td>$command</td><td><pre>$job</pre></td><td>$cfg_timestamp</td></tr>";
		}
	}
	
?>
</table>
