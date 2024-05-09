<br><br><br>

<?php

$mons = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");


$yearcurrent=date("Y");
$reached_end = false;
print "<table class=\"list_items\" style=\"width:99%;font-size:10px;\" >";

for($y=$yearcurrent;$y>=2022;$y--) 
{
	
  print "<tr><td>$y</td>";
  $monthcurrent=date('m');

	  
  for($m=1;$m<=12;$m++) 
  {	
  		$mname = $mons[$m];
  		$enable_link = false;
  		$_m = sprintf("%02d", $m); //month in 01,02 format
  		$datecurrent = $y."-".$_m."-01";
  		$query = "select count(*) row_count FROM smoad_device_status_log
			WHERE device_serialnumber=\"$G_device_serialnumber\" and year(log_timestamp) = $y and month(log_timestamp) = $_m";
		if($res = $db->query($query)) { while($row = $res->fetch_assoc()) {	$row_count = $row['row_count']; if($row_count>0) { $enable_link=true; } } }
  		
  		if($reached_end==false && $enable_link==true)
  		{ print "<td><a href='index.php?page=ztp_dev_status_log&date=".urlencode($datecurrent)."&skey=$session_key' >$mname</a></td>"; }
  		else { print "<td>-</td>"; }
  		if($monthcurrent==$m && $yearcurrent==$y) { $reached_end=true; }
  }
  print "</tr>";
  
}
print "</table>";


?>
                     