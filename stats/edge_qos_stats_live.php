<!DOCTYPE html>
<html><head>
<style>
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.topnav {
  overflow: hidden;
  background-color: grey;
  width: 25%;
}

.topnav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.topnav a:hover {
  background-color: #ddd;
  color: black;
}

.topnav a.active {
  background-color: #04AA6D;
  color: white;
}
.parent_container {
  /* height:2000px;
  overflow-y:scroll;
  overflow-x:hidden; */
}
canvas {
    height: 300px; /* Set the desired height in pixels */
  }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<body>
<?php
    include('../c/c_db_access.php');
        $serialnumber = htmlspecialchars($_GET["sn"]);
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

   //find GW sn and vxlan_id from EDGE serial
	$gw_serialnumber = $sdwan_server_ipaddr = null;
	$vxlan_id = 0;

	$query = "SELECT sdwan_server_ipaddr FROM smoad_devices WHERE serialnumber=\"$serialnumber\"";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$sdwan_server_ipaddr = $row['sdwan_server_ipaddr'];
		}
	}
    
	if($sdwan_server_ipaddr!=null)
	{	$query = "SELECT serialnumber FROM smoad_sdwan_servers WHERE ipaddr=\"$sdwan_server_ipaddr\"";
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$gw_serialnumber = $row['serialnumber'];
			}
		}
		
		$query = "SELECT vxlan_id FROM smoad_sds_wg_peers WHERE serialnumber=\"$gw_serialnumber\"";
		if($res = $db->query($query))
		{	while($row = $res->fetch_assoc())
			{	$vxlan_id = $row['vxlan_id'];
			}
		}
	}
	
	if($gw_serialnumber==null) { print "<pre>ERROR: not found any matching GW circuits matching these logs !</pre><br>"; }
	
	$where_clause_gw_serial_vxlan_id = " device_serialnumber=\"$gw_serialnumber\" and vxlan_id=$vxlan_id "; 
   // echo $where_clause_gw_serial_vxlan_id;exit;
	$where_clause_interval_24h = " log_timestamp > now() - interval 24 hour ";
	$where_clause_interval_1w = " log_timestamp > now() - interval 1 week ";
	$main_table = " smoad_device_network_qos_stats_log ";

    function get_query_result($columns,$time_interval){
        
        $glbl_where_clause_gw_serial_vxlan_id = $GLOBALS['where_clause_gw_serial_vxlan_id'];
        return "SELECT DATE_FORMAT(log_timestamp, '%Y-%m-%d %H:%i:00') AS log_timestamp_10_mins, $columns FROM smoad_device_network_qos_stats_log  WHERE log_timestamp >= NOW() - $time_interval AND log_timestamp < NOW() AND $glbl_where_clause_gw_serial_vxlan_id GROUP BY log_timestamp, MINUTE(log_timestamp) DIV 10  ORDER BY log_timestamp";
        
      }

    ?>
    <style>


    .btn {
    cursor: pointer;
    background-color: #E9EFF5;
    font-family: Helvetica;
    font-size: 16px;
    padding:8px;
    border: none;

    }

    .btn:disabled {
    cursor: not-allowed;
    background-color: #E0E2E5;
    }
    .graph {
    width:80%;
    }
    .title {
    margin-bottom: 4%;
    }
    @media (min-width:991px)
    {
    .protocol-stat {
        width:40%;
    }

    }

  
    </style>
    <div class="topnav" style="display:none" >
    <a class="active" onclick="myFunction()" >WAN1</a>
    <a onclick="myFunction.call(this.id)">LAN</a>
    <a onclick="myFunction()">LTE1</a>
    <a onclick="myFunction()">LTE2</a>
    </div>
<!-- Graph placeholders -->

        <button onclick="ShowPastWeek()" id="PW" class="btn" >Past Week</button>
        <button onclick="ShowPastDay()" id="PD" class="btn" disabled >Past 24 Hours</button>
        <div id="DisplayText" style= "position: relative;padding-top:30px;font-weight: bold;">Showing: Past 24 Hours</div>
        <div class="parent_container">  
        <div class="row rx_bytes">
            <div class="col-lg-9">
            <h4>RX Bytes</h4>
            </div>    
       </div>
       <div class="row">
            <div class="col-lg-9">
            <b><h6>Application Stats</b></h6>
            </div>    
       </div>
       <div class="row">
            <div class="col-lg-11">
              <canvas id="myChart"></canvas>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-9">
            <b><h6>Protocol Stats</b></h6>
            </div>    
       </div>
        <div class="row">
            <div class="col-lg-10">
              <canvas id="rx-byte-pro-chart"></canvas>
            </div>
        </div>

       
       <div class="row">
            <div class="col-lg-9">
            <h4>RX Packet</h4>
            </div>    
       </div>
   
  
       <div class="row">
            <div class="col-lg-9">
            <b><h6>Application Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-11">
            <canvas id="rx-pkt-pie-chart"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
            <b><h6>Protocol Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-10">
              <canvas id="rx-pkt-pro-pie-chart"></canvas>
            </div>
        </div>
      
        <div class="row">
            <div class="col-lg-9">
            <h4>TX Bytes</h4>
            </div>    
       </div>

      
       <div class="row">
            <div class="col-lg-9">
            <b><h6>Application Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-11">
               <canvas id="tx-bytes-pie-chart"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
            <b><h6>Protocol Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-10">
              <canvas id="tx-bytes-pro-pie-chart"></canvas>
            </div>
        </div>
     
        <div class="row">
            <div class="col-lg-9">
               <h4 >TX Packet </h4>
            </div>    
       </div>

   
  
       <div class="row">
            <div class="col-lg-9">
            <b><h6>Application Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-11">
               <canvas id="tx-pkt-pie-chart"></canvas>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
            <b><h6>Protocol Stats</b></h6>
            </div>    
       </div>
        <div class="row">
        <div class="col-lg-10">
              <canvas id="tx-pkt-pro-pie-chart"></canvas>
            </div>
        </div> 
      
        </div>
         <?php 

      

        $rx_bytes_labl = array();
        $rx_bytes_http = array();
        $rx_bytes_https = array();
        $rx_bytes_iperf = array();
        $rx_bytes_zoom = array();
        $rx_bytes_microsoft_teams = array();
        $rx_bytes_skype = array();
        $rx_bytes_voip = array();
        $rx_bytes_other = array();
        $rx_bytes_tcp = array();
        $rx_bytes_udp = array();
        $rx_bytes_icmp = array();

        // start of rx chart data's with 10 minutes back
        

       $current_time_qry = get_query_result("log_timestamp,sum(http_rx_bytes) as http_rx_bytes, sum(https_rx_bytes) as https_rx_bytes, sum(iperf_rx_bytes) as iperf_rx_bytes, sum(zoom_rx_bytes) as zoom_rx_bytes, sum(microsoft_teams_rx_bytes) as microsoft_teams_rx_bytes,sum(skype_rx_bytes) as skype_rx_bytes, sum(voip_rx_bytes) as voip_rx_bytes, sum(other_rx_bytes) as other_rx_bytes,sum(icmp_rx_bytes) as icmp_rx_bytes,sum(tcp_rx_bytes) as tcp_rx_bytes,sum(udp_rx_bytes) as udp_rx_bytes","INTERVAL 24 HOUR");
    
     
        $res1 = $db->query($current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($rx_bytes_labl,$row2['log_timestamp_10_mins']);
            array_push($rx_bytes_http,$row2['http_rx_bytes']);
            array_push($rx_bytes_https,$row2['https_rx_bytes']);
            array_push($rx_bytes_iperf,$row2['iperf_rx_bytes']);
            array_push($rx_bytes_zoom,$row2['zoom_rx_bytes']);
            array_push($rx_bytes_microsoft_teams,$row2['microsoft_teams_rx_bytes']);
            array_push($rx_bytes_skype,$row2['skype_rx_bytes']);
            array_push($rx_bytes_voip,$row2['voip_rx_bytes']);
            array_push($rx_bytes_other,$row2['other_rx_bytes']);
            array_push($rx_bytes_tcp,$row2['tcp_rx_bytes']);
            array_push($rx_bytes_udp,$row2['udp_rx_bytes']);
            array_push($rx_bytes_icmp,$row2['icmp_rx_bytes']);
        
        } 
        
        $max_rx_types_app = max(max($rx_bytes_http),max($rx_bytes_https),max($rx_bytes_iperf),max($rx_bytes_zoom),max($rx_bytes_microsoft_teams),max($rx_bytes_skype),max($rx_bytes_voip),max($rx_bytes_other));

        $max_rx_types_pro = max(max($rx_bytes_tcp),max($rx_bytes_udp),max($rx_bytes_icmp));

       }
       }else{
        echo "Error in ".$res1."<br>".$db->error;
       } 

       

        $wk_rx_bytes_labl = array();
        $wk_rx_bytes_http = array();
        $wk_rx_bytes_https = array();
        $wk_rx_bytes_iperf = array();
        $wk_rx_bytes_zoom = array();
        $wk_rx_bytes_microsoft_teams = array();
        $wk_rx_bytes_skype = array();
        $wk_rx_bytes_voip = array();
        $wk_rx_bytes_other = array();
        $wk_rx_bytes_tcp = array();
        $wk_rx_bytes_udp = array();
        $wk_rx_bytes_icmp = array();

        // start of rx week chart data's with 10 minutes back

        $wk_current_time_qry = get_query_result("log_timestamp,sum(http_rx_bytes) as http_rx_bytes, sum(https_rx_bytes) as https_rx_bytes, sum(iperf_rx_bytes) as iperf_rx_bytes, sum(zoom_rx_bytes) as zoom_rx_bytes, sum(microsoft_teams_rx_bytes) as microsoft_teams_rx_bytes,sum(skype_rx_bytes) as skype_rx_bytes, sum(voip_rx_bytes) as voip_rx_bytes, sum(other_rx_bytes) as other_rx_bytes,sum(icmp_rx_bytes) as icmp_rx_bytes,sum(tcp_rx_bytes) as tcp_rx_bytes,sum(udp_rx_bytes) as udp_rx_bytes","INTERVAL 1 WEEK");


        $res1 = $db->query($wk_current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {

        while($row2 = $res1->fetch_assoc())
        {
            array_push($wk_rx_bytes_labl,$row2['log_timestamp_10_mins']);
            array_push($wk_rx_bytes_http,$row2['http_rx_bytes']);
            array_push($wk_rx_bytes_https,$row2['https_rx_bytes']);
            array_push($wk_rx_bytes_iperf,$row2['iperf_rx_bytes']);
            array_push($wk_rx_bytes_zoom,$row2['zoom_rx_bytes']);
            array_push($wk_rx_bytes_microsoft_teams,$row2['microsoft_teams_rx_bytes']);
            array_push($wk_rx_bytes_skype,$row2['skype_rx_bytes']);
            array_push($wk_rx_bytes_voip,$row2['voip_rx_bytes']);
            array_push($wk_rx_bytes_other,$row2['other_rx_bytes']);
            array_push($wk_rx_bytes_tcp,$row2['tcp_rx_bytes']);
            array_push($wk_rx_bytes_udp,$row2['udp_rx_bytes']);
            array_push($wk_rx_bytes_icmp,$row2['icmp_rx_bytes']);
        }
       
        $wk_max_rx_types_app = max(max($wk_rx_bytes_http),max($wk_rx_bytes_https),max($wk_rx_bytes_iperf),max($wk_rx_bytes_zoom),max($wk_rx_bytes_microsoft_teams),max($wk_rx_bytes_skype),max($wk_rx_bytes_voip),max($wk_rx_bytes_other));

        $wk_max_rx_types_pro = max(max($wk_rx_bytes_tcp),max($wk_rx_bytes_udp),max($wk_rx_bytes_icmp));
       }  
       }else{
            echo "Error in ".$res1."<br>".$db->error;
        } 
        

        // end of rx week chart data's with 10 minutes back
      
        $rx_pkt_labl = array();
        $rx_pkt_http = array();
        $rx_pkt_https = array();
        $rx_pkt_iperf = array();
        $rx_pkt_zoom = array();
        $rx_pkt_microsoft_teams = array();
        $rx_pkt_skype = array();
        $rx_pkt_voip = array();
        $rx_pkt_other = array();
        $rx_pkt_tcp = array();
        $rx_pkt_udp = array();
        $rx_pkt_icmp = array();

        // start of rx pkt chart data's with 10 minutes back

        $current_time_qry = get_query_result("log_timestamp,sum(http_rx_pkts) as http_rx_pkts,sum(https_rx_pkts) as https_rx_pkts,sum(iperf_rx_pkts) as iperf_rx_pkts,sum(zoom_rx_pkts) as zoom_rx_pkts,sum(microsoft_teams_rx_pkts) as microsoft_teams_rx_pkts,sum(skype_rx_pkts) as skype_rx_pkts,sum(voip_rx_pkts) as voip_rx_pkts,sum(other_rx_pkts) as other_rx_pkts,sum(icmp_rx_pkts) as icmp_rx_pkts,sum(tcp_rx_pkts) as tcp_rx_pkts,sum(udp_rx_pkts) as udp_rx_pkts","INTERVAL 24 HOUR");

   
       
        $res1 = $db->query($current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($rx_pkt_labl,$row2['log_timestamp_10_mins']);
            array_push($rx_pkt_http,$row2['http_rx_pkts']);
            array_push($rx_pkt_https,$row2['https_rx_pkts']);
            array_push($rx_pkt_iperf,$row2['iperf_rx_pkts']);
            array_push($rx_pkt_zoom,$row2['zoom_rx_pkts']);
            array_push($rx_pkt_microsoft_teams,$row2['microsoft_teams_rx_pkts']);
            array_push($rx_pkt_skype,$row2['skype_rx_pkts']);
            array_push($rx_pkt_voip,$row2['voip_rx_pkts']);
            array_push($rx_pkt_other,$row2['other_rx_pkts']);
            array_push($rx_pkt_tcp,$row2['tcp_rx_pkts']);
            array_push($rx_pkt_udp,$row2['udp_rx_pkts']);
            array_push($rx_pkt_icmp,$row2['icmp_rx_pkts']);
          
        } 
        } 
        }else{
                echo "Error in ".$res1."<br>".$db->error;
        }

        // end of rx pkt chart data's with 10 minutes back
        
        // start of week rx pkt chart data's with 10 minutes back

        $wk_rx_pkt_labl = array();
        $wk_rx_pkt_http = array();
        $wk_rx_pkt_https = array();
        $wk_rx_pkt_iperf = array();
        $wk_rx_pkt_zoom = array();
        $wk_rx_pkt_microsoft_teams = array();
        $wk_rx_pkt_skype = array();
        $wk_rx_pkt_voip = array();
        $wk_rx_pkt_other = array();
        $wk_rx_pkt_tcp = array();
        $wk_rx_pkt_udp = array();
        $wk_rx_pkt_icmp = array();

        $wk_current_time_qry = get_query_result("log_timestamp,sum(http_rx_pkts) as http_rx_pkts,sum(https_rx_pkts) as https_rx_pkts,sum(iperf_rx_pkts) as iperf_rx_pkts,sum(zoom_rx_pkts) as zoom_rx_pkts,sum(microsoft_teams_rx_pkts) as microsoft_teams_rx_pkts,sum(skype_rx_pkts) as skype_rx_pkts,sum(voip_rx_pkts) as voip_rx_pkts,sum(other_rx_pkts) as other_rx_pkts,sum(icmp_rx_pkts) as icmp_rx_pkts,sum(tcp_rx_pkts) as tcp_rx_pkts,sum(udp_rx_pkts) as udp_rx_pkts","INTERVAL 1 WEEK");

       
        $res1 = $db->query($wk_current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($wk_rx_pkt_labl,$row2['log_timestamp_10_mins']);
            array_push($wk_rx_pkt_http,$row2['http_rx_pkts']);
            array_push($wk_rx_pkt_https,$row2['https_rx_pkts']);
            array_push($wk_rx_pkt_iperf,$row2['iperf_rx_pkts']);
            array_push($wk_rx_pkt_zoom,$row2['zoom_rx_pkts']);
            array_push($wk_rx_pkt_microsoft_teams,$row2['microsoft_teams_rx_pkts']);
            array_push($wk_rx_pkt_skype,$row2['skype_rx_pkts']);
            array_push($wk_rx_pkt_voip,$row2['voip_rx_pkts']);
            array_push($wk_rx_pkt_other,$row2['other_rx_pkts']);
            array_push($wk_rx_pkt_tcp,$row2['tcp_rx_pkts']);
            array_push($wk_rx_pkt_udp,$row2['udp_rx_pkts']);
            array_push($wk_rx_pkt_icmp,$row2['icmp_rx_pkts']);
            
        } 
       } 
       }else{
            echo "Error in ".$res1."<br>".$db->error;
       }

        // end of week rx pkt chart data's with 10 minutes back



        $tx_byte_labl = array();
        $tx_byte_http = array();
        $tx_byte_https = array();
        $tx_byte_iperf = array();
        $tx_byte_zoom = array();
        $tx_byte_microsoft_teams = array();
        $tx_byte_skype = array();
        $tx_byte_voip = array();
        $tx_byte_other = array();
        $tx_byte_tcp = array();
        $tx_byte_udp = array();
        $tx_byte_icmp = array();

        // start of tx chart data's with 10 minutes back

        $current_time_qry = get_query_result("log_timestamp,sum(http_tx_bytes) as http_tx_bytes,sum(https_tx_bytes) as https_tx_bytes,sum(iperf_tx_bytes) as iperf_tx_bytes,sum(zoom_tx_bytes) as zoom_tx_bytes,sum(microsoft_teams_tx_bytes) as microsoft_teams_tx_bytes,sum(skype_tx_bytes) as skype_tx_bytes,sum(voip_tx_bytes) as voip_tx_bytes,sum(other_tx_bytes) as other_tx_bytes,sum(icmp_tx_bytes) as icmp_tx_bytes,sum(tcp_tx_bytes) as tcp_tx_bytes,sum(udp_tx_bytes) as udp_tx_bytes","INTERVAL 24 HOUR");

      

       
        $res1 = $db->query($current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($tx_byte_labl,$row2['log_timestamp_10_mins']);
            array_push($tx_byte_http,$row2['http_tx_bytes']);
            array_push($tx_byte_https,$row2['https_tx_bytes']);
            array_push($tx_byte_iperf,$row2['iperf_tx_bytes']);
            array_push($tx_byte_zoom,$row2['zoom_tx_bytes']);
            array_push($tx_byte_microsoft_teams,$row2['microsoft_teams_tx_bytes']);
            array_push($tx_byte_skype,$row2['skype_tx_bytes']);
            array_push($tx_byte_voip,$row2['voip_tx_bytes']);
            array_push($tx_byte_other,$row2['other_tx_bytes']);
            array_push($tx_byte_tcp,$row2['tcp_tx_bytes']);
            array_push($tx_byte_udp,$row2['udp_tx_bytes']);
            array_push($tx_byte_icmp,$row2['icmp_tx_bytes']);
           
        } 
        $max_tx_types_app = max(max($tx_byte_http),max($tx_byte_https),max($tx_byte_iperf),max($tx_byte_zoom),max($tx_byte_microsoft_teams),max($tx_byte_skype),max($tx_byte_voip),max($tx_byte_other));

        $max_tx_types_pro = max(max($tx_byte_tcp),max($tx_byte_udp),max($tx_byte_icmp));
       } 
       }else{
        echo "Error in ".$res1."<br>".$db->error;
       }
        // end of tx chart data's with 10 minutes back

       

       

        // start of week tx chart data's with 10 minutes back

        $wk_tx_byte_labl = array();
        $wk_tx_byte_http = array();
        $wk_tx_byte_https = array();
        $wk_tx_byte_iperf = array();
        $wk_tx_byte_zoom = array();
        $wk_tx_byte_microsoft_teams = array();
        $wk_tx_byte_skype = array();
        $wk_tx_byte_voip = array();
        $wk_tx_byte_other = array();
        $wk_tx_byte_tcp = array();
        $wk_tx_byte_udp = array();
        $wk_tx_byte_icmp = array();    
       



        $current_time_qry = get_query_result("log_timestamp,sum(http_tx_bytes) as http_tx_bytes,sum(https_tx_bytes) as https_tx_bytes,sum(iperf_tx_bytes) as iperf_tx_bytes,sum(zoom_tx_bytes) as zoom_tx_bytes,sum(microsoft_teams_tx_bytes) as microsoft_teams_tx_bytes,sum(skype_tx_bytes) as skype_tx_bytes,sum(voip_tx_bytes) as voip_tx_bytes,sum(other_tx_bytes) as other_tx_bytes,sum(icmp_tx_bytes) as icmp_tx_bytes,sum(tcp_tx_bytes) as tcp_tx_bytes,sum(udp_tx_bytes) as udp_tx_bytes","INTERVAL 1 WEEK");


       
        $res1 = $db->query($current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($wk_tx_byte_labl,$row2['log_timestamp_10_mins']);
            array_push($wk_tx_byte_http,$row2['http_tx_bytes']);
            array_push($wk_tx_byte_https,$row2['https_tx_bytes']);
            array_push($wk_tx_byte_iperf,$row2['iperf_tx_bytes']);
            array_push($wk_tx_byte_zoom,$row2['zoom_tx_bytes']);
            array_push($wk_tx_byte_microsoft_teams,$row2['microsoft_teams_tx_bytes']);
            array_push($wk_tx_byte_skype,$row2['skype_tx_bytes']);
            array_push($wk_tx_byte_voip,$row2['voip_tx_bytes']);
            array_push($wk_tx_byte_other,$row2['other_tx_bytes']);
            array_push($wk_tx_byte_tcp,$row2['tcp_tx_bytes']);
            array_push($wk_tx_byte_udp,$row2['udp_tx_bytes']);
            array_push($wk_tx_byte_icmp,$row2['icmp_tx_bytes']);
         
        } 
        $wk_max_tx_types_app = max(max($wk_tx_byte_http),max($wk_tx_byte_https),max($wk_tx_byte_iperf),max($wk_tx_byte_zoom),max($wk_tx_byte_microsoft_teams),max($wk_tx_byte_skype),max($wk_tx_byte_voip),max($wk_tx_byte_other));

        $wk_max_tx_types_pro = max(max($wk_tx_byte_tcp),max($wk_tx_byte_udp),max($wk_tx_byte_icmp));
       } 
       }else{
        echo "Error in ".$res1."<br>".$db->error;
       }
        // end of week tx chart data's with 10 minutes back

       

        


        // start of tx pkt chart data's with 10 minutes back

        $tx_pkt_labl = array();
        $tx_pkt_http = array();
        $tx_pkt_https = array();
        $tx_pkt_iperf = array();
        $tx_pkt_zoom = array();
        $tx_pkt_microsoft_teams = array();
        $tx_pkt_skype = array();
        $tx_pkt_voip = array();
        $tx_pkt_other = array();
        $tx_pkt_tcp = array();
        $tx_pkt_udp = array();
        $tx_pkt_icmp = array();

    

       $current_time_qry = get_query_result("log_timestamp,sum(http_tx_pkts) as http_tx_pkts,sum(https_tx_pkts) as https_tx_pkts,sum(iperf_tx_pkts) as iperf_tx_pkts,sum(zoom_tx_pkts) as zoom_tx_pkts,sum(microsoft_teams_tx_pkts) as microsoft_teams_tx_pkts,sum(skype_tx_pkts) as skype_tx_pkts,sum(voip_tx_pkts) as voip_tx_pkts,sum(other_tx_pkts) as other_tx_pkts,sum(icmp_tx_pkts) as icmp_tx_pkts,sum(tcp_tx_pkts) as tcp_tx_pkts,sum(udp_tx_pkts) as udp_tx_pkts","INTERVAL 24 HOUR");


       
        $res1 = $db->query($current_time_qry);
        if($res1){
        if ($res1->num_rows > 0)
        {
        while($row2 = $res1->fetch_assoc())
        {
            array_push($tx_pkt_labl,$row2['log_timestamp_10_mins']);
            array_push($tx_pkt_http,$row2['http_tx_pkts']);
            array_push($tx_pkt_https,$row2['https_tx_pkts']);
            array_push($tx_pkt_iperf,$row2['iperf_tx_pkts']);
            array_push($tx_pkt_zoom,$row2['zoom_tx_pkts']);
            array_push($tx_pkt_microsoft_teams,$row2['microsoft_teams_tx_pkts']);
            array_push($tx_pkt_skype,$row2['skype_tx_pkts']);
            array_push($tx_pkt_voip,$row2['voip_tx_pkts']);
            array_push($tx_pkt_other,$row2['other_tx_pkts']);
            array_push($tx_pkt_tcp,$row2['tcp_tx_pkts']);
            array_push($tx_pkt_udp,$row2['udp_tx_pkts']);
            array_push($tx_pkt_icmp,$row2['icmp_tx_pkts']);
            
        }
       } 
       }else{
        echo "Error in ".$res1."<br>".$db->error;
       }

        // end of tx pkt chart data's with 10 minutes back


        // start of week tx pkt chart data's with 10 minutes back

            $wk_tx_pkt_labl = array();
            $wk_tx_pkt_http = array();
            $wk_tx_pkt_https = array();
            $wk_tx_pkt_iperf = array();
            $wk_tx_pkt_zoom = array();
            $wk_tx_pkt_microsoft_teams = array();
            $wk_tx_pkt_skype = array();
            $wk_tx_pkt_voip = array();
            $wk_tx_pkt_other = array();
            $wk_tx_pkt_tcp = array();
            $wk_tx_pkt_udp = array();
            $wk_tx_pkt_icmp = array();
    
            

            $wk_current_time_qry = get_query_result("log_timestamp,sum(http_tx_pkts) as http_tx_pkts,sum(https_tx_pkts) as https_tx_pkts,sum(iperf_tx_pkts) as iperf_tx_pkts,sum(zoom_tx_pkts) as zoom_tx_pkts,sum(microsoft_teams_tx_pkts) as microsoft_teams_tx_pkts,sum(skype_tx_pkts) as skype_tx_pkts,sum(voip_tx_pkts) as voip_tx_pkts,sum(other_tx_pkts) as other_tx_pkts,sum(icmp_tx_pkts) as icmp_tx_pkts,sum(tcp_tx_pkts) as tcp_tx_pkts,sum(udp_tx_pkts) as udp_tx_pkts","INTERVAL 1 WEEK");
    
    
            
            $res1 = $db->query($wk_current_time_qry);
            if($res1){
            if ($res1->num_rows > 0)
            {
            while($row2 = $res1->fetch_assoc())
            {
                array_push($wk_tx_pkt_labl,$row2['log_timestamp_10_mins']);
                array_push($wk_tx_pkt_http,$row2['http_tx_pkts']);
                array_push($wk_tx_pkt_https,$row2['https_tx_pkts']);
                array_push($wk_tx_pkt_iperf,$row2['iperf_tx_pkts']);
                array_push($wk_tx_pkt_zoom,$row2['zoom_tx_pkts']);
                array_push($wk_tx_pkt_microsoft_teams,$row2['microsoft_teams_tx_pkts']);
                array_push($wk_tx_pkt_skype,$row2['skype_tx_pkts']);
                array_push($wk_tx_pkt_voip,$row2['voip_tx_pkts']);
                array_push($wk_tx_pkt_other,$row2['other_tx_pkts']);
                array_push($wk_tx_pkt_tcp,$row2['tcp_tx_pkts']);
                array_push($wk_tx_pkt_udp,$row2['udp_tx_pkts']);
                array_push($wk_tx_pkt_icmp,$row2['icmp_tx_pkts']);
               
            }
            } 
            }else{
                echo "Error in ".$res1."<br>".$db->error;
            }
    
        // end of week tx pkt chart data's with 10 minutes back
    
        $max_pro_unit_name = array();
        $wk_max_pro_unit_name = array();
        $max_app_unit_name = array();
        $wk_max_app_unit_name = array();

      

       if(isset($max_rx_types_app) || isset($max_tx_types_app)){
        $app_max = max($max_rx_types_app,$max_tx_types_app);
        if($app_max > 1100) {

            for($c=0;$c <= 10; $c++){
                $max_app_unit_name[$c]  = 'Kb' ;
            }

            for ($i = 0; $i < count($rx_bytes_http); $i++) {
                if(!empty($rx_bytes_http[$i])){
                    $rx_bytes_http[$i] = round($rx_bytes_http[$i] / 1000,2) ;
                    
                }
            }

            for ($j = 0; $j < count($rx_bytes_https); $j++) {
                if(!empty($rx_bytes_https[$j])){
                $rx_bytes_https[$j] = round($rx_bytes_https[$j] / 1000 ,2);
                }
            }
            
            for ($k = 0; $k < count($rx_bytes_iperf); $k++) {
                if(!empty($rx_bytes_iperf[$k])){
                $rx_bytes_iperf[$k] = round($rx_bytes_iperf[$k] / 1000,2) ;
                }
            }

            for ($l = 0; $l < count($rx_bytes_zoom); $l++) {
                if(!empty($rx_bytes_zoom[$l])){
                $rx_bytes_zoom[$l] = round($rx_bytes_zoom[$l] / 1000 ,2);
                }
            }

            for ($m = 0; $m < count($rx_bytes_microsoft_teams); $m++) {
                if(!empty($rx_bytes_microsoft_teams[$m])){
                $rx_bytes_microsoft_teams[$m] = round($rx_bytes_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($rx_bytes_skype); $n++) {
                if(!empty($rx_bytes_skype[$n])){
                $rx_bytes_skype[$n] = round($rx_bytes_skype[$n] / 1000,2) ;
                }
            }

            for ($o = 0; $o < count($rx_bytes_voip); $o++) {
                if(!empty($rx_bytes_voip[$o])){
                $rx_bytes_voip[$o] = round($rx_bytes_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($rx_bytes_other); $p++) {
                if(!empty($rx_bytes_other[$p])){
                $rx_bytes_other[$p] = round($rx_bytes_other[$p] / 1000 ,2);
                }
            }
            for ($i = 0; $i < count($tx_byte_http); $i++) {
                if(!empty($tx_byte_http[$i])){
                    $tx_byte_http[$i] = round($tx_byte_http[$i] / 1000,2) ;
                }
            }

            for ($j = 0; $j < count($tx_byte_https); $j++) {
                if(!empty($tx_byte_https[$j])){
                $tx_byte_https[$j] = round($tx_byte_https[$j] / 1000,2) ;
                }
            }
            
            for ($k = 0; $k < count($tx_byte_iperf); $k++) {
                if(!empty($tx_byte_iperf[$k])){
                $tx_byte_iperf[$k] = round($tx_byte_iperf[$k] / 1000 ,2);
                }
            }

            for ($l = 0; $l < count($tx_byte_zoom); $l++) {
                if(!empty($tx_byte_zoom[$l])){
                $tx_byte_zoom[$l] = round($tx_byte_zoom[$l] / 1000 ,2);
                }
            }

            for ($m = 0; $m < count($tx_byte_microsoft_teams); $m++) {
                if(!empty($tx_byte_microsoft_teams[$m])){
                $tx_byte_microsoft_teams[$m] = round($tx_byte_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($tx_byte_skype); $n++) {
                if(!empty($tx_byte_skype[$n])){
                $tx_byte_skype[$n] = round($tx_byte_skype[$n] / 1000,2) ;
                }
            }

            for ($o = 0; $o < count($tx_byte_voip); $o++) {
                if(!empty($tx_byte_voip[$o])){
                $tx_byte_voip[$o] = round($tx_byte_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($tx_byte_other); $p++) {
                if(!empty($tx_byte_other[$p])){
                $tx_byte_other[$p] = round($tx_byte_other[$p] / 1000,2);
                }
               
            }
            $app_max /= 1000;
        }
        if($app_max>1100) {
            for($c=0;$c <= 10; $c++){
                $max_app_unit_name[$c]  = 'Mb' ;
            }
            // $max_rx_bytes_pro_unit_number /= 1000;
            for ($i = 0; $i < count($rx_bytes_http); $i++) {
                if(!empty($rx_bytes_http[$i])){
                $rx_bytes_http[$i] = round($rx_bytes_http[$i] / 1000,2) ;
                }
            }

            for ($q = 0; $q < count($rx_bytes_https); $q++) {
                if(!empty($rx_bytes_https[$i])){
                $rx_bytes_https[$q] = round($rx_bytes_https[$q] / 1000,2) ;
                }
            }
            
            for ($r = 0; $r < count($rx_bytes_iperf); $r++) {
                if(!empty($rx_bytes_iperf[$r])){
                $rx_bytes_iperf[$r] = round($rx_bytes_iperf[$r] / 1000,2) ;
                }
            }

            for ($s = 0; $s < count($rx_bytes_zoom); $s++) {
                if(!empty($rx_bytes_zoom[$s])){
                $rx_bytes_zoom[$s] = round($rx_bytes_zoom[$s] / 1000,2);
                }
            }

            for ($t = 0; $t < count($rx_bytes_microsoft_teams); $t++) {
                if(!empty($rx_bytes_microsoft_teams[$t])){
                $rx_bytes_microsoft_teams[$t] = round($rx_bytes_microsoft_teams[$t] / 1000,2) ;
                }
            }
            
            for ($u = 0; $u < count($rx_bytes_skype); $u++) {
                if(!empty($rx_bytes_skype[$u])){
                $rx_bytes_skype[$u] = round($rx_bytes_skype[$u] / 1000,2) ;
                }
            }

            for ($u = 0; $u < count($rx_bytes_voip); $u++) {
                if(!empty($rx_bytes_voip[$u])){
                $rx_bytes_voip[$u] = round($rx_bytes_voip[$u] / 1000,2) ;
                }
            }

            for ($u = 0; $u < count($rx_bytes_other); $u++) {
                if(!empty($rx_bytes_other[$u])){
                $rx_bytes_other[$u] = round($rx_bytes_other[$u] / 1000,2) ;
                }
            }

            for ($i = 0; $i < count($tx_byte_http); $i++) {
                if(!empty($tx_byte_http[$i])){
                    $tx_byte_http[$i] = round($tx_byte_http[$i] / 1000,2) ;
                }
            }

            for ($j = 0; $j < count($tx_byte_https); $j++) {
                if(!empty($tx_byte_https[$j])){
                $tx_byte_https[$j] = round($tx_byte_https[$j] / 1000,2) ;
                }
            }
            
            for ($k = 0; $k < count($tx_byte_iperf); $k++) {
                if(!empty($tx_byte_iperf[$k])){
                $tx_byte_iperf[$k] = round($tx_byte_iperf[$k] / 1000,2) ;
                }
            }

            for ($l = 0; $l < count($tx_byte_zoom); $l++) {
                if(!empty($tx_byte_zoom[$l])){
                $tx_byte_zoom[$l] = round($tx_byte_zoom[$l] / 1000,2) ;
                }
            }

            for ($m = 0; $m < count($tx_byte_microsoft_teams); $m++) {
                if(!empty($tx_byte_microsoft_teams[$m])){
                $tx_byte_microsoft_teams[$m] = round($tx_byte_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($tx_byte_skype); $n++) {
                if(!empty($tx_byte_skype[$n])){
                $tx_byte_skype[$n] = round( $tx_byte_skype[$n] / 1000 ,2) ;
                }
            }

            for ($o = 0; $o < count($tx_byte_voip); $o++) {
                if(!empty($tx_byte_voip[$o])){
                $tx_byte_voip[$o] = round( $tx_byte_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($tx_byte_other); $p++) {
                if(!empty($tx_byte_other[$p])){
                $tx_byte_other[$p] = round($tx_byte_other[$p] / 1000,2) ;
                }
            }
        }
       }

       if(isset($max_rx_types_pro) || isset($max_tx_types_pro)) {
        $pro_max = max($max_rx_types_pro,$max_tx_types_pro);
        if($pro_max > 1100) {

            for($c=0;$c <= 10; $c++){
                $max_pro_unit_name[$c]  = 'Kb' ;
            }

            for ($v = 0; $v < count($rx_bytes_tcp); $v++) {
                if(!empty($rx_bytes_tcp[$v])){
                $rx_bytes_tcp[$v] = round($rx_bytes_tcp[$v] / 1000,2) ;
                }
            }

            for ($w = 0; $w < count($rx_bytes_udp); $w++) {
                if(!empty($rx_bytes_udp[$w])){
                $rx_bytes_udp[$w] = round($rx_bytes_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($rx_bytes_icmp); $x++) {
                if(!empty($rx_bytes_icmp[$x])){
                $rx_bytes_icmp[$x] = round($rx_bytes_icmp[$x] / 1000,2) ;
                }
            }

            for ($v = 0; $v < count($tx_byte_tcp); $v++) {
                if(!empty($tx_byte_tcp[$v])){
                $tx_byte_tcp[$v] = round($tx_byte_tcp[$v] / 1000,2) ;
                }
            }

            for ($w = 0; $w < count($tx_byte_udp); $w++) {
                if(!empty($tx_byte_udp[$w])){
                $tx_byte_udp[$w] = round($tx_byte_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($tx_byte_icmp); $x++) {
                if(!empty($tx_byte_icmp[$x])){
                $tx_byte_icmp[$x] = round($tx_byte_icmp[$x] / 1000,2) ;
                }
            }

            $pro_max /= 1000;
        }
        if($pro_max>1100) {
            for($c=0;$c <= 10; $c++){
                $max_pro_unit_name[$c]  = 'Mb' ;
            }
            // $max_rx_bytes_pro_unit_number /= 1000;
            for ($y = 0; $y < count($rx_bytes_tcp); $y++) {
                if(!empty($rx_bytes_tcp[$y])){
                $rx_bytes_tcp[$y] = round($rx_bytes_tcp[$y] / 1000,2) ;
                }
            }

            for ($z = 0; $z < count($rx_bytes_udp); $z++) {
                if(!empty($rx_bytes_udp[$z])){
                $rx_bytes_udp[$z] = round($rx_bytes_udp[$z] / 1000,2) ;
                }
            }
            
            for ($a = 0; $a < count($rx_bytes_icmp); $a++) {
                if(!empty($rx_bytes_icmp[$a])){
                $rx_bytes_icmp[$a] = round($rx_bytes_icmp[$a] / 1000,2) ;
                }
            }

            for ($v = 0; $v < count($tx_byte_tcp); $v++) {
                if(!empty($tx_byte_tcp[$v])){
                $tx_byte_tcp[$v] = round($tx_byte_tcp[$v] / 1000 ,2);
                }
            }

            for ($w = 0; $w < count($tx_byte_udp); $w++) {
                if(!empty($tx_byte_udp[$w])){
                $tx_byte_udp[$w] = round($tx_byte_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($tx_byte_icmp); $x++) {
                if(!empty($tx_byte_icmp[$x])){
                $tx_byte_icmp[$x] = round($tx_byte_icmp[$x] / 1000,2) ;
                }
               
            }

            
        }
       }
             

        if(isset($wk_max_rx_types_app) || isset($wk_max_tx_types_app)) {
            $wk_app_max = max($wk_max_rx_types_app,$wk_max_tx_types_app);
        if($wk_app_max > 1100) {
            for($c=0;$c <= 10; $c++){
                $wk_max_app_unit_name[$c]  = 'Kb' ;
            }

            for ($i = 0; $i < count($wk_rx_bytes_http); $i++) {
                if(!empty($wk_rx_bytes_http[$i])){
                    $wk_rx_bytes_http[$i] = round($wk_rx_bytes_http[$i] / 1000,2) ;
                }
               
            }

            for ($j = 0; $j < count($wk_rx_bytes_https); $j++) {
                if(!empty($wk_rx_bytes_https[$j])){
                $wk_rx_bytes_https[$j] = round($wk_rx_bytes_https[$j] / 1000 ,2);
                }
            }
            
            for ($k = 0; $k < count($wk_rx_bytes_iperf); $k++) {
                if(!empty($wk_rx_bytes_iperf[$k])){
                $wk_rx_bytes_iperf[$k] = round($wk_rx_bytes_iperf[$k] / 1000,2) ;
                }
            }

            for ($l = 0; $l < count($wk_rx_bytes_zoom); $l++) {
                if(!empty($wk_rx_bytes_zoom[$l])){
                $wk_rx_bytes_zoom[$l] = round($wk_rx_bytes_zoom[$l] / 1000 ,2);
                }
            }

            for ($m = 0; $m < count($wk_rx_bytes_microsoft_teams); $m++) {
                if(!empty($wk_rx_bytes_microsoft_teams[$m])){
                $wk_rx_bytes_microsoft_teams[$m] = round($wk_rx_bytes_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($wk_rx_bytes_skype); $n++) {
                if(!empty($wk_rx_bytes_skype[$n])){
                $wk_rx_bytes_skype[$n] = round($wk_rx_bytes_skype[$n] / 1000,2) ;
                }
            }

            for ($o = 0; $o < count($wk_rx_bytes_voip); $o++) {
                if(!empty($wk_rx_bytes_voip[$o])){
                $wk_rx_bytes_voip[$o] = round($wk_rx_bytes_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($wk_rx_bytes_other); $p++) {
                if(!empty($wk_rx_bytes_other[$p])){
                $wk_rx_bytes_other[$p] = round($wk_rx_bytes_other[$p] / 1000 ,2);
                }            }
            for ($i = 0; $i < count($wk_tx_byte_http); $i++) {
                if(!empty($wk_tx_byte_http[$i])){
                    $wk_tx_byte_http[$i] = round($wk_tx_byte_http[$i] / 1000,2) ;
                }
            }

            for ($j = 0; $j < count($wk_tx_byte_https); $j++) {
                if(!empty($wk_tx_byte_https[$j])){
                $wk_tx_byte_https[$j] = round($wk_tx_byte_https[$j] / 1000,2) ;
                }
            }
            
            for ($k = 0; $k < count($wk_tx_byte_iperf); $k++) {
                if(!empty($wk_tx_byte_iperf[$k])){
                $wk_tx_byte_iperf[$k] = round($wk_tx_byte_iperf[$k] / 1000 ,2);
                }
            }

            for ($l = 0; $l < count($wk_tx_byte_zoom); $l++) {
                if(!empty($wk_tx_byte_zoom[$l])){
                $wk_tx_byte_zoom[$l] = round($wk_tx_byte_zoom[$l] / 1000 ,2);
                }
            }

            for ($m = 0; $m < count($wk_tx_byte_microsoft_teams); $m++) {
                if(!empty($wk_tx_byte_microsoft_teams[$m])){
                $wk_tx_byte_microsoft_teams[$m] = round($wk_tx_byte_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($wk_tx_byte_skype); $n++) {
                if(!empty($wk_tx_byte_skype[$n])){
                $wk_tx_byte_skype[$n] = round($wk_tx_byte_skype[$n] / 1000,2) ;
                }
            }

            for ($o = 0; $o < count($wk_tx_byte_voip); $o++) {
                if(!empty($wk_tx_byte_voip[$o])){
                $wk_tx_byte_voip[$o] = round($wk_tx_byte_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($wk_tx_byte_other); $p++) {
                if(!empty($wk_tx_byte_other[$p])){
                $wk_tx_byte_other[$p] = round($wk_tx_byte_other[$p] / 1000,2);
                }
            }
            $wk_app_max /= 1000;
        }
        if($wk_app_max>1100) {

            for($c=0;$c <= 10; $c++){
                $wk_max_app_unit_name[$c]  = 'Mb' ;
            }

            for ($i = 0; $i < count($wk_rx_bytes_http); $i++) {
                if(!empty($wk_rx_bytes_http[$i])){
                $wk_rx_bytes_http[$i] = round($wk_rx_bytes_http[$i] / 1000,2) ;
                }
               
            }

            for ($q = 0; $q < count($wk_rx_bytes_https); $q++) {
                if(!empty($wk_rx_bytes_https[$i])){
                $wk_rx_bytes_https[$q] = round($wk_rx_bytes_https[$q] / 1000,2) ;
                }
            }
            
            for ($r = 0; $r < count($wk_rx_bytes_iperf); $r++) {
                if(!empty($wk_rx_bytes_iperf[$r])){
                $wk_rx_bytes_iperf[$r] = round($wk_rx_bytes_iperf[$r] / 1000,2) ;
                }
            }

            for ($s = 0; $s < count($wk_rx_bytes_zoom); $s++) {
                if(!empty($wk_rx_bytes_zoom[$s])){
                $wk_rx_bytes_zoom[$s] = round($wk_rx_bytes_zoom[$s] / 1000,2) ;
                }
            }

            for ($t = 0; $t < count($wk_rx_bytes_microsoft_teams); $t++) {
                if(!empty($wk_rx_bytes_microsoft_teams[$t])){
                $wk_rx_bytes_microsoft_teams[$t] = round($wk_rx_bytes_microsoft_teams[$t] / 1000,2) ;
                }
            }
            
            for ($u = 0; $u < count($wk_rx_bytes_skype); $u++) {
                if(!empty($wk_rx_bytes_skype[$u])){
                $wk_rx_bytes_skype[$u] = round($wk_rx_bytes_skype[$u] / 1000,2) ;
                }
            }

            for ($u = 0; $u < count($wk_rx_bytes_voip); $u++) {
                if(!empty($wk_rx_bytes_voip[$u])){
                $wk_rx_bytes_voip[$u] = round($wk_rx_bytes_voip[$u] / 1000,2) ;
                }
            }

            for ($u = 0; $u < count($wk_rx_bytes_other); $u++) {
                if(!empty($wk_rx_bytes_other[$u])){
                $wk_rx_bytes_other[$u] = round($wk_rx_bytes_other[$u] / 1000,2) ;
                }
            }

            for ($i = 0; $i < count($wk_tx_byte_http); $i++) {
                if(!empty($wk_tx_byte_http[$i])){
                    $wk_tx_byte_http[$i] = round($wk_tx_byte_http[$i] / 1000,2) ;
                }
            }

            for ($j = 0; $j < count($wk_tx_byte_https); $j++) {
                if(!empty($wk_tx_byte_https[$j])){
                $wk_tx_byte_https[$j] = round($wk_tx_byte_https[$j] / 1000,2) ;
                }
            }
            
            for ($k = 0; $k < count($wk_tx_byte_iperf); $k++) {
                if(!empty($wk_tx_byte_iperf[$k])){
                $wk_tx_byte_iperf[$k] = round($wk_tx_byte_iperf[$k] / 1000,2) ;
                }
            }

            for ($l = 0; $l < count($wk_tx_byte_zoom); $l++) {
                if(!empty($wk_tx_byte_zoom[$l])){
                $wk_tx_byte_zoom[$l] = round($wk_tx_byte_zoom[$l] / 1000,2) ;
                }
            }

            for ($m = 0; $m < count($wk_tx_byte_microsoft_teams); $m++) {
                if(!empty($wk_tx_byte_microsoft_teams[$m])){
                $wk_tx_byte_microsoft_teams[$m] = round($wk_tx_byte_microsoft_teams[$m] / 1000,2) ;
                }
            }
            
            for ($n = 0; $n < count($wk_tx_byte_skype); $n++) {
                if(!empty($wk_tx_byte_skype[$n])){
                $wk_tx_byte_skype[$n] = round( $wk_tx_byte_skype[$n] / 1000 ,2) ;
                }
            }

            for ($o = 0; $o < count($wk_tx_byte_voip); $o++) {
                if(!empty($wk_tx_byte_voip[$o])){
                $wk_tx_byte_voip[$o] = round( $wk_tx_byte_voip[$o] / 1000,2) ;
                }
            }

            for ($p = 0; $p < count($wk_tx_byte_other); $p++) {
                if(!empty($wk_tx_byte_other[$p])){
                $wk_tx_byte_other[$p] = round($wk_tx_byte_other[$p] / 1000,2) ;
                }
            }
        }
        }
        if(isset($wk_max_rx_types_pro) || isset($wk_max_tx_types_pro)) {
        $wk_pro_max = max($wk_max_rx_types_pro,$wk_max_tx_types_pro);
        if($wk_pro_max > 1100) {

            for($c=0;$c <= 10; $c++){
                $wk_max_pro_unit_name[$c]  = 'Kb' ;
            }
            

            for ($v = 0; $v < count($wk_rx_bytes_tcp); $v++) {
                if(!empty($wk_rx_bytes_tcp[$v])){
                $wk_rx_bytes_tcp[$v] = round($wk_rx_bytes_tcp[$v] / 1000,2) ;
                }
                
            }

            for ($w = 0; $w < count($wk_rx_bytes_udp); $w++) {
                if(!empty($wk_rx_bytes_udp[$w])){
                $wk_rx_bytes_udp[$w] = round($wk_rx_bytes_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($wk_rx_bytes_icmp); $x++) {
                if(!empty($wk_rx_bytes_icmp[$x])){
                $wk_rx_bytes_icmp[$x] = round($wk_rx_bytes_icmp[$x] / 1000,2) ;
                }
            }

            for ($v = 0; $v < count($wk_tx_byte_tcp); $v++) {
                if(!empty($wk_tx_byte_tcp[$v])){
                $wk_tx_byte_tcp[$v] = round($wk_tx_byte_tcp[$v] / 1000,2) ;
                }
            }

            for ($w = 0; $w < count($wk_tx_byte_udp); $w++) {
                if(!empty($wk_tx_byte_udp[$w])){
                $wk_tx_byte_udp[$w] = round($wk_tx_byte_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($wk_tx_byte_icmp); $x++) {
                if(!empty($wk_tx_byte_icmp[$x])){
                $wk_tx_byte_icmp[$x] = round($wk_tx_byte_icmp[$x] / 1000,2) ;
                }
            }

            $wk_pro_max /= 1000;
        }
        if($wk_pro_max>1100) {
            for($c=0;$c <= 10; $c++){
                $wk_max_pro_unit_name[$c]  = 'Mb' ;
            }
            // $max_rx_bytes_pro_unit_number /= 1000;
            for ($y = 0; $y < count($wk_rx_bytes_tcp); $y++) {
                if(!empty($wk_rx_bytes_tcp[$y])){
                $wk_rx_bytes_tcp[$y] = round($wk_rx_bytes_tcp[$y] / 1000,2) ;
                }
               
            }

            for ($z = 0; $z < count($wk_rx_bytes_udp); $z++) {
                if(!empty($wk_rx_bytes_udp[$z])){
                $wk_rx_bytes_udp[$z] = round($wk_rx_bytes_udp[$z] / 1000,2) ;
                }
            }
            
            for ($a = 0; $a < count($wk_rx_bytes_icmp); $a++) {
                if(!empty($wk_rx_bytes_icmp[$a])){
                $wk_rx_bytes_icmp[$a] = round($wk_rx_bytes_icmp[$a] / 1000,2) ;
                }
            }

            for ($v = 0; $v < count($wk_tx_byte_tcp); $v++) {
                if(!empty($wk_tx_byte_tcp[$v])){
                $wk_tx_byte_tcp[$v] = round($wk_tx_byte_tcp[$v] / 1000 ,2);
                }
            }

            for ($w = 0; $w < count($wk_tx_byte_udp); $w++) {
                if(!empty($wk_tx_byte_udp[$w])){
                $wk_tx_byte_udp[$w] = round($wk_tx_byte_udp[$w] / 1000,2) ;
                }
            }
            
            for ($x = 0; $x < count($wk_tx_byte_icmp); $x++) {
                if(!empty($wk_tx_byte_icmp[$x])){
                $wk_tx_byte_icmp[$x] = round($wk_tx_byte_icmp[$x] / 1000,2) ;
                }
            }

            
        }
        }

?>
    

<script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var  rx_bytes_label = <?php echo json_encode($rx_bytes_labl); ?>;
    var  rx_bytes_http = <?php echo json_encode($rx_bytes_http); ?>;
    var  rx_bytes_https = <?php echo json_encode($rx_bytes_https); ?>;
    var  rx_bytes_iperf = <?php echo json_encode($rx_bytes_iperf); ?>;
    var  rx_bytes_zoom = <?php echo json_encode($rx_bytes_zoom); ?>;
    var  rx_bytes_microsoft_teams = <?php echo json_encode($rx_bytes_microsoft_teams); ?>;
    var  rx_bytes_skype = <?php echo json_encode($rx_bytes_skype); ?>;
    var  rx_bytes_voip = <?php echo json_encode($rx_bytes_voip); ?>;
    var  rx_bytes_other = <?php echo json_encode($rx_bytes_other); ?>;
    var  rx_bytes_tcp = <?php echo json_encode($rx_bytes_tcp); ?>;
    var  rx_bytes_udp = <?php echo json_encode($rx_bytes_udp); ?>;
    var  rx_bytes_icmp = <?php echo json_encode($rx_bytes_icmp); ?>;
    var max_app_unit_name = <?php echo json_encode($max_app_unit_name); ?>;
console.log(max_app_unit_name,'max_app_unit_name myChart');


var rx_byte_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: rx_bytes_label,
        datasets: [{
            label: 'http', // Name the series
            data: rx_bytes_http, // Specify the data values array
            fill: true,
            borderColor: 'rgb(229,124,35)', // Add custom color border (Line)
            backgroundColor: 'rgba(229,124,35,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'https', // Name the series
            data: rx_bytes_https, // Specify the data values array
            fill: true,
            borderColor: 'rgb(233,102,160)', // Add custom color border (Line)
            backgroundColor: 'rgba(233,102,160,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'iperf', // Name the series
            data: rx_bytes_iperf, // Specify the data values array
            fill: true,
            borderColor: 'rgb(137,129,33)', // Add custom color border (Line)
            backgroundColor: 'rgba(137,129,33,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'zoom', // Name the series
            data: rx_bytes_zoom, // Specify the data values array
            fill: true,
            borderColor: 'rgb(167,130,149)', // Add custom color border (Line)
            backgroundColor: 'rgba(167,130,149,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'microsoft teams', // Name the series
            data: rx_bytes_microsoft_teams, // Specify the data values array
            fill: true,
            borderColor: 'rgb(120,193,243)', // Add custom color border (Line)
            backgroundColor: 'rgba(120,193,243,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'skype', // Name the series
            data: rx_bytes_skype, // Specify the data values array
            fill: true,
            borderColor: 'rgb(181,201,154)', // Add custom color border (Line)
            backgroundColor: 'rgba(181,201,154,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'voip', // Name the series
            data: rx_bytes_voip, // Specify the data values array
            fill: true,
            borderColor: 'rgb(78,79,235)', // Add custom color border (Line)
            backgroundColor: 'rgba(78,79,235,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'other', // Name the series
            data: rx_bytes_other, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                const unit = max_app_unit_name[tooltipItem.datasetIndex];
                return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
});
</script>


<script>
           // rx-byte-pro-chart starts here
    var ctx = document.getElementById("rx-byte-pro-chart").getContext('2d');
    var  rx_bytes_label = <?php echo json_encode($rx_bytes_labl); ?>;
    var  rx_bytes_tcp = <?php echo json_encode($rx_bytes_tcp); ?>;
    var  rx_bytes_udp = <?php echo json_encode($rx_bytes_udp); ?>;
    var  rx_bytes_icmp = <?php echo json_encode($rx_bytes_icmp); ?>;
    var max_pro_unit_name = <?php echo json_encode($max_pro_unit_name); ?>;

    

  var pro_rx_byte_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: rx_bytes_label,
        datasets: [{
            label: 'tcp', // Name the series
            data: rx_bytes_tcp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(58,166,185)', // Add custom color border (Line)
            backgroundColor: 'rgba(58,166,185,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'udp', // Name the series
            data: rx_bytes_udp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(231,206,166)', // Add custom color border (Line)
            backgroundColor: 'rgba(231,206,166,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'icmp', // Name the series
            data: rx_bytes_icmp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                const unit = max_pro_unit_name[tooltipItem.datasetIndex];
                return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
});
 // rx-byte-pro-chart ends here
</script>


<script>
    var ctx = document.getElementById("rx-pkt-pie-chart").getContext('2d');
    var  rx_pkt_labl = <?php echo json_encode($rx_pkt_labl); ?>;
    var  rx_pkt_http = <?php echo json_encode($rx_pkt_http); ?>;
    var  rx_pkt_https = <?php echo json_encode($rx_pkt_https); ?>;
    var  rx_pkt_iperf = <?php echo json_encode($rx_pkt_iperf); ?>;
    var  rx_pkt_zoom = <?php echo json_encode($rx_pkt_zoom); ?>;
    var  rx_pkt_microsoft_teams = <?php echo json_encode($rx_pkt_microsoft_teams); ?>;
    var  rx_pkt_skype = <?php echo json_encode($rx_pkt_skype); ?>;
    var  rx_pkt_voip = <?php echo json_encode($rx_pkt_voip); ?>;
    var  rx_pkt_other = <?php echo json_encode($rx_pkt_other); ?>;
    var  rx_pkt_tcp = <?php echo json_encode($rx_pkt_tcp); ?>;
    var  rx_pkt_udp = <?php echo json_encode($rx_pkt_udp); ?>;
    var  rx_pkt_icmp = <?php echo json_encode($rx_pkt_icmp); ?>;


var rx_pkt_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: rx_pkt_labl,
        datasets: [{
            label: 'http', // Name the series
            data: rx_pkt_http, // Specify the data values array
            fill: true,
            borderColor: 'rgb(229,124,35)', // Add custom color border (Line)
            backgroundColor: 'rgba(229,124,35,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'https', // Name the series
            data: rx_pkt_https, // Specify the data values array
            fill: true,
            borderColor: 'rgb(233,102,160)', // Add custom color border (Line)
            backgroundColor: 'rgba(233,102,160,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'iperf', // Name the series
            data: rx_pkt_iperf, // Specify the data values array
            fill: true,
            borderColor: 'rgb(137,129,33)', // Add custom color border (Line)
            backgroundColor: 'rgba(137,129,33,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'zoom', // Name the series
            data: rx_pkt_zoom, // Specify the data values array
            fill: true,
            borderColor: 'rgb(167,130,149)', // Add custom color border (Line)
            backgroundColor: 'rgba(167,130,149,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'microsoft teams', // Name the series
            data: rx_pkt_microsoft_teams, // Specify the data values array
            fill: true,
            borderColor: 'rgb(120,193,243)', // Add custom color border (Line)
            backgroundColor: 'rgba(120,193,243,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'skype', // Name the series
            data: rx_pkt_skype, // Specify the data values array
            fill: true,
            borderColor: 'rgb(181,201,154)', // Add custom color border (Line)
            backgroundColor: 'rgba(181,201,154,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'voip', // Name the series
            data: rx_pkt_voip, // Specify the data values array
            fill: true,
            borderColor: 'rgb(78,79,235)', // Add custom color border (Line)
            backgroundColor: 'rgba(78,79,235,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'other', // Name the series
            data: rx_pkt_other, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                return `${datasetLabel}: ${value}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
});
</script>
       


<script>
    // rx-pkt-pro-pie-char starts here
    var ctx = document.getElementById("rx-pkt-pro-pie-chart").getContext('2d');
    var  rx_pkt_labl = <?php echo json_encode($rx_pkt_labl); ?>;
    var  rx_pkt_tcp = <?php echo json_encode($rx_pkt_tcp); ?>;
    var  rx_pkt_udp = <?php echo json_encode($rx_pkt_udp); ?>;
    var  rx_pkt_icmp = <?php echo json_encode($rx_pkt_icmp); ?>;



  var pro_rx_pkt_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: rx_pkt_labl,
        datasets: [{
            label: 'tcp', // Name the series
            data: rx_pkt_tcp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(58,166,185)', // Add custom color border (Line)
            backgroundColor: 'rgba(58,166,185,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'udp', // Name the series
            data: rx_pkt_udp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(231,206,166)', // Add custom color border (Line)
            backgroundColor: 'rgba(231,206,166,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'icmp', // Name the series
            data: rx_pkt_icmp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                return `${datasetLabel}: ${value}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
    });
 // rx-pkt-pro-pie-char ends here
</script>

<script>
    var ctx = document.getElementById("tx-bytes-pie-chart").getContext('2d');
    var  tx_byte_labl = <?php echo json_encode($tx_byte_labl); ?>;
    var  tx_byte_http = <?php echo json_encode($tx_byte_http); ?>;
    var  tx_byte_https = <?php echo json_encode($tx_byte_https); ?>;
    var  tx_byte_iperf = <?php echo json_encode($tx_byte_iperf); ?>;
    var  tx_byte_zoom = <?php echo json_encode($tx_byte_zoom); ?>;
    var  tx_byte_microsoft_teams = <?php echo json_encode($tx_byte_microsoft_teams); ?>;
    var  tx_byte_skype = <?php echo json_encode($tx_byte_skype); ?>;
    var  tx_byte_voip = <?php echo json_encode($tx_byte_voip); ?>;
    var  tx_byte_other = <?php echo json_encode($tx_byte_other); ?>;
    var  tx_byte_tcp = <?php echo json_encode($tx_byte_tcp); ?>;
    var  tx_byte_udp = <?php echo json_encode($tx_byte_udp); ?>;
    var  tx_byte_icmp = <?php echo json_encode($tx_byte_icmp); ?>;
    var max_app_unit_name = <?php echo json_encode($max_app_unit_name); ?>;

  

var tx_byte_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: tx_byte_labl,
        datasets: [{
            label: 'http', // Name the series
            data: tx_byte_http, // Specify the data values array
            fill: true,
            borderColor: 'rgb(229,124,35)', // Add custom color border (Line)
            backgroundColor: 'rgba(229,124,35,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'https', // Name the series
            data: tx_byte_https, // Specify the data values array
            fill: true,
            borderColor: 'rgb(233,102,160)', // Add custom color border (Line)
            backgroundColor: 'rgba(233,102,160,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'iperf', // Name the series
            data: tx_byte_iperf, // Specify the data values array
            fill: true,
            borderColor: 'rgb(137,129,33)', // Add custom color border (Line)
            backgroundColor: 'rgba(137,129,33,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'zoom', // Name the series
            data: tx_byte_zoom, // Specify the data values array
            fill: true,
            borderColor: 'rgb(167,130,149)', // Add custom color border (Line)
            backgroundColor: 'rgba(167,130,149,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'microsoft teams', // Name the series
            data: tx_byte_microsoft_teams, // Specify the data values array
            fill: true,
            borderColor: 'rgb(120,193,243)', // Add custom color border (Line)
            backgroundColor: 'rgba(120,193,243,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'skype', // Name the series
            data: tx_byte_skype, // Specify the data values array
            fill: true,
            borderColor: 'rgb(181,201,154)', // Add custom color border (Line)
            backgroundColor: 'rgba(181,201,154,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'voip', // Name the series
            data: tx_byte_voip, // Specify the data values array
            fill: true,
            borderColor: 'rgb(78,79,235)', // Add custom color border (Line)
            backgroundColor: 'rgba(78,79,235,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'other', // Name the series
            data: tx_byte_other, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                const unit = max_app_unit_name[tooltipItem.datasetIndex];
                return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
});
</script>

<script>
    // tx-bytes-pro-pie-chart starts here
    var ctx = document.getElementById("tx-bytes-pro-pie-chart").getContext('2d');

    var  tx_byte_labl = <?php echo json_encode($tx_byte_labl); ?>;
    var  tx_byte_tcp = <?php echo json_encode($tx_byte_tcp); ?>;
    var  tx_byte_udp = <?php echo json_encode($tx_byte_udp); ?>;
    var  tx_byte_icmp = <?php echo json_encode($tx_byte_icmp); ?>;
    var  max_pro_unit_name = <?php echo json_encode($max_pro_unit_name); ?>;

  var pro_tx_byte_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: tx_byte_labl,
        datasets: [{
            label: 'tcp', // Name the series
            data: tx_byte_tcp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(58,166,185)', // Add custom color border (Line)
            backgroundColor: 'rgba(58,166,185,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'udp', // Name the series
            data: tx_byte_udp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(231,206,166)', // Add custom color border (Line)
            backgroundColor: 'rgba(231,206,166,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'icmp', // Name the series
            data: tx_byte_icmp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                const unit = max_pro_unit_name[tooltipItem.datasetIndex];
                return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
    });
 // tx-bytes-pro-pie-chart ends here
</script>

<script>
    var ctx = document.getElementById("tx-pkt-pie-chart").getContext('2d');
    var  tx_pkt_labl = <?php echo json_encode($tx_pkt_labl); ?>;
    var  tx_pkt_http = <?php echo json_encode($tx_pkt_http); ?>;
    var  tx_pkt_https = <?php echo json_encode($tx_pkt_https); ?>;
    var  tx_pkt_iperf = <?php echo json_encode($tx_pkt_iperf); ?>;
    var  tx_pkt_zoom = <?php echo json_encode($tx_pkt_zoom); ?>;
    var  tx_pkt_microsoft_teams = <?php echo json_encode($tx_pkt_microsoft_teams); ?>;
    var  tx_pkt_skype = <?php echo json_encode($tx_pkt_skype); ?>;
    var  tx_pkt_voip = <?php echo json_encode($tx_pkt_voip); ?>;
    var  tx_pkt_other = <?php echo json_encode($tx_pkt_other); ?>;
    var  tx_pkt_tcp = <?php echo json_encode($tx_pkt_tcp); ?>;
    var  tx_pkt_udp = <?php echo json_encode($tx_pkt_udp); ?>;
    var  tx_pkt_icmp = <?php echo json_encode($tx_pkt_icmp); ?>;

   

var tx_pkt_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: tx_pkt_labl,
        datasets: [{
            label: 'http', // Name the series
            data: tx_pkt_http, // Specify the data values array
            fill: true,
            borderColor: 'rgb(229,124,35)', // Add custom color border (Line)
            backgroundColor: 'rgba(229,124,35,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'https', // Name the series
            data: tx_pkt_https, // Specify the data values array
            fill: true,
            borderColor: 'rgb(233,102,160)', // Add custom color border (Line)
            backgroundColor: 'rgba(233,102,160,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'iperf', // Name the series
            data: tx_pkt_iperf, // Specify the data values array
            fill: true,
            borderColor: 'rgb(137,129,33)', // Add custom color border (Line)
            backgroundColor: 'rgba(137,129,33,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'zoom', // Name the series
            data: tx_pkt_zoom, // Specify the data values array
            fill: true,
            borderColor: 'rgb(167,130,149)', // Add custom color border (Line)
            backgroundColor: 'rgba(167,130,149,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'microsoft teams', // Name the series
            data: tx_pkt_microsoft_teams, // Specify the data values array
            fill: true,
            borderColor: 'rgb(120,193,243)', // Add custom color border (Line)
            backgroundColor: 'rgba(120,193,243,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'skype', // Name the series
            data: tx_pkt_skype, // Specify the data values array
            fill: true,
            borderColor: 'rgb(181,201,154)', // Add custom color border (Line)
            backgroundColor: 'rgba(181,201,154,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'voip', // Name the series
            data: tx_pkt_voip, // Specify the data values array
            fill: true,
            borderColor: 'rgb(78,79,235)', // Add custom color border (Line)
            backgroundColor: 'rgba(78,79,235,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'other', // Name the series
            data: tx_pkt_other, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                return `${datasetLabel}: ${value}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
});
</script>


<script>
    // tx-pkt-pro-pie-chart starts here
    var ctx = document.getElementById("tx-pkt-pro-pie-chart").getContext('2d');

    var  tx_pkt_labl = <?php echo json_encode($tx_pkt_labl); ?>;
    var  tx_pkt_tcp = <?php echo json_encode($tx_pkt_tcp); ?>;
    var  tx_pkt_udp = <?php echo json_encode($tx_pkt_udp); ?>;
    var  tx_pkt_icmp = <?php echo json_encode($tx_pkt_icmp); ?>;


  var pro_tx_pkt_chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: tx_pkt_labl,
        datasets: [{
            label: 'tcp', // Name the series
            data: tx_pkt_tcp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(58,166,185)', // Add custom color border (Line)
            backgroundColor: 'rgba(58,166,185,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'udp', // Name the series
            data: tx_pkt_udp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(231,206,166)', // Add custom color border (Line)
            backgroundColor: 'rgba(231,206,166,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        },
        {
            label: 'icmp', // Name the series
            data: tx_pkt_icmp, // Specify the data values array
            fill: true,
            borderColor: 'rgb(209,209,209)', // Add custom color border (Line)
            backgroundColor: 'rgba(209,209,209,0.5)', // Add custom color background (Points and Fill)
            borderWidth: 1 // Specify bar border width
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
                display: true,
                position: 'right',
            },
       
        tooltips: {
            responsive: true,
            mode: 'index', // 'nearest', 'index', 'interpolate', or 'point'
            intersect: false,
            displayColors: false, // Set to true if you want to show color boxes in tooltips
            callbacks: {
            title: function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            },
            label: function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                return `${datasetLabel}: ${value}`; // Show the dataset label and value as the tooltip label
            },
        },
      },
        scales: {
            x: {
            display: true,
            },
            y: {
            display: true,
            },
        },
    }
    });
 // tx-pkt-pro-pie-chart ends here
</script>

<script>
// Modify the data or options of the chart instance
function ShowPastWeek() {

        rx_byte_chart.config.data.labels = <?php echo json_encode($wk_rx_bytes_labl); ?>;
        rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_rx_bytes_http); ?>;
        rx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($wk_rx_bytes_https); ?>;
        rx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($wk_rx_bytes_iperf); ?>;
        rx_byte_chart.config.data.datasets[3].data = <?php echo json_encode($wk_rx_bytes_zoom); ?>;
        rx_byte_chart.config.data.datasets[4].data = <?php echo json_encode($wk_rx_bytes_microsoft_teams); ?>;
        rx_byte_chart.config.data.datasets[5].data = <?php echo json_encode($wk_rx_bytes_skype); ?>;
        rx_byte_chart.config.data.datasets[6].data = <?php echo json_encode($wk_rx_bytes_voip); ?>;
        rx_byte_chart.config.data.datasets[7].data = <?php echo json_encode($wk_rx_bytes_other); ?>;

        pro_rx_byte_chart.config.data.labels = <?php echo json_encode($wk_rx_bytes_labl); ?>;
        pro_rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_rx_bytes_tcp); ?>;
        pro_rx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($wk_rx_bytes_udp); ?>;
        pro_rx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($wk_rx_bytes_icmp); ?>;

        rx_pkt_chart.config.data.labels = <?php echo json_encode($wk_rx_pkt_labl); ?>;
        rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_rx_pkt_http); ?>;
        rx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($wk_rx_pkt_https); ?>;
        rx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($wk_rx_pkt_iperf); ?>;
        rx_pkt_chart.config.data.datasets[3].data = <?php echo json_encode($wk_rx_pkt_zoom); ?>;
        rx_pkt_chart.config.data.datasets[4].data = <?php echo json_encode($wk_rx_pkt_microsoft_teams); ?>;
        rx_pkt_chart.config.data.datasets[5].data = <?php echo json_encode($wk_rx_pkt_skype); ?>;
        rx_pkt_chart.config.data.datasets[6].data = <?php echo json_encode($wk_rx_pkt_voip); ?>;
        rx_pkt_chart.config.data.datasets[7].data = <?php echo json_encode($wk_rx_pkt_other); ?>;

        pro_rx_pkt_chart.config.data.labels = <?php echo json_encode($wk_rx_pkt_labl); ?>;
        pro_rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_rx_pkt_tcp); ?>;
        pro_rx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($wk_rx_pkt_udp); ?>;
        pro_rx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($wk_rx_pkt_icmp); ?>;

        tx_byte_chart.config.data.labels = <?php echo json_encode($wk_tx_byte_labl); ?>;
        tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_byte_http); ?>;
        tx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($wk_tx_byte_https); ?>;
        tx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($wk_tx_byte_iperf); ?>;
        tx_byte_chart.config.data.datasets[3].data = <?php echo json_encode($wk_tx_byte_zoom); ?>;
        tx_byte_chart.config.data.datasets[4].data = <?php echo json_encode($wk_tx_byte_microsoft_teams); ?>;
        tx_byte_chart.config.data.datasets[5].data = <?php echo json_encode($wk_tx_byte_skype); ?>;
        tx_byte_chart.config.data.datasets[6].data = <?php echo json_encode($wk_tx_byte_voip); ?>;
        tx_byte_chart.config.data.datasets[7].data = <?php echo json_encode($wk_tx_byte_other); ?>;

        pro_tx_byte_chart.config.data.labels = <?php echo json_encode($wk_tx_byte_labl); ?>;
        pro_tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_byte_tcp); ?>;
        pro_tx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($wk_tx_byte_udp); ?>;
        pro_tx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($wk_tx_byte_icmp); ?>;

        tx_pkt_chart.config.data.labels = <?php echo json_encode($wk_tx_pkt_labl); ?>;
        tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_pkt_http); ?>;
        tx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($wk_tx_pkt_https); ?>;
        tx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($wk_tx_pkt_iperf); ?>;
        tx_pkt_chart.config.data.datasets[3].data = <?php echo json_encode($wk_tx_pkt_zoom); ?>;
        tx_pkt_chart.config.data.datasets[4].data = <?php echo json_encode($wk_tx_pkt_microsoft_teams); ?>;
        tx_pkt_chart.config.data.datasets[5].data = <?php echo json_encode($wk_tx_pkt_skype); ?>;
        tx_pkt_chart.config.data.datasets[6].data = <?php echo json_encode($wk_tx_pkt_voip); ?>;
        tx_pkt_chart.config.data.datasets[7].data = <?php echo json_encode($wk_tx_pkt_other); ?>;

        pro_tx_pkt_chart.config.data.labels = <?php echo json_encode($wk_tx_pkt_labl); ?>;
        pro_tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_pkt_tcp); ?>;
        pro_tx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($wk_tx_pkt_udp); ?>;
        pro_tx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($wk_tx_pkt_icmp); ?>;

        //tooltip updations
        var wk_max_app_unit_name = <?php echo json_encode($wk_max_app_unit_name); ?>;
        console.log(wk_max_app_unit_name,'wk_max_app_unit_name');
        console.log(rx_byte_chart.config.data.labels,'rx_byte_chart.config.data.labels');
        

        rx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
                return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
            }

        rx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
                const value = tooltipItem.yLabel;
                const unit = wk_max_app_unit_name[tooltipItem.datasetIndex];
                return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
            }

        tx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        tx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = wk_max_app_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

            

       

        var wk_max_pro_unit_name = <?php echo json_encode($wk_max_pro_unit_name); ?>;

        pro_rx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        pro_rx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = wk_max_pro_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

        pro_tx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        pro_tx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = wk_max_pro_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

    
        rx_byte_chart.update();
        pro_rx_byte_chart.update();
        rx_pkt_chart.update();
        pro_rx_pkt_chart.update();
        tx_byte_chart.update();
        pro_tx_byte_chart.update();
        tx_pkt_chart.update();
        pro_tx_pkt_chart.update();

        document.getElementById('PD').removeAttribute("disabled");
        document.getElementById('PW').setAttribute("disabled","disabled");
        var DisplayText = document.getElementById('DisplayText');
        DisplayText.innerHTML = 'Showing: Past 1 week';
}

function ShowPastDay() {

        rx_byte_chart.config.data.labels = <?php echo json_encode($rx_bytes_labl); ?>;
        rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($rx_bytes_http); ?>;
        rx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($rx_bytes_https); ?>;
        rx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($rx_bytes_iperf); ?>;
        rx_byte_chart.config.data.datasets[3].data = <?php echo json_encode($rx_bytes_zoom); ?>;
        rx_byte_chart.config.data.datasets[4].data = <?php echo json_encode($rx_bytes_microsoft_teams); ?>;
        rx_byte_chart.config.data.datasets[5].data = <?php echo json_encode($rx_bytes_skype); ?>;
        rx_byte_chart.config.data.datasets[6].data = <?php echo json_encode($rx_bytes_voip); ?>;
        rx_byte_chart.config.data.datasets[7].data = <?php echo json_encode($rx_bytes_other); ?>;

        pro_rx_byte_chart.config.data.labels = <?php echo json_encode($rx_bytes_labl); ?>;
        pro_rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($rx_bytes_tcp); ?>;
        pro_rx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($rx_bytes_udp); ?>;
        pro_rx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($rx_bytes_icmp); ?>;

        rx_pkt_chart.config.data.labels = <?php echo json_encode($rx_pkt_labl); ?>;
        rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($rx_pkt_http); ?>;
        rx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($rx_pkt_https); ?>;
        rx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($rx_pkt_iperf); ?>;
        rx_pkt_chart.config.data.datasets[3].data = <?php echo json_encode($rx_pkt_zoom); ?>;
        rx_pkt_chart.config.data.datasets[4].data = <?php echo json_encode($rx_pkt_microsoft_teams); ?>;
        rx_pkt_chart.config.data.datasets[5].data = <?php echo json_encode($rx_pkt_skype); ?>;
        rx_pkt_chart.config.data.datasets[6].data = <?php echo json_encode($rx_pkt_voip); ?>;
        rx_pkt_chart.config.data.datasets[7].data = <?php echo json_encode($rx_pkt_other); ?>;

        pro_rx_pkt_chart.config.data.labels = <?php echo json_encode($rx_pkt_labl); ?>;
        pro_rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($rx_pkt_tcp); ?>;
        pro_rx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($rx_pkt_udp); ?>;
        pro_rx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($rx_pkt_icmp); ?>;

        tx_byte_chart.config.data.labels = <?php echo json_encode($tx_byte_labl); ?>;
        tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($tx_byte_http); ?>;
        tx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($tx_byte_https); ?>;
        tx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($tx_byte_iperf); ?>;
        tx_byte_chart.config.data.datasets[3].data = <?php echo json_encode($tx_byte_zoom); ?>;
        tx_byte_chart.config.data.datasets[4].data = <?php echo json_encode($tx_byte_microsoft_teams); ?>;
        tx_byte_chart.config.data.datasets[5].data = <?php echo json_encode($tx_byte_skype); ?>;
        tx_byte_chart.config.data.datasets[6].data = <?php echo json_encode($tx_byte_voip); ?>;
        tx_byte_chart.config.data.datasets[7].data = <?php echo json_encode($tx_byte_other); ?>;

        pro_tx_byte_chart.config.data.labels = <?php echo json_encode($tx_byte_labl); ?>;
        pro_tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($tx_byte_tcp); ?>;
        pro_tx_byte_chart.config.data.datasets[1].data = <?php echo json_encode($tx_byte_udp); ?>;
        pro_tx_byte_chart.config.data.datasets[2].data = <?php echo json_encode($tx_byte_icmp); ?>;

        tx_pkt_chart.config.data.labels = <?php echo json_encode($tx_pkt_labl); ?>;
        tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($tx_pkt_http); ?>;
        tx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($tx_pkt_https); ?>;
        tx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($tx_pkt_iperf); ?>;
        tx_pkt_chart.config.data.datasets[3].data = <?php echo json_encode($tx_pkt_zoom); ?>;
        tx_pkt_chart.config.data.datasets[4].data = <?php echo json_encode($tx_pkt_microsoft_teams); ?>;
        tx_pkt_chart.config.data.datasets[5].data = <?php echo json_encode($tx_pkt_skype); ?>;
        tx_pkt_chart.config.data.datasets[6].data = <?php echo json_encode($tx_pkt_voip); ?>;
        tx_pkt_chart.config.data.datasets[7].data = <?php echo json_encode($tx_pkt_other); ?>;

        pro_tx_pkt_chart.config.data.labels = <?php echo json_encode($tx_pkt_labl); ?>;
        pro_tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($tx_pkt_tcp); ?>;
        pro_tx_pkt_chart.config.data.datasets[1].data = <?php echo json_encode($tx_pkt_udp); ?>;
        pro_tx_pkt_chart.config.data.datasets[2].data = <?php echo json_encode($tx_pkt_icmp); ?>;

        //tooltip updations
        var max_app_unit_name = <?php echo json_encode($max_app_unit_name); ?>;
        console.log(max_app_unit_name,'max_app_unit_name');
        
        rx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        rx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = max_app_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

        tx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        tx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = max_app_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

       


        var max_pro_unit_name = <?php echo json_encode($max_pro_unit_name); ?>;
        console.log('max_pro_unit_name 24 hrs',max_pro_unit_name);
       
        pro_rx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        pro_rx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = max_pro_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }

        pro_tx_byte_chart.config.options.tooltips.callbacks.title = function(tooltipItems, data) {
            return data.labels[tooltipItems[0].index]; // Show the label as the tooltip title
        }

        pro_tx_byte_chart.config.options.tooltips.callbacks.label = function(tooltipItem, data) {
            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label;
            const value = tooltipItem.yLabel;
            const unit = max_pro_unit_name[tooltipItem.datasetIndex];
            return `${datasetLabel}: ${value} ${unit}`; // Show the dataset label and value as the tooltip label
        }
       
       

        rx_byte_chart.update();
        pro_rx_byte_chart.update();
        rx_pkt_chart.update();
        pro_rx_pkt_chart.update();
        tx_byte_chart.update();
        pro_tx_byte_chart.update();
        tx_pkt_chart.update();
        pro_tx_pkt_chart.update();

        var DisplayText = document.getElementById('DisplayText');
        document.getElementById('PW').removeAttribute("disabled");
        document.getElementById('PD').setAttribute("disabled","disabled");
        DisplayText.innerHTML = 'Showing: Past 24 Hours';
}

</script>


    
</body>
</html>

