<?php 

$query = "select email1 FROM profile where id = 1";
if($res = $db->query($query))
{	while($row = $res->fetch_assoc())
	{	$G_root_email1 = $row['email1']; }
}


function api_send_mail_for_alert($subject, $details, $mail_id)
{	$details = str_replace("<br>", "\n", $details);
	$details = str_replace("<b>", "", $details);
	$details = str_replace("</b>", "", $details);
   $to = bin2hex($mail_id);
	$headers = bin2hex("From: noreply@smoad.io");
	$subject = bin2hex($subject);
	
	//append timestamp
	$t=time(); $timestamp = date("Y-m-d H:i:s",$t);
   $timestamp = chop($timestamp);
   $details .= "Timestamp: $timestamp";
   
	$txt = bin2hex($details);
	$server_select = rand(1,2);
	if($server_select==1) { $url = 'http://the-toffee-project.org/mail/mail.php'; }
	else if($server_select==2) { $url = 'http://sareesaremypassion.org/mail/mail.php'; }
	
	//$url = 'http://the-toffee-project.org/mail/mail.php';
	$url = 'http://sareesaremypassion.org/mail/mail.php'; 

	//$url = 'https://smoad.io/mail/mail.php';
	$url = $url."?to=$to"."&headers=$headers"."&subject=$subject"."&txt=$txt";
	file_get_contents($url, false, $context);
}

function api_send_mail_for_alert_to_customer($db, $subject, $details, $customer_id)
{	$email1 = null;
	if($customer_id==null || $customer_id=='notset') { return; } //not associated with any customer
   $query = "select email1 FROM smoad_customers where id = $customer_id";
	if($res = $db->query($query))
	{	while($row = $res->fetch_assoc())
		{	$email1 = $row['email1']; }
	}
	if($email1==null) { return; } //email is not set
   api_send_mail_for_alert($subject, $details, $email1);
}

?>
