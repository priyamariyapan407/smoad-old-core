<?php error_reporting(5);
$url = 'http://localhost/api/api_remote_auth.php';
$data = array('username' => 'kiran', 'password' => 'Welcome123', 'serialnumber' => '100001');

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }
else 
{	$result=chop($result);
	$lines = explode("\n", $result);
	foreach($lines as $line) 
	{  //print "$line \n";
		$tuples = explode(":", $line);
		print $tuples[0]." - ".$tuples[1]." \n";
	}
}
?>