<html><head>

</head>
<body>
<?php 
error_reporting(5);

$_serialnumber = "100002"; 
$_link_status_wan = "down";                                                          
$_link_status_lte1 = "up";            
$_link_status_lte2 = "up";
$_signalstrength_lte1 = "good";
$_signalstrength_lte2 = "excellent";

$_ticket_subject="Test subject";
$_ticket_body="Test body";
$_ticket_prio=1;

print "<form method=\"POST\" action=\"api_device_status_log.php\" >
		<input type=\"hidden\" name=\"serialnumber\" value=\"$_serialnumber\" />
		
		<input type=\"hidden\" name=\"link_status_wan\" value=\"$_link_status_wan\" />
		<input type=\"hidden\" name=\"link_status_lte1\" value=\"$_link_status_lte1\" />
		<input type=\"hidden\" name=\"link_status_lte2\" value=\"$_link_status_lte2\" />
		
		<input type=\"hidden\" name=\"signal_strength_lte1\" value=\"$_signalstrength_lte1\" />
		<input type=\"hidden\" name=\"signal_strength_lte2\" value=\"$_signalstrength_lte2\" />
		
		<input type=\"hidden\" name=\"ticket_subject\" value=\"$_ticket_subject\" />
		<input type=\"hidden\" name=\"ticket_body\" value=\"$_ticket_body\" />
		<input type=\"hidden\" name=\"ticket_prio\" value=\"$_ticket_prio\" />
		
		<input type=\"submit\" name=\"submit_ok\" value=\"execute\" style=\"border:0;\" class=\"a_button_blue\" />
		</form>";
?>


</body>
</html>