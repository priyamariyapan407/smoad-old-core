<?php


function wg_up_down($db)
{	$query = "select enable from smoad_wg_server_cfg where id=1"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$enable = $row['enable'];
		}
	}
	if($enable=="TRUE") { $db->query("insert into smoad_jobs (job) values (\"wg-quick up wg0\")"); }
	else if($enable=="FALSE") { $db->query("insert into smoad_jobs (job) values (\"wg-quick down wg0\")"); }
}


function wg_up_down_peer($db, $id)
{	$query = "select pubkey, allowedipsubnet, enable from smoad_wg_peers where id=$id"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$pubkey = $row['pubkey'];			
			$allowedipsubnet = $row['allowedipsubnet'];
			$enable = $row['enable'];
		}
	}
	if($enable=="TRUE") { $db->query("insert into smoad_jobs (job) values (\"wg set wg0 peer $pubkey allowed-ips $allowedipsubnet\")"); }
	else if($enable=="FALSE") { $db->query("insert into smoad_jobs (job) values (\"wg set wg0 peer $pubkey remove\")"); }
}


function wg_up_down_peers($db)
{	$query = "select id from smoad_wg_peers"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			wg_up_down_peer($db, $id);
		}
	}
}

function wg_update_status($db)
{	$ret = "DOWN";

	$query = "select status, port from smoad_wg_server_cfg where id=1"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$status = $row['status'];
			$port = $row['port'];
		}
	}

	$output = `wg show | grep wg`;
	if(strpos($output, $port) !== false) { $ret = "UP"; } else { $ret = "DOWN"; }
	if($ret!=$status) $query="update smoad_wg_server_cfg set status=\"$ret\" where id=1"; $db->query($query);
	print "wg status: $ret\n";
}

function wg_update_status_peers($db)
{	$ret = "DOWN";
	$query = "select id, pubkey, status from smoad_wg_peers"; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$pubkey = $row['pubkey'];
			$status = $row['status'];
			//check if the WG server is up and update DB
			$command = "wg show | grep $pubkey";
			$output = `$command`; 			
			if(strpos($output, $pubkey) >0) 
			{	  
				  $command2 = "wg | grep $pubkey -A 1 | grep endpoint";
				  $output2 = `$command2`;
				
				  if(strpos($output2, "endpoint") !== false) 
				  { $ret = "UP"; }
				  else 
				  { $ret = "UP_WAITING"; }
			}
			else 
			{ $ret = "DOWN"; }
			//print "wg peer status: $id $ret\n";
			if($ret!=$status) { $query="update smoad_wg_peers set status=\"$ret\" where id=$id"; $db->query($query); }
		}
	}
}

 		  

?>