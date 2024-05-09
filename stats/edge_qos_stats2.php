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
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<body>
<?php
    include('../c/c_db_access.php');
    $port_nw_stats = htmlspecialchars($_GET["port_nw_stats"]);
    $port_device_stats = htmlspecialchars($_GET["port_device_stats"]);
        $serialnumber = htmlspecialchars($_GET["sn"]);
        ini_set('display_errors', 1);
        error_reporting(E_ALL);


    if($port_nw_stats=='wan1') { $port_nw_stats_name="WAN1"; }
    else if($port_nw_stats=='wan2') { $port_nw_stats_name="WAN2"; }
    else if($port_nw_stats=='wan3') { $port_nw_stats_name="WAN3"; }
    else if($port_nw_stats=='lte1') { $port_nw_stats_name="LTE1"; }
   else if($port_nw_stats=='lte2') { $port_nw_stats_name="LTE2"; }
   else if($port_nw_stats=='lte3') { $port_nw_stats_name="LTE3"; }
   else if($port_nw_stats=='sdwan') { $port_nw_stats_name="SD-WAN"; }

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
        
        <div class="row">
            
            <div class="col-lg-6" align="center">
            <div class="graph">

            <div class="title">
            <h4 >RX Bytes </h4>
            </div>

            <div class="row">
                
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="chart-heading">
                        <b><h6>Application Stats</h6></b>
                    </div>
                    <canvas id="rx-pie-chart"></canvas>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 protocol-stat">
                    <div class="chart-heading ">
                        <b><h6>Protocol Stats</h6></b>
                    </div>
                    <canvas id="pro-rx-pie-chart"></canvas>
                </div>
            </div>

            
            </div>
            </div>

            <div class="col-lg-6" align="center">
            <div class="graph">
            <div class="title">
            <h4 >RX Packet </h4>
            </div>

            <div class="row">
                
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="chart-heading">
                        <b><h6>Application Stats</h6></b>
                    </div>
                    <canvas id="rx-pkt-pie-chart"></canvas>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 protocol-stat">
                    <div class="chart-heading">
                        <b><h6>Protocol Stats</h6></b>
                    </div>
                    <canvas id="pro-rx-pkt-pie-chart"></canvas>
                </div>
            </div>


            
            </div>
            </div>
        </div>
        <div class="row" style="margin-top:4%;">
            
            <div class="col-lg-6" align="center">
            <div class="graph">
            <div class="title">
            <h4 >TX Bytes </h4>
            </div>

            <div class="row">
               
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="chart-heading">
                        <b><h6>Application Stats</h6></b>
                    </div>
                    <canvas id="tx-bytes-pie-chart"></canvas>
                </div>
                <div class="col-lg-6 protocol-stat col-md-6 col-sm-6" >
                    <div class="chart-heading">
                        <b><h6>Protocol Stats</h6></b>
                    </div>
                    <canvas id="pro-tx-bytes-pie-chart"></canvas>
                </div>
            </div>
            
            
            </div>
            </div>

            <div class="col-lg-6" align="center">
            <div class="graph">
            <div class="title">
            <h4 >TX Packet </h4>
            </div>
            <div class="row">
                
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="chart-heading">
                        <b><h6>Application Stats</h6></b>
                    </div>
                    <canvas id="tx-pkt-pie-chart"></canvas>
                </div>
                <div class="col-lg-6 protocol-stat col-md-6 col-sm-6">
                    <div class="chart-heading ">
                        <b><h6>Protocol Stats</h6></b>
                    </div>
                    <canvas id="pro-tx-pkt-pie-chart"></canvas>
                </div>
            </div>
            
            </div>
            </div>
        </div>
        
         <?php 
        $query2 = "select sum(http_rx_bytes) as http_rx_bytes, sum(https_rx_bytes) as https_rx_bytes, sum(iperf_rx_bytes) as iperf_rx_bytes, sum(zoom_rx_bytes) as zoom_rx_bytes, sum(microsoft_teams_rx_bytes) as microsoft_teams_rx_bytes,sum(skype_rx_bytes) as skype_rx_bytes, sum(voip_rx_bytes) as voip_rx_bytes, sum(other_rx_bytes) as other_rx_bytes,sum(icmp_rx_bytes) as icmp_rx_bytes,sum(tcp_rx_bytes) as tcp_rx_bytes,sum(udp_rx_bytes) as udp_rx_bytes from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 24 hour";
        $res2 = $db->query($query2);
        if ($res2->num_rows > 0)
        {  $qos_array = array();
	        $pro_qos_array = array();
	        while($row2 = $res2->fetch_assoc())
	        {	//$_max_rx_app_bytes = max($row2['http_rx_bytes'], $row2['https_rx_bytes'], $row2['iperf_rx_bytes'], $row2['zoom_rx_bytes'], $row2['microsoft_teams_rx_bytes'], $row2['skype_rx_bytes'], $row2['voip_rx_bytes'], $row2['other_rx_bytes'] );
	            array_push($qos_array,$row2['http_rx_bytes']);
	            array_push($qos_array,$row2['https_rx_bytes']);
	            array_push($qos_array,$row2['iperf_rx_bytes']);
	            array_push($qos_array,$row2['zoom_rx_bytes']);
	            array_push($qos_array,$row2['microsoft_teams_rx_bytes']);
	            array_push($qos_array,$row2['skype_rx_bytes']);
	            array_push($qos_array,$row2['voip_rx_bytes']);
	            array_push($qos_array,$row2['other_rx_bytes']);
	            array_push($pro_qos_array,$row2['tcp_rx_bytes']);
	            array_push($pro_qos_array,$row2['udp_rx_bytes']);
	            array_push($pro_qos_array,$row2['icmp_rx_bytes']);
	        }
        }

        $wk_query2 = "select sum(http_rx_bytes) as http_rx_bytes, sum(https_rx_bytes) as https_rx_bytes, sum(iperf_rx_bytes) as iperf_rx_bytes, sum(zoom_rx_bytes) as zoom_rx_bytes, sum(microsoft_teams_rx_bytes) as microsoft_teams_rx_bytes,sum(skype_rx_bytes) as skype_rx_bytes, sum(voip_rx_bytes) as voip_rx_bytes, sum(other_rx_bytes) as other_rx_bytes,sum(icmp_rx_bytes) as icmp_rx_bytes,sum(tcp_rx_bytes) as tcp_rx_bytes,sum(udp_rx_bytes) as udp_rx_bytes from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 1 week";

        $wk_rx_bytes_res2 = $db->query($wk_query2);
           
        if ($wk_rx_bytes_res2->num_rows > 0)
        {
        $wk_qos_array = array();
        $pro_wk_qos_array = array();
        while($wk_row2 = $wk_rx_bytes_res2->fetch_assoc())
        {
            array_push($wk_qos_array,$wk_row2['http_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['https_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['iperf_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['zoom_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['microsoft_teams_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['skype_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['voip_rx_bytes']);
            array_push($wk_qos_array,$wk_row2['other_rx_bytes']);
            array_push($pro_wk_qos_array,$wk_row2['tcp_rx_bytes']);
            array_push($pro_wk_qos_array,$wk_row2['udp_rx_bytes']);
            array_push($pro_wk_qos_array,$wk_row2['icmp_rx_bytes']);
        }
        
        }

        $rx_pkt_query = "select sum(http_rx_pkts) as http_rx_pkts,sum(https_rx_pkts) as https_rx_pkts,sum(iperf_rx_pkts) as iperf_rx_pkts,sum(zoom_rx_pkts) as zoom_rx_pkts,sum(microsoft_teams_rx_pkts) as microsoft_teams_rx_pkts,sum(skype_rx_pkts) as skype_rx_pkts,sum(voip_rx_pkts) as voip_rx_pkts,sum(other_rx_pkts) as other_rx_pkts,sum(icmp_rx_pkts) as icmp_rx_pkts,sum(tcp_rx_pkts) as tcp_rx_pkts,sum(udp_rx_pkts) as udp_rx_pkts  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 24 hour";

        $rx_pk_res = $db->query($rx_pkt_query);
           
        if ($rx_pk_res->num_rows > 0)
        {
        $rx_pkt_array = array();
        $pro_rx_pkt_array = array();
        while($pk_row = $rx_pk_res->fetch_assoc())
        {
            array_push($rx_pkt_array,$pk_row['http_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['https_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['iperf_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['zoom_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['microsoft_teams_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['skype_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['voip_rx_pkts']);
            array_push($rx_pkt_array,$pk_row['other_rx_pkts']);
            array_push($pro_rx_pkt_array,$pk_row['tcp_rx_pkts']);
            array_push($pro_rx_pkt_array,$pk_row['udp_rx_pkts']);
            array_push($pro_rx_pkt_array,$pk_row['icmp_rx_pkts']);
            
        }
        }

        $wk_rx_pkt_query = "select sum(http_rx_pkts) as http_rx_pkts,sum(https_rx_pkts) as https_rx_pkts,sum(iperf_rx_pkts) as iperf_rx_pkts,sum(zoom_rx_pkts) as zoom_rx_pkts,sum(microsoft_teams_rx_pkts) as microsoft_teams_rx_pkts,sum(skype_rx_pkts) as skype_rx_pkts,sum(voip_rx_pkts) as voip_rx_pkts,sum(other_rx_pkts) as other_rx_pkts,sum(icmp_rx_pkts) as icmp_rx_pkts,sum(tcp_rx_pkts) as tcp_rx_pkts,sum(udp_rx_pkts) as udp_rx_pkts  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 1 week";

        $wk_rx_pk_res = $db->query($wk_rx_pkt_query);
           
        if ($wk_rx_pk_res->num_rows > 0)
        {
        $wk_rx_pkt_array = array();
        $pro_wk_rx_pkt_array = array();
        while($wk_pk_row = $wk_rx_pk_res->fetch_assoc())
        {
            array_push($wk_rx_pkt_array,$wk_pk_row['http_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['https_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['iperf_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['zoom_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['microsoft_teams_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['skype_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['voip_rx_pkts']);
            array_push($wk_rx_pkt_array,$wk_pk_row['other_rx_pkts']);
            array_push($pro_wk_rx_pkt_array,$wk_pk_row['tcp_rx_pkts']);
            array_push($pro_wk_rx_pkt_array,$wk_pk_row['udp_rx_pkts']);
            array_push($pro_wk_rx_pkt_array,$wk_pk_row['icmp_rx_pkts']);
            
        }
        }

        $tx_bytes_query = "select sum(http_tx_bytes) as http_tx_bytes,sum(https_tx_bytes) as https_tx_bytes,sum(iperf_tx_bytes) as iperf_tx_bytes,sum(zoom_tx_bytes) as zoom_tx_bytes,sum(microsoft_teams_tx_bytes) as microsoft_teams_tx_bytes,sum(skype_tx_bytes) as skype_tx_bytes,sum(voip_tx_bytes) as voip_tx_bytes,sum(other_tx_bytes) as other_tx_bytes,sum(icmp_tx_bytes) as icmp_tx_bytes,sum(tcp_tx_bytes) as tcp_tx_bytes,sum(udp_tx_bytes) as udp_tx_bytes  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 24 hour";

        $tx_bytes_res = $db->query($tx_bytes_query);
           
        if ($tx_bytes_res->num_rows > 0)
        {
        $tx_bytes_array = array();
        $pro_tx_bytes_array = array();
        while($tx_bytes_row = $tx_bytes_res->fetch_assoc())
        {
            array_push($tx_bytes_array,$tx_bytes_row['http_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['https_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['iperf_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['zoom_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['microsoft_teams_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['skype_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['voip_tx_bytes']);
            array_push($tx_bytes_array,$tx_bytes_row['other_tx_bytes']);
            array_push($pro_tx_bytes_array,$tx_bytes_row['tcp_tx_bytes']);
            array_push($pro_tx_bytes_array,$tx_bytes_row['udp_tx_bytes']);
            array_push($pro_tx_bytes_array,$tx_bytes_row['icmp_tx_bytes']);
            
        }
        }

        $wk_tx_bytes_query = "select sum(http_tx_bytes) as http_tx_bytes,sum(https_tx_bytes) as https_tx_bytes,sum(iperf_tx_bytes) as iperf_tx_bytes,sum(zoom_tx_bytes) as zoom_tx_bytes,sum(microsoft_teams_tx_bytes) as microsoft_teams_tx_bytes,sum(skype_tx_bytes) as skype_tx_bytes,sum(voip_tx_bytes) as voip_tx_bytes,sum(other_tx_bytes) as other_tx_bytes,sum(icmp_tx_bytes) as icmp_tx_bytes,sum(tcp_tx_bytes) as tcp_tx_bytes,sum(udp_tx_bytes) as udp_tx_bytes  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 1 week";

        $wk_tx_bytes_res = $db->query($wk_tx_bytes_query);
           
        if ($wk_tx_bytes_res->num_rows > 0)
        {
        $wk_tx_bytes_array = array();
        $pro_wk_tx_bytes_array = array();
        while($wk_tx_bytes_row = $wk_tx_bytes_res->fetch_assoc())
        {
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['http_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['https_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['iperf_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['zoom_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['microsoft_teams_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['skype_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['voip_tx_bytes']);
            array_push($wk_tx_bytes_array,$wk_tx_bytes_row['other_tx_bytes']);
            array_push($pro_wk_tx_bytes_array,$wk_tx_bytes_row['tcp_tx_bytes']);
            array_push($pro_wk_tx_bytes_array,$wk_tx_bytes_row['udp_tx_bytes']);
            array_push($pro_wk_tx_bytes_array,$wk_tx_bytes_row['icmp_tx_bytes']);
            
        }
        }


        $tx_pkt_query = "select sum(http_tx_pkts) as http_tx_pkts,sum(https_tx_pkts) as https_tx_pkts,sum(iperf_tx_pkts) as iperf_tx_pkts,sum(zoom_tx_pkts) as zoom_tx_pkts,sum(microsoft_teams_tx_pkts) as microsoft_teams_tx_pkts,sum(skype_tx_pkts) as skype_tx_pkts,sum(voip_tx_pkts) as voip_tx_pkts,sum(other_tx_pkts) as other_tx_pkts,sum(icmp_tx_pkts) as icmp_tx_pkts,sum(tcp_tx_pkts) as tcp_tx_pkts,sum(udp_tx_pkts) as udp_tx_pkts  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 24 hour";

        $tx_pkt_res = $db->query($tx_pkt_query);
        
        if ($tx_pkt_res->num_rows > 0)
        {
        $tx_pkt_array = array();
        $pro_tx_pkt_array = array();
        while($tx_pkt_row = $tx_pkt_res->fetch_assoc())
        {
            array_push($tx_pkt_array,$tx_pkt_row['http_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['https_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['iperf_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['zoom_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['microsoft_teams_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['skype_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['voip_tx_pkts']);
            array_push($tx_pkt_array,$tx_pkt_row['other_tx_pkts']);
            array_push($pro_tx_pkt_array,$tx_pkt_row['tcp_tx_pkts']);
            array_push($pro_tx_pkt_array,$tx_pkt_row['udp_tx_pkts']);
            array_push($pro_tx_pkt_array,$tx_pkt_row['icmp_tx_pkts']);
            
        }
        } 


        $wk_tx_pkt_query = "select sum(http_tx_pkts) as http_tx_pkts,sum(https_tx_pkts) as https_tx_pkts,sum(iperf_tx_pkts) as iperf_tx_pkts,sum(zoom_tx_pkts) as zoom_tx_pkts,sum(microsoft_teams_tx_pkts) as microsoft_teams_tx_pkts,sum(skype_tx_pkts) as skype_tx_pkts,sum(voip_tx_pkts) as voip_tx_pkts,sum(other_tx_pkts) as other_tx_pkts,sum(icmp_tx_pkts) as icmp_tx_pkts,sum(tcp_tx_pkts) as tcp_tx_pkts,sum(udp_tx_pkts) as udp_tx_pkts  from smoad_device_network_qos_stats_log where log_timestamp > now() - interval 1 week";

        $wk_tx_pkt_res = $db->query($wk_tx_pkt_query);
        
        if ($wk_tx_pkt_res->num_rows > 0)
        {
        $wk_tx_pkt_array = array();
        $pro_wk_tx_pkt_array = array();
        while($wk_tx_pkt_row = $wk_tx_pkt_res->fetch_assoc())
        {
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['http_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['https_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['iperf_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['zoom_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['microsoft_teams_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['skype_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['voip_tx_pkts']);
            array_push($wk_tx_pkt_array,$wk_tx_pkt_row['other_tx_pkts']);
            array_push($pro_wk_tx_pkt_array,$wk_tx_pkt_row['tcp_tx_pkts']);
            array_push($pro_wk_tx_pkt_array,$wk_tx_pkt_row['udp_tx_pkts']);
            array_push($pro_wk_tx_pkt_array,$wk_tx_pkt_row['icmp_tx_pkts']);
            
        }
        } 
    
?>
     
       
<script>
        var pie_chart_val = <?php echo json_encode($qos_array); ?>;
        console.log('pie_chart_val',pie_chart_val);
        var rx_byte_chart = new Chart(document.getElementById("rx-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels : [ "http", "https", "iperf", "zoom",
        				"microsoft_teams","skype","voip","other" ],
        		datasets : [ {
        			backgroundColor : [ "#E57C23", "#E966A0",
        					"#898121", "#A78295", "#78C1F3","#B5C99A","#4E4FEB","#D1D1D1" ],
        			data : pie_chart_val
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var pro_qos_array = <?php echo json_encode($pro_qos_array); ?>;
        console.log('pro_qos_array',pro_qos_array);
        var pro_rx_byte_chart = new Chart(document.getElementById("pro-rx-pie-chart"), {
        	type : 'pie',
        	data : { 
        		labels :["tcp","udp","icmp"],
        		datasets : [ {
        			backgroundColor : [ "#3AA6B9","#E7CEA6","#D1D1D1" ],
        			data : pro_qos_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var rx_pkt_array = <?php echo json_encode($rx_pkt_array); ?>;
        console.log('rx_pkt_array',rx_pkt_array);
        var rx_pkt_chart = new Chart(document.getElementById("rx-pkt-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels : [ "http", "https", "iperf", "zoom",
        				"microsoft_teams","skype","voip","other" ],
        		datasets : [ {
        			backgroundColor : [ "#E57C23", "#E966A0",
        					"#898121", "#A78295", "#78C1F3","#B5C99A","#4E4FEB","#D1D1D1" ],
        			data : rx_pkt_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var pro_rx_pkt_array = <?php echo json_encode($pro_rx_pkt_array); ?>;
        console.log('pro_rx_pkt_array',pro_rx_pkt_array);
        var pro_rx_pkt_chart = new Chart(document.getElementById("pro-rx-pkt-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels :["tcp","udp","icmp"],
        		datasets : [ {
        			backgroundColor : [ "#3AA6B9","#E7CEA6","#D1D1D1" ],
        			data : pro_rx_pkt_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var tx_bytes_array = <?php echo json_encode($tx_bytes_array); ?>;
        console.log('tx_bytes_array',tx_bytes_array);
        var tx_byte_chart =  new Chart(document.getElementById("tx-bytes-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels : [ "http", "https", "iperf", "zoom",
        				"microsoft_teams","skype","voip","other" ],
        		datasets : [ {
        			backgroundColor : [ "#E57C23", "#E966A0",
        					"#898121", "#A78295", "#78C1F3","#B5C99A","#4E4FEB","#D1D1D1" ],
        			data : tx_bytes_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var pro_tx_bytes_array = <?php echo json_encode($pro_tx_bytes_array); ?>;
        console.log('pro_tx_bytes_array',pro_tx_bytes_array);
        var pro_tx_byte_chart =  new Chart(document.getElementById("pro-tx-bytes-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels :["tcp","udp","icmp"],
        		datasets : [ {
        			backgroundColor : [ "#3AA6B9","#E7CEA6","#D1D1D1" ],
        			data : pro_tx_bytes_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>


<script>
        var tx_pkt_array = <?php echo json_encode($tx_pkt_array); ?>;
        console.log('tx_pkt_array',tx_pkt_array);
      var tx_pkt_chart =  new Chart(document.getElementById("tx-pkt-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels : [ "http", "https", "iperf", "zoom",
        				"microsoft_teams","skype","voip","other" ],
        		datasets : [ {
        			backgroundColor : [ "#E57C23", "#E966A0",
        					"#898121", "#A78295", "#78C1F3","#B5C99A","#4E4FEB","#D1D1D1" ],
        			data : tx_pkt_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
            }
           }
        });
</script>

<script>
        var pro_tx_pkt_array = <?php echo json_encode($pro_tx_pkt_array); ?>;
        console.log('pro_tx_pkt_array',pro_tx_pkt_array);
      var pro_tx_pkt_chart =  new Chart(document.getElementById("pro-tx-pkt-pie-chart"), {
        	type : 'pie',
        	data : {
        		labels :["tcp","udp","icmp"],
        		datasets : [ {
        			backgroundColor : [ "#3AA6B9","#E7CEA6","#D1D1D1" ],
        			data : pro_tx_pkt_array
        		} ]
        	},
            options: {
            responsive: true,
            legend: {
                position: 'right',
                labels: {
                    align: "center"
                }
            }
           }
        });
</script>

<script>
        

// Step 2: Modify the data or options of the chart instance
function ShowPastWeek() {
    rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_qos_array); ?>;
    rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_rx_pkt_array); ?>;
    pro_rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($pro_wk_qos_array); ?>;
    tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_bytes_array); ?>;
    tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($wk_tx_pkt_array); ?>;
    pro_tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($pro_wk_tx_pkt_array); ?>;
    pro_tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($pro_wk_tx_bytes_array); ?>;
    pro_rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($pro_wk_rx_pkt_array); ?>;
    
    // Step 3: Update the chart
    rx_byte_chart.update();
    rx_pkt_chart.update();
    tx_byte_chart.update();
    pro_tx_byte_chart.update();
    pro_rx_pkt_chart.update();
    pro_rx_byte_chart.update();
    tx_pkt_chart.update();
    pro_tx_pkt_chart.update();
    document.getElementById('PD').removeAttribute("disabled");
    document.getElementById('PW').setAttribute("disabled","disabled");
    var DisplayText = document.getElementById('DisplayText');
    DisplayText.innerHTML = 'Showing: Past 1 week';
}

function ShowPastDay() {
    rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($qos_array); ?>;
    pro_rx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($pro_qos_array); ?>;
    rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($rx_pkt_array); ?>;
    tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($tx_bytes_array); ?>;
    tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($tx_pkt_array); ?>;
    pro_tx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($pro_tx_pkt_array); ?>;
    pro_tx_byte_chart.config.data.datasets[0].data = <?php echo json_encode($pro_tx_bytes_array); ?>;
    pro_rx_pkt_chart.config.data.datasets[0].data = <?php echo json_encode($pro_rx_pkt_array); ?>;

    // Step 3: Update the chart
    rx_byte_chart.update();
    pro_rx_byte_chart.update();
    rx_pkt_chart.update();
    tx_byte_chart.update();
    tx_pkt_chart.update();
    pro_tx_pkt_chart.update();
    pro_tx_byte_chart.update();
    pro_rx_pkt_chart.update();
    var DisplayText = document.getElementById('DisplayText');
    document.getElementById('PW').removeAttribute("disabled");
    document.getElementById('PD').setAttribute("disabled","disabled");
    DisplayText.innerHTML = 'Showing: Past 24 Hours';
}

</script>

    
</body>
</html>

