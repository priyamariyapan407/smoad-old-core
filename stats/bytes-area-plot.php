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
        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-zoom/1.2.0/chartjs-plugin-zoom.js"></script></head>
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

        // Create connection
   //     $db = new mysqli($servername, $username, $password, 'smoad');
   //     mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        //Unit conversion function for rx and tx graphs
    function _unit_conversion($db, $metric, $serialnumber) {
            $unit=1; 
            $unit_name = "Kb/s";
            $unit_details = [];
            $unit_details['unit'] = $unit; $unit_name = "Kb/s"; $unit_details['unit_name'] = $unit_name;
            $query = "select  max(CAST($metric AS DECIMAL(10,2))) max_bits from smoad_device_network_stats_log 
        where log_timestamp>=DATE_SUB(NOW(),INTERVAL 200 HOUR) and device_serialnumber=\"$serialnumber\"";
            if($res = $db->query($query))
            {   while($row = $res->fetch_assoc())
                { 
                $max_bits = $row['max_bits'];
                if($max_bits<=0) { $max_bits=1; } 
                $max_bits = $max_bits*8; //convert to bits
                if($max_bits>1100) { $unit=1000; $unit_details['unit'] = $unit; $unit_name = "Mb/s"; $unit_details['unit_name'] = $unit_name; $max_bits/=1000; } //Mb
                if($max_bits>1100) { $unit=1000000; $unit_details['unit'] = $unit; $unit_name = "Gb/s"; $unit_details['unit_name'] = $unit_name; $max_bits/=1000000; } //Gb
                }
        }
            return $unit_details;
    }
    
    
        //--------PAST DAY----------------------------------------------------------------------------------------
        
        //Link up/down count metrics for past day
        function _get_link_status_updown_count($db, $port, $serialnumber) {
            $upCountMetric = 'link_status_'."$port".'_up_count';
            $downCountMetric = 'link_status_'."$port".'_down_count';
            
            $query2 = "SELECT DATE_FORMAT(log_timestamp, '%d-%c %H:%i') log_timestamp,
                    $upCountMetric upCountMetric, $downCountMetric downCountMetric 
                    FROM smoad_device_status_log
                    WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";

    
            if($res2 = $db->query($query2))
            {  
                if ($res2->num_rows > 0)
                { 
                while($row2 = $res2->fetch_assoc())
                {
                    $_graph_data[0][] = $row2['log_timestamp'];
                    $_graph_data[1][] = $row2['upCountMetric'];
                    $_graph_data[2][] = $row2['downCountMetric'];
                }
        }
       else
        {
            $_graph_data[0][] = "No Data";
            $_graph_data[1][] = 0;
            $_graph_data[2] = "No Data";
        }
        }
         return $_graph_data;
        
        }
        // return graph data per port for each provided metric for past day
        function _get_graph_data_per_port_from_db($db, $port, $option, $serialnumber) {
        $metric = $port."_".$option; 
            $unit_details = [];
            if ($option == 'rx_bytes_rate' || $option == 'tx_bytes_rate' ){
                $unit_details = _unit_conversion($db, $metric, $serialnumber);
            }
            $query2 = "SELECT DATE_FORMAT(log_timestamp, '%d-%c %H:%i') log_timestamp,
                    TRUNCATE($metric, 2) as metric
                    FROM smoad_device_network_stats_log
                          WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";

            if($res2 = $db->query($query2))
            {   
                if ($res2->num_rows > 0)
                {
                while($row2 = $res2->fetch_assoc())
                {
                    $_graph_data[0][] = $row2['log_timestamp'];
                    if ($option == 'rx_bytes_rate' || $option == 'tx_bytes_rate' ){
                $_graph_data[1][] = $row2['metric']*8/$unit_details['unit'];
                //$_graph_data[1][] = $row2['metric']*8/1000;
                        $_graph_data[2] = $unit_details['unit_name']; //"kb"; //
                    } else {
                        $_graph_data[1][] = $row2['metric'];
            }        
            $temp = $row2['log_timestamp'];
                }
            
           
        }
        else
        {
            $_graph_data[0][] = "No Data";
            $_graph_data[1][] = 0;
            $_graph_data[2] = "No Data";
        }
        }
         return $_graph_data;
        
        }

    
        //--------PAST WEEK--------------------------------------------------------------------------------------
        
        //Changes - Date Format, Duration(168 Hour), Week and day division.
        //Link up/down count metrics for past week
        function _get_link_status_updown_count_week($db, $port, $serialnumber) {
            $upCountMetric = 'link_status_'."$port".'_up_count';
            $downCountMetric = 'link_status_'."$port".'_down_count';
            
            $query2 = "SELECT DATE_FORMAT(log_timestamp, '%d-%c %H:%i') log_timestamp,
                    $upCountMetric upCountMetric, $downCountMetric downCountMetric 
                    FROM smoad_device_status_log
                    WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 168 HOUR)";
            if($res2 = $db->query($query2))
            {   
                while($row2 = $res2->fetch_assoc())
                {
                    $_graph_data[0][] = $row2['log_timestamp'];
                    $_graph_data[1][] = $row2['upCountMetric'];
                    $_graph_data[2][] = $row2['downCountMetric'];
                }
            }
            return $_graph_data;
        }
        // return graph data per port for each provided metric for past week
        function _get_graph_data_per_port_from_db_week($db, $port, $option, $serialnumber) {
        $metric = $port."_".$option; 
            $unit_details = [];
            if ($option == 'rx_bytes_rate' || $option == 'tx_bytes_rate' ){
                $unit_details = _unit_conversion($db, $metric, $serialnumber);
            }
            $query2 = "SELECT DATE_FORMAT(log_timestamp, '%d-%c %H:%i') log_timestamp,
                    TRUNCATE($metric, 2) as metric
                    FROM smoad_device_network_stats_log
                          WHERE device_serialnumber = \"$serialnumber\" and log_timestamp>=DATE_SUB(NOW(),INTERVAL 168 HOUR)";
            if($res2 = $db->query($query2))
            {   
                while($row2 = $res2->fetch_assoc())
                {
                    $_graph_data[0][] = $row2['log_timestamp'];
                    if ($option == 'rx_bytes_rate' || $option == 'tx_bytes_rate' ){
                $_graph_data[1][] = $row2['metric']*8/$unit_details['unit'];
                //$_graph_data[1][] = $row2['metric']*8/1000;
                        $_graph_data[2] = $unit_details['unit_name']; //"kb"; //
                    } else {
                        $_graph_data[1][] = $row2['metric'];
            }        
            $temp = $row2['log_timestamp'];
                }
            }
            return $_graph_data;
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
        
        <div id="chart-container" style="position: relative; height:30vh; width:80vw">

        <canvas id="myChart3" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
        <canvas id="myChart2" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
        <canvas id="myChart" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
        <canvas id="myChart1" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
        <canvas id="myChart4" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
        <canvas id="myChart5" style="width:100%;max-width:1000px;height:150px"></canvas><br><br>
       </div>
       
        <div id="chart-container-week" style="position: relative ; display:none; height:30vh; width:80vw">
        
        <canvas id="myChart8" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
        <canvas id="myChart9" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
        <canvas id="myChart6" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
        <canvas id="myChart7" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
        <canvas id="myChart10" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
         <canvas id="myChart11" style="width:100%;max-width:1000px;height:45px"></canvas><br><br>
     
        
        </div>

       
    
    
    <script>
//Place where each graph is called and rendered
        var ctx = document.getElementById("myChart");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db($db, $port_nw_stats, 'latency', $serialnumber)); ?>
    
    graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];
        
        renderGraph(ctx, graphLabels, graphData, 'Latency (ms)', 'Latency');
        
        var ctx1 = document.getElementById("myChart1");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db($db, $port_nw_stats, 'jitter', $serialnumber)); ?>
       
        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        renderGraph(ctx1, graphLabels, graphData, 'Jitter (ms)', 'Jitter');


        var ctx3 = document.getElementById("myChart3");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db($db, $port_nw_stats, 'rx_bytes_rate', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Received ' + '('+graphDataFromDB[2]+')';

        renderGraph(ctx3, graphLabels, graphData, portMetric, 'rx_bytes_rate');

        var ctx2 = document.getElementById("myChart2");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db($db, $port_nw_stats, 'tx_bytes_rate', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Transferred  ' + '('+graphDataFromDB[2]+')';
        
        renderGraph(ctx2, graphLabels, graphData, portMetric, 'tx_bytes_rate');


        var ctx4 = document.getElementById("myChart4");

        var graphDataFromDB = <?php echo json_encode(_get_link_status_updown_count($db, $port_device_stats, $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Link status UP Count';
        port = <?php echo json_encode($port_device_stats); ?>

        renderGraph(ctx4, graphLabels, graphData, portMetric, 'link_status_'+port+'_up_count', 'bar');

        var ctx5 = document.getElementById("myChart5");

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[2];

        portMetric = 'Link status DOWN Count';

        renderGraph(ctx5, graphLabels, graphData, portMetric, 'link_status_'+port+'_down_count', 'bar');
        
        
        //PAST WEEK CHART RENDERS



        var ctx6 = document.getElementById("myChart6");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db_week($db, $port_nw_stats, 'latency', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        renderGraph(ctx6, graphLabels, graphData, 'Latency (ms)', 'Latency');
        

        var ctx7 = document.getElementById("myChart7");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db_week($db, $port_nw_stats, 'jitter', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        renderGraph(ctx7, graphLabels, graphData, 'Jitter (ms)', 'Jitter');


        var ctx8 = document.getElementById("myChart8");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db_week($db, $port_nw_stats, 'rx_bytes_rate', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Received ' + '('+graphDataFromDB[2]+')';

        renderGraph(ctx8, graphLabels, graphData, portMetric, 'rx_bytes_rate');


        var ctx9 = document.getElementById("myChart9");

        var graphDataFromDB = <?php echo json_encode(_get_graph_data_per_port_from_db_week($db, $port_nw_stats, 'tx_bytes_rate', $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Transferred  ' + '('+graphDataFromDB[2]+')';

        renderGraph(ctx9, graphLabels, graphData, portMetric, 'tx_bytes_rate');


        var ctx10 = document.getElementById("myChart10");

        var graphDataFromDB = <?php echo json_encode(_get_link_status_updown_count_week($db, $port_device_stats, $serialnumber)); ?>

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[1];

        portMetric = 'Link status UP Count';

        port = <?php echo json_encode($port_device_stats); ?>

        renderGraph(ctx10, graphLabels, graphData, portMetric, 'link_status_'+port+'_up_count', 'bar');


        var ctx11 = document.getElementById("myChart11");

        graphLabels = graphDataFromDB[0];

        graphData = graphDataFromDB[2];

        portMetric = 'Link status DOWN Count';

        renderGraph(ctx11, graphLabels, graphData, portMetric, 'link_status_'+port+'_down_count', 'bar');

        function renderGraph(ctx, graphLabels, graphData, portMetric, yAxisLabel, chartType){
            if (chartType === null || chartType === undefined) {
                chartType = 'line';
            }
            var color = "rgba(75,192,192,1)";
            var pointHoverBorderColor = "rgba(220,220,220,1)";
            var backgroundColor = "rgba(75,192,192,0.4)";
            if (yAxisLabel === "rx_bytes_rate") {
                color = "rgba(33, 145, 80,0.9)";
                pointHoverBorderColor = "rgba(33, 145, 80,0.9)";
                backgroundColor = "rgba(33, 145, 80,0.4)";
            }
            if (yAxisLabel === "tx_bytes_rate") {
                color = "rgba(41,129,228,0.9)";
                pointHoverBorderColor = "rgba(41,129,228,0.9)";
                backgroundColor = "rgba(41,129,228,0.4)";
            }
            if (yAxisLabel === "Jitter") {
                color = "rgba(216,68,48,0.98)";
                pointHoverBorderColor = "rgba(216,68,48,0.98)";
                backgroundColor = "rgba(216,68,48,0.4)";
            }

            if (yAxisLabel === "Latency") {
                color = "rgba(255, 195, 0, 0.9)";
                pointHoverBorderColor = "rgba(255, 195, 0, 0.9)";
                backgroundColor = "rgba(255, 195, 0, 0.4)";
            }
            if (yAxisLabel.includes("up_count")) {
                color = "rgba(133, 200, 138, 1)";
                pointHoverBorderColor = "rgba(195, 229, 174,0.4)";
                backgroundColor = "rgba(107, 203, 119, 1)";
            }

            if (yAxisLabel.includes("down_count")) {
                color = "rgba(216, 33, 72,0.4)";
                pointHoverBorderColor = "rgba(232, 58, 20, 1)";
                backgroundColor = "rgba(137, 15, 13, 1)";
            }
            var data = {
            labels: graphLabels,
            datasets: [
                {
                    label: portMetric,
                    fill: true,
                    lineTension: 0.1,
                    backgroundColor: backgroundColor,
                    borderColor: color,
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: color,
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: color,
                    pointHoverBorderColor: pointHoverBorderColor,
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    borderWidth: 2, //line thickness
                    data: graphData,
                }]
            };
            var myLineChart = new Chart(ctx, {
                type: chartType,
                data: data,
                options : {
                    responsive: true,
            maintainAspectRatio: true,
            plugins: {
                        zoom: 
                        {
                            pan:
                            {enabled: true,mode:'x'},
                            zoom: {wheel: {enabled: true,},mode: 'x'}       
                        },
                        },
                    //XY-axis labelling and tick configuration    
                    scales: {
                        yAxes: [{
                        ticks: {
                            autoskip: true,
                            autoSkipPadding: 20
                        },
                        scaleLabel: {
                            display: true,
                            labelString: yAxisLabel
                        }
                        }],
                        xAxes: [{
                            barPercentage: 0.1,
                        ticks: {
                            autoskip: true,
                            autoSkipPadding: 20
                        },
                        
                        scaleLabel: {
                            display: true,
                            labelString: 'Timestamp'
                        }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                return " "+portMetric + " " + tooltipItem.yLabel.toFixed(2);
                            }
                        }
                    }
                }
            });
        }      


           function myFunction(id) {
            var x = document.getElementById("chart-container");
            if (x.style.display === "none") {
                x.style.display = "inline-block";
            } else {
                x.style.display = "none";
            }
        }
  
        
        
        function ShowPastDay() {
        var x = document.getElementById("chart-container-week");
        var y = document.getElementById("chart-container");
        var z = document.getElementById("DisplayText");
        if (y.style.display === "none") {
        x.style.display = "none";
        y.style.display ="block";
        document.getElementById("PW").disabled = false;
        document.getElementById("PD").disabled = true;
        }
        z.innerHTML = "Showing: Past 24 Hours";
        }
        
        function ShowPastWeek() {
        var x = document.getElementById("chart-container");
        var y = document.getElementById("chart-container-week");
        var z = document.getElementById("DisplayText");
        if (y.style.display === "none") {
        x.style.display = "none";
        y.style.display ="block";
        document.getElementById("PW").disabled = true;
        document.getElementById("PD").disabled = false;
        }
        z.innerHTML = "Showing: Past Week";
        }
      </script>

</script>         
</body>
</html>

