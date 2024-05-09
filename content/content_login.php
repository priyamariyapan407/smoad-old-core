<?php error_reporting(5); session_start(); session_unset(); session_destroy(); ?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php include('c/c_align.php'); ?>
<title>SMOAD Networks - SMOAD CORE - Login</title>
<link rel="icon" href="i/favicon.png" sizes="16x16" type="image/png">
<style>
/* geometric font */
@font-face {
    font-family: "Gilroy";
    src: url("c/Gilroy-Regular.ttf");
}
@font-face {
    font-family: "Gilroy";
    src: url("c/Gilroy-Bold.ttf");
    font-weight: bold;
}
input.text_style {border-width:1px;border-style:ridge;border-color:#C6C6C6;border-collapse:collapse;color:#111;font-family:'Gilroy', arial;
	font-size:14px;height:30px;padding:6px 4px;border-radius:2px;min-width:160px;background-color:#fff;}
.a_button_black {background-color:#020202;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}
div.login_box {margin-top:20px;margin-left: calc(50% - 305px);width:610px;
	background-color:#fff;border-width:1px;border-style:ridge;border-color:#fafafa;
	box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
	border-radius: 4px;padding: 4px;
	}
</style>

</head><body style="background-color:#f2f2f2;font-family:'Gilroy', arial;font-size:14px;letter-spacing:0.2px;">

<div class="login_box">
<table style="background-color:#ffffff;width: 100%;width:600px;" cellpadding="2" cellspacing="2" >
<tr><td align="center"><img style="padding:10px;object-fit:cover;width:140px;" src="i/logo1.png"></td></tr>
<tr><td align="center" style="color:#111111;font-size:12px;"><!--SMOAD Orchestrator--><br>
<br><br><form method="POST" action="c/c_login.php">
<table summary="" align="center" >
<tr><td>Login &nbsp;<input class="text_style" type="text" name="username" ></td>
<td width="10" > </td>
<td>Password &nbsp;<input class="text_style" type="password" name="password" ></td></tr>
</table>
<br><br>
<?php api_button_post("Login", "nowarning", "black"); ?>
</form></td></tr>
</table>
</div>

</body></html>