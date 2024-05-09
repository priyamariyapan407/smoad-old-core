<?php 

 
function api_net_stats_get_disp($value)
{	$unit=" KB";
	if($value>1000000000) { $value=$value/1000000000; $unit=" TB"; }
	if($value>1000000) { $value=$value/1000000; $unit=" GB"; }
	if($value>1000) { $value=$value/1000; $unit=" MB"; }
	$value=number_format($value, 1);
	return $value.$unit;
}
  
function api_net_rate_stats_get_disp($value)
{	$unit=" K";
	$value=$value*8; //convert to bits
	if($value>1000000000) { $value=$value/1000000000; $unit=" T"; }
	if($value>1000000) { $value=$value/1000000; $unit=" G"; }
	if($value>1000) { $value=$value/1000; $unit=" M"; }
	$value=number_format($value, 1);
	return $value.$unit."b/s";
}

function api_ip_addr_get_meta($ipaddr)
{	$ipaddr_meta = json_decode(file_get_contents("http://ipinfo.io/{$ipaddr}/json"));
	$ipaddr_meta_found = false;
	$ipaddr_meta_city = $ipaddr_meta_org = $ipaddr_meta_hostname = null;
	$ipaddr_meta_dump = null;
	if(isset($ipaddr_meta->org)) { $ipaddr_meta_found=true; $ipaddr_meta_dump.=$ipaddr_meta->org; }
	if(isset($ipaddr_meta->city)) { $ipaddr_meta_found=true; $ipaddr_meta_dump.="<br>".$ipaddr_meta->city; }
	if(isset($ipaddr_meta->country)) { $ipaddr_meta_found=true; $ipaddr_meta_dump.=", ".$ipaddr_meta->country; }
	if(isset($ipaddr_meta->hostname)) { $ipaddr_meta_found=true; $ipaddr_meta_dump.="<br>".$ipaddr_meta->hostname; }
	return $ipaddr_meta_dump;
}
?>
