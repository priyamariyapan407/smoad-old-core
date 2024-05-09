<?php
$_page = $_GET['pagination']; if($_page==null) { $_page=1; }

if($_POST['command']=="del_log") 
{	$_id = $_POST['id']; 
	if($_id>0)
	{	$query = "delete from smoad_fw_log where id=$_id"; 
		$db->query($query);
	}
}

$_date = $_GET['date'];
$date = explode('-', $_date);


$where_clause = " where year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1] ";
$total_items=0; $total_pages=0;
api_ui_pagination_get_total_items_total_pages($db, 'smoad_fw_log', $where_clause, $G_items_per_page, $total_items, $total_pages);
api_ui_pagination_get_pagination_table($db, $_page, $total_pages, "index.php?page=firewall_log&skey=".$session_key."&date=".$_date);

$limitstart = ($_page-1)*$G_items_per_page;

?>

<table class="list_items" style="width:99%;font-size:10px;">
<tr><th>ID</th><th>Type</th><th>Rule ID</th><th>Packet-Count</th><th>Port</th><th>Source-MAC</th><th>Destination-MAC</th><th>Source-IP</th><th>Destination-IP</th>
    <th>Protocol</th><th>Source-Port</th><th>Destination-Port</th><th><center>Action</center></th><th>Reason</th><th>Timestamp</th><th></th></tr> 

<?php

$query = "select id, type, rule_id, pkt_count, port, src_mac, dst_mac, src_ip, dst_ip, proto, src_port, dst_port, action, reason, log_timestamp from 
	       smoad_fw_log $where_clause order by id desc limit $limitstart".",$G_items_per_page "; 
	if($res = $db->query($query))
	{	
	   while($row = $res->fetch_assoc())
		{	$id = $row['id'];
			$type = $row['type'];
			$rule_id = $row['rule_id'];
			$pkt_count = $row['pkt_count'];
			$port = $row['port'];
			$src_mac = $row['src_mac'];
			$dst_mac = $row['dst_mac'];
			$src_ip = $row['src_ip'];
			$dst_ip = $row['dst_ip'];
			$proto = $row['proto'];
			$src_port = $row['src_port'];
			$dst_port = $row['dst_port'];
			$action = $row['action'];
			$reason = $row['reason'];
			$timestamp = $row['log_timestamp'];
			
			$proto=get_proto_byname($proto);
			$src_port=get_port_byname($src_port);
		   $dst_port=get_port_byname($dst_port);
		   
		   if($action=="allow") { $_action="ALLOW"; $bg_style="style=\"color:#2981e4;font-weight:bold;\""; }
			else if($action=="monitor") { $_action="MONITOR"; $bg_style="style=\"color:#4d916a;font-weight:bold;\""; }
			else if($action=="drop") { $_action="DROP"; $bg_style="style=\"color:#D84430;font-weight:bold;\""; }
		   
			print "<tr><td >$id</td>";
			if($type=="user") { print "<td ><img src=\"i/user-red.png\" title=\"User defined\" /></td>"; } 
			else if($type=="ips") { print "<td ><img src=\"i/ai.png\" title=\"IPS (AI)\" /></td>"; } 
			else { print "<td ><img src=\"i/dismiss.png\" title=\"Unknown\" /></td>"; }
			print "<td >$rule_id</td><td >$pkt_count</td>";
			print "<td >$port</td><td >$src_mac</td><td >$dst_mac</td>
					 <td >$src_ip</td><td >$dst_ip</td><td >$proto</td>
			       <td >$src_port</td><td >$dst_port</td>
			       <td $bg_style ><center>$_action</center></td><td >$reason</td><td >$timestamp</td>";
			       
			if($login_type=='root' || $login_type=='admin' || $login_type=='customer')
			{
				print "<td ><form method=\"POST\" action=\"$curr_page\" >
						<input type=\"hidden\" name=\"command\" value=\"del_log\" />
						<input type=\"hidden\" name=\"id\" value=\"$id\" />
						<!--<input type=\"submit\" name=\"submit_ok\" value=\"Delete\" style=\"border:0;\" class=\"a_button_red\" />-->
						<input type=\"image\" src=\"i/trash.png\" alt=\"Delete\" title=\"Delete\" class=\"top_title_icons\" />
						</form></td>";
			}
			else { print "<td $bg_style></td>"; }		
		
			print "</tr>";
		}
	}
	
function get_proto_byname($proto)
{
   if($proto == 1) $proto = "ICMP";
   elseif($proto == 6) $proto = "TCP";
   elseif($proto == 17) $proto = "UDP";
   return $proto;
}	
	
function get_port_byname($port)
{
  switch($port)
  { case "0050": $port = "HTTP"; break;
  	 case "0c38": $port = "HTTP2"; break;
  	 case "1f90": $port = "HTTP3"; break;
  	 case "1f98": $port = "HTTP4"; break;
  	 case "0015": $port = "FTP"; break;
  	 case "0801": $port = "NFS"; break;
  	 case "0cea": $port = "MYSQL"; break;
  	 case "1538": $port = "PGSQL"; break;
  	 case "0016": $port = "SSH"; break;
  	 case "0019": $port = "SMTP"; break;
  	 case "01bb": $port = "SSL"; break;
  	 case "006e": $port = "POP"; break;
  	 case "0035": $port = "DNS"; break;
  	 case "14eb": $port = "LLMNR"; break;
  	 case "076c": $port = "SSDP"; break;
  	 case "0017": $port = "TELNET"; break;
  	 case "008f": $port = "IMAP"; break;
  	 case "03e1": $port = "IMAPS"; break;
  	 case "0185": $port = "LDAP"; break;
  	 case "0058": $port = "KRB"; break;
  	 case "13c4": $port = "SIP"; break;
  	 case "13e2": $port = "SIP2"; break;
  	 case "1f40": $port = "RTP"; break;
  	 case "1f42": $port = "RTP2"; break;
  	 case "1392": $port = "RTP3"; break;
  	 case "1394": $port = "RTP4"; break;
  	 case "1f41": $port = "RTCP"; break;
  	 case "1f43": $port = "RTCP2"; break;
  	 case "1393": $port = "RTCP3"; break;
  	 case "1395": $port = "RTCP4"; break;
  	 case "0d3d": $port = "RDP"; break;
    case "170d": $port = "VNC"; break;
	 case "170e": $port = "VNC2"; break;
  	 case "170f": $port = "VNC3"; break;
    case "1710": $port = "VNC4"; break;
  	 case "c8d5": $port = "TORRENT"; break;
  	 default: $port = "0x".$port; break;
  }
  
  return $port;
}
























?>
</table>
