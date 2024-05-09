<?php error_reporting(5);
$url = 'http://192.168.8.193/api/api_get_device_meta.php';
$data = array('command' => 'verify', 'serialnumber' => '100002');

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
		print "Tuple: ".$tuples[0]." - ".$tuples[1]." \n";
		
		if($tuples[0]=="result") { $result=$tuples[1]; }
	}
}

print "Query result: $result \n";

?>