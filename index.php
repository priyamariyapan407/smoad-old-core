<?php include("c/c_db_access.php"); 

include("c/c_G_vars.php"); error_reporting(5); session_start();
include("c/c_api_set1.php");
include("c/c_api_set2.php");
include("c/c_api_pagination.php");
include("c/c_api_network.php");
include("c/c_api_dash.php");
include("c/c_wg_api.php");
include("c/c_api_ztp.php");
include("c/c_api_mail.php");
include("c/c_smoad.php");

$page = $_GET['page'];
$skey = $_GET['skey'];
if($page==NULL) { $page="login"; }
$help="nohelp";

$pageset=false;
$contentfilepath="content"; //default
$contentfilepathztpdev="content_ztp_dev"; //ztp-dev
$contentfilepathztpsds="content_ztp_sds"; //ztp-sds
$contentfilepathztpcust="content_ztp_cust"; //ztp-cust

$G_server_name = $_SESSION['server_name'];
$username = $_SESSION['username'];
$session_key = $_SESSION['sessionkey'];
$id_customer = $_SESSION['id_customer'];
$login_type = $_SESSION['login_type'];
$curr_page_protocol=$_SERVER['PROTOCOL'] = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$curr_page= $curr_page_protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//ZTP-Device
$G_device_serialnumber = $_SESSION['ztp_dev_sn'];
$G_device_id = $_SESSION['ztp_dev_id'];
$G_device_details = $_SESSION['ztp_dev_details'];
$G_device_model = $_SESSION['ztp_dev_model'];
$G_device_model_variant = $_SESSION['ztp_dev_model_variant'];
$G_device_os = $_SESSION['ztp_dev_os'];

//Pagination
$G_items_per_page = 30;

//ZTP-SDWAN-Server
$G_sds_serialnumber = $_SESSION['ztp_sds_sn'];
$G_sds_id = $_SESSION['ztp_sds_id'];
$G_sds_details = $_SESSION['ztp_sds_details'];

//ZTP-Customer
$G_cust_id = $_SESSION['ztp_cust_id'];
$G_cust_name = $_SESSION['ztp_cust_name'];

//email
$G_root_email1 = $_SESSION['root_email1'];
$G_user_email1 = $_SESSION['user_email1'];
$G_cust_email1 = $_SESSION['cust_email1'];


$sdwan_server_proto = "https";

function api_checklogin($db, $username, $dbname, $row)
{	$query = "select login_current_timestamp from $dbname where $row ='$username'";
	if($res = $db->query($query))
	{ while ($row = $res->fetch_assoc()) { $_login_current_timestamp = $row['login_current_timestamp']; } }

	if($_login_current_timestamp=="notset" || $_login_current_timestamp==null) { return false; }
	$_current_timestamp = time();

	$_elapsed_mins = round(($_current_timestamp - $_login_current_timestamp) / 60);
	if($_elapsed_mins>60) { $db->query("update profile set login_current_timestamp=\"notset\" where $row ='$username'"); return false; }
	return true;
}

//checklogin
if($page!="login") 
{  if($skey!=$session_key) { $page="logout"; }
	
	//Host Header Injection prevention - comment for now to allow any IP
	//$allowed_host = array('103.219.247.205', '192.168.7.199', 'smoad-server');
	//if(!isset($_SERVER['HTTP_HOST']) || !in_array($_SERVER['HTTP_HOST'], $allowed_host)) { $page="logout"; }
	
	if(api_checklogin($db, $username, 'profile', 'username')==false && 
		api_checklogin($db, $username, 'smoad_customers', 'custname')==false && 
		api_checklogin($db, $username, 'smoad_users', 'username')==false) { $page="logout"; }
}

if($page=="home") { $page_title = "SMOAD Core - Home"; $pageset=true; }
else if($page=="port_config") { $page_title = "Port Config"; $pageset=true; }
else if($page=="users") { $page_title = "Users"; $pageset=true; }
else if($page=="password") { $page_title = "Password"; $pageset=true; }
else if($page=="user_details") { $page_title = "User Details"; $pageset=true; }
else if($page=="customers") { $page_title = "Customers"; $pageset=true; }
else if($page=="customer_details") { $page_title = "Customer Details"; $pageset=true; }
else if($page=="devices") { $page_title = "SMOAD Edge Devices"; $pageset=true; }
else if($page=="dev_config_templates") { $page_title = "SMOAD Edge Device Config Templates"; $pageset=true; }
else if($page=="dev_config_template_details") { $page_title = "SMOAD Edge Device Config Template Details"; $pageset=true; }
else if($page=="user_device_access_log") { $page_title = "SMOAD User Device Access Log"; $pageset=true; }
else if($page=="sdwan_servers") { $page_title = "SD-WAN Servers"; $pageset=true; }
else if($page=="ticketing_servers") { $page_title = "IMS - Ticketing Servers"; $pageset=true; }
else if($page=="ticketing_server_details") { $page_title = "IMS - Ticketing Server Details"; $pageset=true; }
else if($page=="update_firmware_server") { $page_title = "Frmware Server Settings"; $pageset=true; }
else if($page=="logout") { $page_title = "... auto logout!"; $pageset=true; }
else if($page=="firewall_dash") { $page_title = "Security - Firewall Dashboard"; $pageset=true; }
else if($page=="firewall_rules") { $page_title = "Security - Firewall Log"; $pageset=true; }
else if($page=="firewall_ip_list") { $page_title = "Security - Firewall IP List"; $pageset=true; }
else if($page=="firewall_log_index") { $page_title = "Security - Firewall Log Index"; $pageset=true; }
else if($page=="firewall_log") { $page_title = "Security - Firewall Log"; $pageset=true; }
else if($page=="alerts") { $page_title = "Alerts"; $pageset=true; }
else if($page=="alert_details") { $page_title = "Alert - Details"; $pageset=true; }
else if($page=="alerts_index") { $page_title = "Alerts - Index"; $pageset=true; }
else if($page=="alert_config") { $page_title = "Alert - Configuration"; $pageset=true; }
else if($page=="debug_jobs") { $page_title = "Engineering Debug - Jobs"; $pageset=true; }
else if($page=="ztp_dev_home") { $page_title = "Edge ZTP - Home"; $pageset=true; }
else if($page=="ztp_dev_network") { $page_title = "Edge ZTP - Network"; $pageset=true; }
else if($page=="ztp_dev_dash") { $page_title = "Edge ZTP - Dashboard"; $pageset=true; }
else if($page=="ztp_dev_wan") { $page_title = "Edge ZTP - WAN Settings"; $pageset=true; }
else if($page=="ztp_dev_lan") { $page_title = "Edge ZTP - LAN Settings"; $pageset=true; }
else if($page=="ztp_dev_lte") { $page_title = "Edge ZTP - LTE Settings"; $pageset=true; }
else if($page=="ztp_dev_wireless") { $page_title = "Edge ZTP - Wireless Settings"; $pageset=true; }
else if($page=="ztp_dev_sdwan") { $page_title = "Edge ZTP - SD-WAN Settings"; $pageset=true; }
else if($page=="ztp_dev_qos") { $page_title = "Edge ZTP - QoS Settings"; $pageset=true; }
else if($page=="ztp_dev_qos_app_prio") { $page_title = "Edge ZTP - QoS Application Prioritization"; $pageset=true; }
else if($page=="ztp_dev_agg") { $page_title = "Edge ZTP - Link aggregation"; $pageset=true; }
else if($page=="ztp_dev_firmware") { $page_title = "Edge ZTP - Firmware"; $pageset=true; }
else if($page=="ztp_dev_config") { $page_title = "Edge ZTP - Device Config"; $pageset=true; }
else if($page=="ztp_dev_debug_jobs") { $page_title = "Edge ZTP - Engineering Debug - Jobs"; $pageset=true; }
else if($page=="ztp_dev_debug_smoad_device_jobs") { $page_title = "Edge ZTP - Engineering Debug - Dev Jobs"; $pageset=true; }
else if($page=="ztp_dev_debug_smoad_server_jobs") { $page_title = "Edge ZTP - Engineering Debug - Server Jobs"; $pageset=true; }
else if($page=="ztp_dev_status_log_index") { $page_title = "Edge ZTP - Link-Status Log Index"; $pageset=true; }
else if($page=="ztp_dev_status_log") { $page_title = "Edge ZTP - Link-Status Log"; $pageset=true; }
else if($page=="ztp_dev_network_stats_log_index") { $page_title = "Edge ZTP - Network Stats Log Index"; $pageset=true; }
else if($page=="ztp_dev_network_stats_log") { $page_title = "Edge ZTP - Network Stats Log"; $pageset=true; }
else if($page=="ztp_dev_access_log_index") { $page_title = "Edge ZTP - User Access Log Index"; $pageset=true; }
else if($page=="ztp_dev_access_log") { $page_title = "Edge ZTP - User Access Log"; $pageset=true; }
else if($page=="ztp_dev_consolidated_log_index") { $page_title = "Edge ZTP - Consolidated Log Index"; $pageset=true; }
else if($page=="ztp_dev_consolidated_log") { $page_title = "Edge ZTP - Consolidated Log"; $pageset=true; }
else if($page=="ztp_dev_consolidated_report_index") { $page_title = "Edge ZTP - Consolidated Report"; $pageset=true; }
else if($page=="ztp_sds_home") { $page_title = "Gateway ZTP - Home"; $pageset=true; }
else if($page=="ztp_sds_circuits") { $page_title = "Gateway ZTP - Circuits"; $pageset=true; }
else if($page=="ztp_sds_softclient_config") { $page_title = "Gateway ZTP - Soft-client Configuration"; $pageset=true; }
else if($page=="ztp_sds_vlans") { $page_title = "Gateway ZTP - VLAN Settings"; $pageset=true; }
else if($page=="ztp_sds_devices") { $page_title = "Gateway ZTP - Devices"; $pageset=true; }
else if($page=="ztp_sds_debug_jobs") { $page_title = "Gateway ZTP - Engineering Debug - Jobs"; $pageset=true; }
else if($page=="ztp_sds_debug_smoad_server_jobs") { $page_title = "Gateway ZTP - Engineering Debug - Server Jobs"; $pageset=true; }
else if($page=="ztp_sds_dev_cfg") { $page_title = "Gateway ZTP - Device Settings"; $pageset=true; }
else if($page=="ztp_sds_server_cfg") { $page_title = "Gateway ZTP - Server Settings"; $pageset=true; }
else if($page=="ztp_cust_home") { $page_title = "Customer ZTP - Home"; $pageset=true; }
else if($page=="ztp_cust_devices") { $page_title = "Customer ZTP - Devices"; $pageset=true; }

if(!$pageset) { $page="login"; $pageset=true; }

//Content file
if(strpos($page , "ztp_dev_")!== false) 
{ $contentfile=$contentfilepathztpdev."/content_".$page.".php"; 
  $page_title.= " - $G_device_serialnumber - $G_device_details";
  $G_ztp_dev_page=true;
}
else if(strpos($page , "ztp_sds_")!== false) 
{ $contentfile=$contentfilepathztpsds."/content_".$page.".php";
  $page_title.= " - $G_sds_serialnumber - $G_sds_details";
  
  //get server type
  $query = "select type from smoad_sdwan_servers where serialnumber='$G_sds_serialnumber'";
  if($res = $db->query($query)) { while ($row = $res->fetch_assoc()) { $G_sds_type = $row['type']; } }
  
  $G_ztp_sds_page=true; 
}
else if(strpos($page , "ztp_cust_")!== false) 
{ $contentfile=$contentfilepathztpcust."/content_".$page.".php";
  $page_title.= " - $G_cust_name";
  
  $G_ztp_cust_page=true; 
}
else { $contentfile=$contentfilepath."/content_".$page.".php"; }

if($page=="login" || $page=="logout") { include "$contentfile"; } else  { include 'c/c_main.php'; }

//auto redirect to logout page, if the page is not logout page !!
//this is ideal activity timer, setting this to 15 mins  !
if($page!="logout" && $page!="login") { print "<meta HTTP-EQUIV=\"REFRESH\" content=\"900; url=index.php?page=logout\">"; }

mysqli_close($db);
?>

