<?php

//ZTP Dev
function sm_ztp_add_job($db, $device_serialnumber, $job)
{	$query = "insert into smoad_device_jobs (device_serialnumber, job) values ('$device_serialnumber', '$job')";
   $db->query($query);
}

function sm_ztp_del_jobs($db, $device_serialnumber)
{	$query = "delete from smoad_device_jobs where device_serialnumber='$device_serialnumber'";
   $db->query($query);
}

function sm_ztp_dev_get_dev_status($db, $serialnumber)
{	$query = "select status FROM smoad_devices where serialnumber='$serialnumber'";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{ $status = $row['status']; return $status; }	
	}
	return "down";
}

function sm_ztp_dev_get_dev_count($db, $status, $login_type)
{	$up_dev = 0;
	if($status!=null) { $status = "status=\"$status\""; }
	$id_customer = $GLOBALS['id_customer'];
	if($login_type=='root') { $customer=""; } else { $customer="customer_id = $id_customer "; }
	
	if($status!=null) { $where_clause="where $status"; }
	if($customer!=null) { if($where_clause!=null) { $where_clause="and $customer"; } else { $where_clause="where $customer"; } }
	
	$query = "select count(*) qty FROM smoad_devices $where_clause ";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$up_dev = $row['qty']; }	
	}
	return $up_dev;
}

//ZTP SDS
function sm_ztp_sds_add_job($db, $sds_serialnumber, $job)
{	$query = "insert into smoad_sdwan_server_jobs (sds_serialnumber, job) values ('$sds_serialnumber', '$job')";
   $db->query($query);
}

function sm_ztp_sds_del_jobs($db, $sds_serialnumber)
{	$query = "delete from smoad_sdwan_server_jobs where sds_serialnumber='$sds_serialnumber'";
   $db->query($query);
}

function sm_ztp_sds_get_gw_status($db, $serialnumber)
{	$query = "select status FROM smoad_sdwan_servers where serialnumber='$serialnumber'";
	//print "<pre>$query</pre>";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{ return "up"; }	
	}
	return "down";
}

function sm_ztp_dev_get_gw_count($db, $status)
{	$up_sds = 0;
	if($status!=null) { $status = "where status=\"$status\""; }
	$query = "select count(*) qty FROM smoad_sdwan_servers $status ";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$up_sds = $row['qty']; }	
	}
	return $up_sds;
}

?>