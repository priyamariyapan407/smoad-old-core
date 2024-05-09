<?php

//external API for: Device (CPE) <> SD-WAN Server
 
error_reporting(5);
include('../../c/c_db_access.php');
include('../../c/c_smoad_api_device_ztp.php');
include('../../c/c_smoad_api.php');
include('c_include.php');

$_command = $_POST['command'];
$_serialnumber = $_POST['serialnumber'];
$_error = true;

//set the job_read_timestamp of this server to monitor if the server is UP/DOWN
if($_serialnumber!=null)
{	$query = "UPDATE smoad_devices SET job_read_timestamp=NOW() WHERE serialnumber='$_serialnumber'";
	$db->query($query);
}

if($_command=="sm_get_device_provision_script_by_serialnumber" && $_serialnumber!=null)
{ print sm_get_device_provision_script_by_serialnumber($db, $_serialnumber); $_error=false; }
else if($_command=="sm_get_device_api_pubkey_by_serialnumber" && $_serialnumber!=null) 
{	$api_pubkey = sm_get_device_api_pubkey_by_serialnumber($db, $_serialnumber);
	if($api_pubkey!=null) { print "$api_pubkey"; $_error=false; }
}
else if($_command=="sm_get_device_api_device_prikey_by_serialnumber" && $_serialnumber!=null) 
{	$api_device_prikey = sm_get_device_api_device_prikey_by_serialnumber($db, $_serialnumber);
	if($api_device_prikey!=null) { print "$api_device_prikey"; $_error=false; }
}
else if($_command=="sm_get_device_jobs_by_serialnumber" && $_serialnumber!=null)
{ print sm_get_device_jobs_by_serialnumber($db, $_serialnumber); $_error=false; }
else if($_command=="sm_get_device_config_by_serialnumber" && $_serialnumber!=null) //ztp edge to core
{  $_config = $_POST['config'];
	$query = "insert into smoad_server_jobs (device_serialnumber, command, job) values 
					(\"$_serialnumber\", \"$_command\", \"$_config\")";
	$db->query($query);
	print "success";
	$_error=false;
}


if($_error==true) { print "fail"; }

?>