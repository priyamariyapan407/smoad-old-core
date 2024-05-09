<html><head>

</head>
<body>
<?php 
error_reporting(5);

$_username = "kiran"; $_password = "Welcome123";

print "<form method=\"POST\" action=\"http://localhost/api/device/api_remote_auth.php\" >
		<input type=\"hidden\" name=\"username\" value=\"$_username\" />
		<input type=\"hidden\" name=\"password\" value=\"$_password\" />
		<input type=\"submit\" name=\"submit_ok\" value=\"execute\" style=\"border:0;\" class=\"a_button_blue\" />
		</form>";
?>

</body>
</html>