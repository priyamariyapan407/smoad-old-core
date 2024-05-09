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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>
<body>
<?php
	include('../c/c_db_access.php');
   ini_set('display_errors', 1);
   error_reporting(E_ALL);


     //Link up/down count metrics
     function _get_fw_drop_count($db)
     {	$query2 = "SELECT DATE_FORMAT(log_timestamp, '%H:%i') log_timestamp, pkt_count FROM smoad_fw_log
                 WHERE action='drop' and log_timestamp>=DATE_SUB(NOW(),INTERVAL 24 HOUR)";
         if($res2 = $db->query($query2))
         {	
             while($row2 = $res2->fetch_assoc())
             {
             	  $_graph_data[0][] = $row2['log_timestamp'];
                 $_graph_data[1][] = $row2['pkt_count'];
             }
         }
         return $_graph_data;
     }
        
?>

    <!--<div class="topnav" style="display:none" >
    <a class="active" onclick="myFunction()" >WAN1</a>
    <a onclick="myFunction.call(this.id)">LAN</a>
    <a onclick="myFunction()">LTE1</a>
    <a onclick="myFunction()">LTE2</a>
    </div>-->
<!-- Graph placeholders -->
    <div id="chart-container" style="position: relative; height:30vh; width:80vw">
        <canvas id="myChart4" style="width:100%;max-width:1000px;height:200px"></canvas><br><br>
    </div>
    <script>
//Place where each graph is called and rendered


        var ctx4 = document.getElementById("myChart4");
        var graphDataFromDB = <?php echo json_encode(_get_fw_drop_count($db)); ?>
        
        graphLabels = graphDataFromDB[0];
        graphData = graphDataFromDB[1];
        portMetric = 'Firewall Packet Drop Count';
        renderGraph(ctx4, graphLabels, graphData, portMetric, 'Pkt Count');


        function renderGraph(ctx, graphLabels, graphData, portMetric, yAxisLabel, chartType)
        {	if (chartType === null || chartType === undefined) { chartType = 'line'; }
            var color = "rgba(75,192,192,1)";
            var pointHoverBorderColor = "rgba(220,220,220,1)";
            var backgroundColor = "rgba(75,192,192,0.4)";
            
            color = "rgba(216,68,48,0.98)";
            pointHoverBorderColor = "rgba(216,68,48,0.98)";
            backgroundColor = "rgba(216,68,48,0.4)";
            

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
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }

</script>         
</body>
</html>

