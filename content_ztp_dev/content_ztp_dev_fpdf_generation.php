<?php
/*call the FPDF library*/
//include('../pdf-lib/fpdf184/fpdf.php');
require('fpdf.php');
$date = $_GET['date'];

session_start();

//ZTP-Device
$G_device_serialnumber = $_SESSION['ztp_dev_sn'];
$G_device_id = $_SESSION['ztp_dev_id'];
$G_device_details = $_SESSION['ztp_dev_details'];
$G_device_model = $_SESSION['ztp_dev_model'];
$G_device_model_variant = $_SESSION['ztp_dev_model_variant'];

include('../c/c_smoad_api_device_ztp.php');
include('../c/c_db_access.php');

$query = "select details from smoad_devices where serialnumber=\"$G_device_serialnumber\""; 
if($res = $db->query($query))
{	while($row = $res->fetch_assoc()) { $G_device_details = $row['details']; }
}

$model = $GLOBALS['G_device_model'];
$model_variant = $GLOBALS['G_device_model_variant'];

//if($G_device_serialnumber==null) { print "<b>Please login again !</b><br><br>Your session has timed out."; }

if($model=="spider") { $_model="SMOAD Spider"; }
else if($model=="spider2") { $_model="SMOAD Spider2"; }
else if($model=="beetle") { $_model="SMOAD Beetle"; }
else if($model=="bumblebee") { $_model="SMOAD BumbleBee"; }
else if($model=="vm") { $_model="SMOAD VM"; }

if($model_variant=="l2") { $_model_variant="L2 SD-WAN"; }
else if($model_variant=="l2w1l2") { $_model_variant="L2 SD-WAN (L2W1L2)"; }
else if($model_variant=="l3") { $_model_variant="L3 SD-WAN"; }
else if($model_variant=="mptcp") { $_model_variant="MPTCP"; }

if(sm_get_device_port_branching_by_serialnumber('LAN')) { $ports_array[] = 'lan'; }
if(sm_get_device_port_branching_by_serialnumber('WAN')) { $ports_array[] = 'wan1'; }
if(sm_get_device_port_branching_by_serialnumber('WAN2')) { $ports_array[] = 'wan2'; }
if(sm_get_device_port_branching_by_serialnumber('WAN3')) { $ports_array[] = 'wan3'; }
if(sm_get_device_port_branching_by_serialnumber('LTE1')) { $ports_array[] = 'lte1'; }
if(sm_get_device_port_branching_by_serialnumber('LTE2')){ $ports_array[] = 'lte2'; }
if(sm_get_device_port_branching_by_serialnumber('LTE3')){ $ports_array[] = 'lte3'; }
if(sm_get_device_port_branching_by_serialnumber('SD-WAN')){ $ports_array[] = 'sdwan'; }

function _unit_conversion(&$unit_value)
{	$unit=1; 
   $unit_name = "Kb";
   $unit_details = [];
   $unit_details['unit'] = $unit; $unit_name = "Kb"; $unit_details['unit_name'] = $unit_name;
   if($unit_value<=0) { $unit_value=1; } 
   $unit_value = $unit_value*8; //convert to bits
   if($unit_value>1100) { $unit=1000; $unit_details['unit'] = $unit; $unit_name = "Mb"; $unit_details['unit_name'] = $unit_name; $unit_value/=1000; } //Mb
   if($unit_value>1100) { $unit=1000; $unit_details['unit'] = $unit; $unit_name = "Gb"; $unit_details['unit_name'] = $unit_name; $unit_value/=1000; } //Gb
   
   $unit_value = number_format($unit_value, 1);
   return $unit_details['unit_name'];
}

//print_R($ports_array);

/*A4 width : 219mm*/
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetTitle('Monthly Consolidated Report');
$pdf->SetY(0);
$pdf->SetFont("Arial","B","13");
$pdf->SetXY(5, $pdf->GetY()+15);
$x = 15;
$y = 10;
$pdf->Cell( $x, $y, $pdf->Image('../i/logo1.png', 10, 7, 33.78), 0, 0, 'L', false );

$pageWidth = 210;
$pageHeight = 297;

/* disable margin
$margin = 8;
$pdf->SetDrawColor(30,30,30);
$pdf->Rect(4, 4, $pageWidth-$margin, $pageHeight-$margin, 'D'); //For A4
*/


$pdf->SetFont("Arial","B","11");
$pdf->SetXY(160, $pdf->GetY());
$pdf->Cell(10, 10, "Date: ".date("F j, Y"));

$pdf->SetFont("Arial","","10");
$pdf->SetXY(10, $pdf->GetY() + 7);
$pdf->Cell($x, $y, "Serial Number: ".$G_device_serialnumber);

$pdf->SetFont("Arial","","10");
$pdf->SetXY(10, $pdf->GetY() + 7);
$pdf->Cell($x, $y, "Details: ".$G_device_details);

$pdf->SetFont("Arial","","10");
$pdf->SetXY(10, $pdf->GetY() + 7);
$pdf->Cell($x, $y, "Model: ".$_model);

$pdf->SetFont("Arial","","10");
$pdf->SetXY(10, $pdf->GetY() + 7);
$pdf->Cell($x, $y, "Model Variant: ".$_model_variant);


$pdf->SetFont("Arial","B","13");
$pdf->SetXY(10, $pdf->GetY() + 18);
//$pdf->SetFillColor(211,211,211);
$pdf->Cell($x+100, $y, "Consolidated Report For the Month: ".date('F, Y', strtotime($date)));

$pdf->SetFont("Arial","B","12");
$pdf->SetXY(10, $pdf->GetY() + 10);
$pdf->Cell($x, $y+2, "Total Data Transferred:");

$border = 0;
$pdf->SetFont("Arial","","10");
$pdf->SetXY(12, $pdf->GetY() + 12);
$pdf->SetFillColor(68, 68, 68);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(30 ,10,'Port',$border,0,'C', true);
$pdf->Cell(30 ,10,'RX',$border,0,'C', true);
$pdf->Cell(30 ,10,'TX',$border,0,'C', true);
//$pdf->Cell(15 ,10,'Up',$border,0,'C', true);
$pdf->Cell(15 ,10,'Down',$border,0,'C', true);


$pdf->Cell(30 ,10,'Up Time',$border,0,'C', true);


$pdf->Cell(20 ,10,'Latency',$border,0,'C', true);
$pdf->Cell(25 ,10,'Jitter',$border,1,'C', true);/*end of line*/
/*Heading Of the table end*/
$pdf->SetFont('Arial','','10');
//$pdf->Cell(10 ,10,"----------------------------",$border,0);
$date = explode('-', $date);
$query = "SELECT 
AVG(avg_lan_rx_bytes_rate) avg_lan_rx_bytes_rate, AVG(avg_lan_tx_bytes_rate) avg_lan_tx_bytes_rate,
AVG(avg_wan1_rx_bytes_rate) avg_wan1_rx_bytes_rate, AVG(avg_wan1_tx_bytes_rate) avg_wan1_tx_bytes_rate,
AVG(avg_wan2_rx_bytes_rate) avg_wan2_rx_bytes_rate, AVG(avg_wan2_tx_bytes_rate) avg_wan2_tx_bytes_rate,
AVG(avg_lte1_rx_bytes_rate) avg_lte1_rx_bytes_rate, AVG(avg_lte1_tx_bytes_rate) avg_lte1_tx_bytes_rate,
AVG(avg_lte2_rx_bytes_rate) avg_lte2_rx_bytes_rate, AVG(avg_lte2_tx_bytes_rate) avg_lte2_tx_bytes_rate,
AVG(avg_lte3_rx_bytes_rate) avg_lte3_rx_bytes_rate, AVG(avg_lte3_tx_bytes_rate) avg_lte3_tx_bytes_rate,
AVG(avg_sdwan_rx_bytes_rate) avg_sdwan_rx_bytes_rate, AVG(avg_sdwan_tx_bytes_rate) avg_sdwan_tx_bytes_rate,
AVG(avg_wan1_latency) avg_wan1_latency, AVG(avg_wan1_jitter) avg_wan1_jitter,
AVG(avg_wan2_latency) avg_wan2_latency, AVG(avg_wan2_jitter) avg_wan2_jitter,
AVG(avg_lte1_latency) avg_lte1_latency, AVG(avg_lte1_jitter) avg_lte1_jitter,
AVG(avg_lte2_latency) avg_lte2_latency, AVG(avg_lte2_jitter) avg_lte2_jitter,
AVG(avg_lte3_latency) avg_lte3_latency, AVG(avg_lte3_jitter) avg_lte3_jitter,
AVG(avg_sdwan_latency) avg_sdwan_latency, AVG(avg_sdwan_jitter) avg_sdwan_jitter,
SUM(sum_lan_rx_bytes) sum_lan_rx_bytes, SUM(sum_lan_tx_bytes) sum_lan_tx_bytes,
SUM(sum_wan1_rx_bytes) sum_wan1_rx_bytes, SUM(sum_wan1_tx_bytes) sum_wan1_tx_bytes,
SUM(sum_wan2_rx_bytes) sum_wan2_rx_bytes, SUM(sum_wan2_tx_bytes) sum_wan2_tx_bytes,
SUM(sum_lte1_rx_bytes) sum_lte1_rx_bytes, SUM(sum_lte1_tx_bytes) sum_lte1_tx_bytes,
SUM(sum_lte2_rx_bytes) sum_lte2_rx_bytes, SUM(sum_lte2_tx_bytes) sum_lte2_tx_bytes,
SUM(sum_lte3_rx_bytes) sum_lte3_rx_bytes, SUM(sum_lte3_tx_bytes) sum_lte3_tx_bytes,
SUM(sum_sdwan_rx_bytes) sum_sdwan_rx_bytes, SUM(sum_sdwan_tx_bytes) sum_sdwan_tx_bytes,
SUM(sum_link_status_wan_up_count) sum_link_status_wan1_up_count, 
SUM(sum_link_status_wan2_up_count) sum_link_status_wan2_up_count, 
SUM(sum_link_status_lte1_up_count) sum_link_status_lte1_up_count, 
SUM(sum_link_status_lte2_up_count) sum_link_status_lte2_up_count, 
SUM(sum_link_status_lte3_up_count) sum_link_status_lte3_up_count, 
SUM(sum_link_status_sdwan_up_count) sum_link_status_sdwan_up_count,
SUM(sum_link_status_wan_down_count) sum_link_status_wan1_down_count, 
SUM(sum_link_status_wan2_down_count) sum_link_status_wan2_down_count, 
SUM(sum_link_status_lte1_down_count) sum_link_status_lte1_down_count, 
SUM(sum_link_status_lte2_down_count) sum_link_status_lte2_down_count, 
SUM(sum_link_status_lte3_down_count) sum_link_status_lte3_down_count, 
SUM(sum_link_status_sdwan_down_count) sum_link_status_sdwan_down_count,



SUM(sum_link_status_wan_repeat_up_count) as sum_link_status_wan1_repeat_up_count,
SUM(sum_link_status_wan2_repeat_up_count) as sum_link_status_wan2_repeat_up_count,
SUM(sum_link_status_wan3_repeat_up_count) as sum_link_status_wan3_repeat_up_count,
SUM(sum_link_status_lte1_repeat_up_count) as sum_link_status_lte1_repeat_up_count,
SUM(sum_link_status_lte2_repeat_up_count) as sum_link_status_lte2_repeat_up_count,
SUM(sum_link_status_lte3_repeat_up_count) as sum_link_status_lte3_repeat_up_count,
SUM(sum_link_status_sdwan_repeat_up_count) as sum_link_status_sdwan_repeat_up_count,

SUM(sum_link_status_any_repeat_up_count) as sum_link_status_any_repeat_up_count,
COUNT(log_timestamp) as count_log_timestamp



FROM smoad_device_consolidated_stats_log
WHERE device_serialnumber=\"$G_device_serialnumber\" and year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1]";
  //print $query;
if($res = $db->query($query))
{
 while($row = $res->fetch_assoc())
 {
     $rx_bytes_total = 0;
     $tx_bytes_total = 0;
     $upCount_total = 0;
     $downCount_total = 0;
     $latencyAvg = 0;
     $jitterAvg = 0;
	 
	 

           for ($i = 0; $i < count($ports_array); $i++) 
           {
                 /*if (!$row['sum_'.$ports_array[$i].'_rx_bytes']) {
                     $row['sum_'.$ports_array[$i].'_rx_bytes'] = '-';
                 }*/

                 if ($i % 2 == 0) {
                     $flag = true;
                 } else {
                     $flag = false;    
                 }
                 $pdf->SetXY(12, $pdf->GetY());
                 //$pdf->SetFillColor(216,68,48); // do not remove
                 $pdf->SetFillColor(233,239,245); 
                 $pdf->SetTextColor(0,0,0);
                 $pdf->Cell(30 ,10,strtoupper($ports_array[$i]),$border,0, 'C', $flag);
                 $rx_bytes = $row['sum_'.$ports_array[$i].'_rx_bytes'];
                 $tx_bytes = $row['sum_'.$ports_array[$i].'_tx_bytes'];
                 $rx_bytes_total += $rx_bytes;
                 $tx_bytes_total += $tx_bytes;

                 $upCount = $row['sum_link_status_'.$ports_array[$i].'_up_count'];
                 $downCount = $row['sum_link_status_'.$ports_array[$i].'_down_count'];
                 $upCount_total += $upCount;
                 $downCount_total += $downCount;

                 if ($downCount == null) {
                         $downCount = 0;
                 }    
                 if ($upCount == null) {
                         $upCount = 0;
                 }
				 
				 //GETTING NUMBER OF DAYS TO MINUTES IN MONTH USING COUNT OF ROW ENTRIES
				 $days_in_month = $row['count_log_timestamp'];
				 $minutes_in_month = $days_in_month * 1440; //MULTIPLYING BY 1440 GIVES US THE TOTAL MINUTES IN THE GIVEN DAYS
				 //OLD CODE $minutes_in_month_for_percentage = bcdiv($minutes_in_month,100,3);
				 $minutes_in_month_for_percentage = round($minutes_in_month/100,3);
				 
				 //LOGIC TO GET PERCENTAGE OF UP TIME IN A MONTH, AND TOTAL UP TIME FOR INDIVIDUAL PORTS
				 $port_repeat_count = $row['sum_link_status_'.$ports_array[$i].'_repeat_up_count'];
				 $total_up_time_port_mins = $port_repeat_count * 2;
				
				 //OLD CODE $percentage_up_port = bcdiv($total_up_time_port_mins,$minutes_in_month_for_percentage,1);
				 $percentage_up_port = round($total_up_time_port_mins/$minutes_in_month_for_percentage,1);
				 //OLD CODE $total_up_time_port_hours = intdiv($total_up_time_port_mins, 60).'H '.($total_up_time_port_mins % 60). 'M';
				 $total_up_time_port_hours = floor($total_up_time_port_mins/ 60) .'H '.($total_up_time_port_mins % 60). 'M';
				
				 

				
				 
                 $latency = $row['avg_'.$ports_array[$i].'_latency'];
                 $latencyAvg += $latency; 

                 $jitter = $row['avg_'.$ports_array[$i].'_jitter'];
                 $jitterAvg += $jitter; 

				$rx_bytes_unit = _unit_conversion($rx_bytes);
                 $pdf->Cell(30, 10,$rx_bytes." ".$rx_bytes_unit,$border,0,'C', $flag);
                 
                 $tx_bytes_unit = _unit_conversion($tx_bytes);
                 $pdf->Cell(30 ,10,$tx_bytes." ".$tx_bytes_unit,$border,0,'C', $flag);
                 
                // $pdf->Cell(15 ,10,$upCount,$border,0,'C', $flag);
                 $pdf->Cell(15 ,10,$downCount,$border,0,'C', $flag);
				 
				 
                 $pdf->Cell(30 ,10,$total_up_time_port_hours." (".$percentage_up_port."%)",$border,0,'C', $flag);
				 
				 
                 $pdf->Cell(20 ,10,round($latency, 2) . ' ms',$border,0,'C', $flag);
                 $pdf->Cell(25 ,10,round($jitter, 2) . ' ms',$border,1,'C', $flag);
           }
      }
      /*$pdf->SetXY(12, $pdf->GetY());

      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(30 ,10,'Total',$border,0,'C', false);
      $pdf->SetFont("Arial","","10");

	$rx_bytes_total_unit = _unit_conversion($rx_bytes_total);
      $pdf->Cell(30, 10,$rx_bytes_total." ".$rx_bytes_total_unit,$border,0,'C', false);
      $tx_bytes_total_unit = _unit_conversion($tx_bytes_total);
      $pdf->Cell(30 ,10,$tx_bytes_total." ".$tx_bytes_total_unit,$border,0,'C', false);
      //$pdf->Cell(15 ,10,$upCount_total,$border,0,'C', false);
      $pdf->Cell(15 ,10,$downCount_total,$border,0,'C', false); 
	  
	  
	  $pdf->Cell(30, 10,$total_up_time_any_hours." (".$percentage_up_any."%)",$border,0,'C', false);*/
	  
      ///$pdf->SetFont('Arial','B',12); Kiran
      ///$pdf->Cell(20 ,10,'Avg',$border,0,'C', false); Kiran
      ///$pdf->SetFont("Arial","","10"); Kiran
      ///$pdf->Cell(20 ,10,round($latencyAvg, 2) . ' ms',$border,0,'C', false); Kiran
      ///$pdf->Cell(25 ,10,round($jitterAvg, 2) . ' ms',$border,1,'C', false); Kiran

 }

$pdf->SetFont("Arial","B","12");
$pdf->SetXY(10, $pdf->GetY() + 2 + count($ports_array)*2);
$pdf->Cell($x, $y+2, "Day-wise breakup:");

$pdf->SetFont("Arial","","9");
$pdf->SetXY(12, $pdf->GetY() + 12);
$pdf->SetFillColor(68, 68, 68);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(20, 10,'Day',$border,0,'C', true);


$pdf->Cell(32, 10,'Port',$border,0,'C', true);
$pdf->Cell(20, 10,'RX',$border,0,'C', true);
$pdf->Cell(20, 10,'TX',$border,0,'C', true);
//$pdf->Cell(15 ,10,'Up',$border,0,'C', true);
$pdf->Cell(10 ,10,'Down',$border,0,'C', true);

$pdf->Cell(22,10, 'Up Time',$border,0,'C', true);

$pdf->Cell(30 ,10,'Latency',$border,0,'C', true);
$pdf->Cell(20 ,10,'Jitter',$border,1,'C', true);/*end of line*/

$pdf->SetXY(12, $pdf->GetY());

$query = "SELECT DATE_FORMAT(log_timestamp, '%d-%b-%Y') log_timestamp,
avg_lan_rx_bytes_rate, avg_lan_tx_bytes_rate,
avg_wan1_rx_bytes_rate, avg_wan1_tx_bytes_rate,
avg_wan2_rx_bytes_rate, avg_wan2_tx_bytes_rate,
avg_lte1_rx_bytes_rate, avg_lte1_tx_bytes_rate,
avg_lte2_rx_bytes_rate, avg_lte2_tx_bytes_rate,
avg_lte3_rx_bytes_rate, avg_lte3_tx_bytes_rate,
avg_sdwan_rx_bytes_rate, avg_sdwan_tx_bytes_rate,
avg_wan1_latency, avg_wan1_jitter,
avg_wan2_latency, avg_wan2_jitter,
avg_lte1_latency, avg_lte1_jitter,
avg_lte2_latency, avg_lte2_jitter,
avg_lte3_latency, avg_lte3_jitter,
avg_sdwan_latency, avg_sdwan_jitter,
sum_lan_rx_bytes, sum_lan_tx_bytes,
sum_wan1_rx_bytes, sum_wan1_tx_bytes,
sum_wan2_rx_bytes, sum_wan2_tx_bytes,
sum_lte1_rx_bytes, sum_lte1_tx_bytes,
sum_lte2_rx_bytes, sum_lte2_tx_bytes,
sum_lte3_rx_bytes, sum_lte3_tx_bytes,
sum_sdwan_rx_bytes, sum_sdwan_tx_bytes,
sum_link_status_wan_up_count as sum_link_status_wan1_up_count, 
sum_link_status_wan2_up_count, 
sum_link_status_lte1_up_count, 
sum_link_status_lte2_up_count, 
sum_link_status_lte3_up_count, 
sum_link_status_sdwan_up_count,
sum_link_status_wan_down_count as sum_link_status_wan1_down_count, 
sum_link_status_wan2_down_count, 
sum_link_status_lte1_down_count, 
sum_link_status_lte2_down_count, 
sum_link_status_lte3_down_count, 
sum_link_status_sdwan_down_count,

sum_link_status_wan_repeat_up_count as sum_link_status_wan1_repeat_up_count,
sum_link_status_wan2_repeat_up_count,
sum_link_status_wan3_repeat_up_count,
sum_link_status_lte1_repeat_up_count,
sum_link_status_lte2_repeat_up_count,
sum_link_status_lte3_repeat_up_count,
sum_link_status_sdwan_repeat_up_count,

sum_link_status_any_repeat_up_count

FROM smoad_device_consolidated_stats_log
WHERE device_serialnumber=\"$G_device_serialnumber\" and year(log_timestamp) = $date[0] and month(log_timestamp) = $date[1]";
$count = 0;
if($res = $db->query($query))
{
 while($row = $res->fetch_assoc())
 {	  if ($count % 2 == 0) { $flag = true; } else { $flag = false; }
 	  $count++;
                 
     $rx_bytes_total = 0;
     $tx_bytes_total = 0;
     $upCount_total = 0;
     $downCount_total = 0;
     $latencyAvg = 0;
     $jitterAvg = 0;
	 
	
	 
           for ($i = 0; $i < count($ports_array); $i++) 
           {
                 /*if (!$row['sum_'.$ports_array[$i].'_rx_bytes']) {
                     $row['sum_'.$ports_array[$i].'_rx_bytes'] = '-';
                 }*/
					  if($i==0) { $log_timestamp = $row['log_timestamp']; } else { $log_timestamp = ''; }

                 $pdf->SetXY(12, $pdf->GetY());
                 //$pdf->SetFillColor(216,68,48); // do not remove
                 $pdf->SetFillColor(233,239,245); 
                 $pdf->SetTextColor(0,0,0);
                 $pdf->Cell(25, 10,$log_timestamp,$border,0,'C', $flag);
                 $pdf->Cell(25 ,10,strtoupper($ports_array[$i]),$border,0, 'C', $flag);
                 $rx_bytes = $row['sum_'.$ports_array[$i].'_rx_bytes'];
                 $tx_bytes = $row['sum_'.$ports_array[$i].'_tx_bytes'];
                 $rx_bytes_total += $rx_bytes;
                 $tx_bytes_total += $tx_bytes;

                 $upCount = $row['sum_link_status_'.$ports_array[$i].'_up_count'];
                 $downCount = $row['sum_link_status_'.$ports_array[$i].'_down_count'];
                 $upCount_total += $upCount;
                 $downCount_total += $downCount;
				 

                 if ($downCount == null) {
                         $downCount = 0;
                 }    
                 if ($upCount == null) {
                         $upCount = 0;
                 }
				 //LOGIC TO GET PERCENTAGE OF UP TIME IN A DAY, AND TOTAL UP TIME FOR INDIVIDUAL PORTS
				 $port_repeat_count = $row['sum_link_status_'.$ports_array[$i].'_repeat_up_count'];
				 $total_up_time_port_mins = $port_repeat_count * 2;
				 
				 
				 //OLD CODE $percentage_up_port = bcdiv($total_up_time_port_mins,14.4,1);
				 $percentage_up_port = round($total_up_time_port_mins/14.4,1);
				 
				 //OLD CODE $total_up_time_port_hours = intdiv($total_up_time_port_mins, 60).'H '.($total_up_time_port_mins % 60). 'M';
				 $total_up_time_port_hours = floor($total_up_time_port_mins/ 60) .'H '.($total_up_time_port_mins % 60). 'M';
				 
				
				
                 $latency = $row['avg_'.$ports_array[$i].'_latency'];
                 $latencyAvg += $latency; 

                 $jitter = $row['avg_'.$ports_array[$i].'_jitter'];
                 $jitterAvg += $jitter; 

					  $rx_bytes_unit = _unit_conversion($rx_bytes);
                 $pdf->Cell(20, 10,$rx_bytes." ".$rx_bytes_unit,$border,0,'C', $flag);
					  $tx_bytes_unit = _unit_conversion($tx_bytes);
                 $pdf->Cell(20 ,10,$tx_bytes." ".$tx_bytes_unit,$border,0,'C', $flag);
                 //$pdf->Cell(15 ,10,$upCount,$border,0,'C', $flag);
                 $pdf->Cell(10 ,10,$downCount,$border,0,'C', $flag);
				 
				 
				 $pdf->Cell(30 ,10,$total_up_time_port_hours." (".$percentage_up_port."%)",$border,0,'C', $flag);
				 
				 
                 $pdf->Cell(20 ,10,round($latency, 2) . ' ms',$border,0,'C', $flag);
                 $pdf->Cell(25 ,10,round($jitter, 2) . ' ms',$border,1,'C', $flag);
           }
      }

 }



$pdf->Output();


?>
      