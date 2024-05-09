<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Job</th><th>TIMESTAMP</th></tr>
<?php

$query = "select id, job, cfg_timestamp from smoad_device_jobs where device_serialnumber=\"$G_device_serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$job = $row['job'];
			$cfg_timestamp = $row['cfg_timestamp'];
			print "<tr><td>$id</td><td><pre>$job</pre></td><td>$cfg_timestamp</td></tr>";
		}
	}
	
?>
</table>

