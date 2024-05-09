<?php

//external API for: Device (CPE) <> SD-WAN Server
 
error_reporting(5);
include('../../c/c_db_access.php');
include('../device/c_include.php');

$_command = $_POST['command'];
$_serialnumber = $_POST['serialnumber'];
$_error = true;

//_debug_api_test_to_db($db, "$_serialnumber - $_command");

function sm_get_sds_jobs_by_serialnumber($db, $serialnumber)
{	$output_script = "";
	$query = "select id, job from smoad_sdwan_server_jobs where sds_serialnumber=\"$serialnumber\""; 
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$job = $row['job'];
			$output_script .= "$job\n";
			$query2 = "delete from smoad_sdwan_server_jobs where id=\"$id\"";
			$db->query($query2);
		}
	}

	return $output_script;
}


//set the job_read_timestamp of this server to monitor if the server is UP/DOWN
if($_serialnumber!=null)
{	$query = "UPDATE smoad_sdwan_servers SET job_read_timestamp=NOW() WHERE serialnumber='$_serialnumber'";
	$db->query($query);
}

//_debug_api_test_to_db($db, "$_serialnumber - $_command");

if($_command=="sm_get_sds_jobs_by_serialnumber" && $_serialnumber!=null)
{ print sm_get_sds_jobs_by_serialnumber($db, $_serialnumber); $_error=false; }
else if($_command=="sm_get_sds_config_by_serialnumber" && $_serialnumber!=null) //ztp gw to core
{  $_config = $_POST['config'];
	//_debug_api_test_to_db($db, "$_serialnumber - $_command $_config");
	$query = "insert into smoad_server_jobs (device_serialnumber, command, job) values 
					(\"$_serialnumber\", \"$_command\", \"$_config\")";
	$db->query($query);
	print "success";
	$_error=false;
}

if($_error==true) { print "fail"; }

?>