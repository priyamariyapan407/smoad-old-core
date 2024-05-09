<?php error_reporting(5);

function api_get_rand_sdwan_server_ipaddr($db)
{	$query = "SELECT ipaddr FROM smoad_sdwan_servers ORDER BY RAND() LIMIT 1"; 
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	return $row['ipaddr'];
			}
		}
	return null;
}




?>