<?php 

//generic all purpose button-link
function api_button($link, $label, $color)
{	print "<form method=\"POST\" action=\"$link\" >
		<input type=\"submit\" name=\"submit_ok\" value=\"$label\" style=\"border:0;\" class=\"a_button_$color\" />
		</form>";
}

//generic all purpose button-link with confirm
function api_button_confirm($link, $label, $confirm_message, $color)
{	if($confirm_message=="defaultwarning") { $confirm_message="onclick=\"return confirm('Are you sure?')\""; }
	else if($confirm_message=="nowarning") { $confirm_message=""; }
	print "<form method=\"POST\" action=\"$link\" >
		<input type=\"submit\" name=\"submit_ok\" value=\"$label\" style=\"border:0;\" class=\"a_button_$color\" $confirm_message />
		</form>";
}

//generic all purpose button for POST/submit 
function api_button_post($label, $confirm_message, $color)
{	if($confirm_message=="defaultwarning") { $confirm_message="onclick=\"return confirm('Are you sure?')\""; }
	else if($confirm_message=="nowarning") { $confirm_message=""; }
	else { $confirm_message="onclick=\"return confirm('$confirm_message')\""; }
	print "<input type=\"submit\" name=\"submit_ok\" value=\"$label\" style=\"border:0;\" class=\"a_button_$color\" $confirm_message />";
}

//generic all purpose form hidden tag
function api_input_hidden($name, $value) { print "<input type=\"hidden\" name=\"$name\" value=\"$value\" />"; }

//generic all purpose form post tag
function api_form_post($link) { print "<form method=\"POST\" action=\"$link\" >"; }

//API to perform a post to remote server (API) and to get results
function api_remote_post($url, $post_data)
{	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'timeout' => 15,  //15 Seconds
	        'content' => http_build_query($post_data)
	    ),
    	'ssl' => array(  //to support https
           'verify_peer' => false,
           'verify_peer_name' => false,
        ),
	);
	$context  = stream_context_create($options);
	$result = file_get_contents($url, false, $context);
	if ($result === FALSE) { return null; }
	else 
	{	$result=chop($result);
		if($result=="fail") { return null; } 
		return $result;
	}
	
	return null;
}

function api_generate_device_api_keys(&$api_prikey, &$api_pubkey)
{	
	$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 1024,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
	);
	
	// Create the private and public key
	$res = openssl_pkey_new($config);
	
	// Extract the private key into $private_key
	openssl_pkey_export($res, $api_prikey);
	
	// Extract the public key into $public_key
	$api_pubkey = openssl_pkey_get_details($res);
	$api_pubkey = $api_pubkey["key"];
	
	$api_prikey = bin2hex($api_prikey);
	$api_pubkey = bin2hex($api_pubkey);
}

function api_encrypt_to_hex($source, &$encryptedhex, $pubkey)
{	$encryptedhex = '';
	foreach (str_split($source, 117) as $chunk)
	{	if (openssl_public_encrypt($chunk, $_encrypted, $pubkey)) { $encryptedhex .= $_encrypted; }
		else { return false; }
	}
	$encryptedhex = bin2hex($encryptedhex);
	return true;
}

function api_dencrypt_from_hex($encryptedhex, &$decrypted, $prikey)
{	$decrypted = '';
    	foreach (str_split(hex2bin($encryptedhex), 128) as $chunk) 
	{	if(openssl_private_decrypt($chunk, $_decrypted, $prikey)==false) { return false; }
         	$decrypted .= $_decrypted;
	}
	return true;
}

function api_contains_html($string) { return preg_match("/<[^<]+>/",$string,$m) != 0; }

function api_prevent_sql_injection($db, $input_string)
{	if(api_contains_html($input_string)) { return null; }
	$output_string = strip_tags(mysqli_real_escape_string($db, $input_string));
	return $output_string;
}

//generate random hash but not starting with zero
function randHash($len=32) 
{  $_hash = "0";
	while($_hash[0]=="0") { $_hash = substr(md5(openssl_random_pseudo_bytes(20)),-$len); }
	return $_hash; 
}

?>
