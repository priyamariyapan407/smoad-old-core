<?php include('c/c_check_login.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<?php print "<title>$page_title</title>"; ?>	
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-Control" content="no-cache">
<link rel="icon" href="i/favicon.png" sizes="16x16" type="image/png">
<style type="text/css">

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

#hr_style {height:1px;border-width:0;color:#999;background-color:#aaa;}
.a_button_red {background-color:#D84430;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}
.a_button_green {background-color:#4d916a;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}
.a_button_blue {background-color:#2981e4;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}
.a_button_orange {background-color:#ff5c00;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}
.a_button_black {background-color:#020202;padding:8px 14px;border-radius:2px;color:#fff;font-size:14px;text-decoration:none;font-weight:bold;}


table.grid_style {border-width:1px;border-spacing:2px;border-style:ridge;border-color:#C6C6C6;border-collapse:collapse;background-color:white;}
table.grid_style th {border-width:1px;padding:6px;border-style:ridge;border-color:#B6B6B6;background-color:white;font-size:12px;}
table.grid_style td {border-width:1px;padding:6px;border-style:ridge;border-color:#C6C6C6;background-color:white;font-size:12px;}
#table_top_heading {background-color:gray;font-weight:bold;color:white;text-decoration:none;font-size:12px;}
pre {border-width:1px;border-collapse:collapse;background-color:#eee;border-color:#C6C6C6;font-family:courier;font-size:13px;padding:10px;}

select {border-width:0px;border-collapse:collapse;border-color:#C6C6C6;font-family: 'Gilroy', arial;font-size:14px;padding:6px 4px;border-radius:2px;min-width:250px;color:#fff;background-color:#D84430;}
input.text_style {border-width:1px;border-style:ridge;border-color:#C6C6C6;border-collapse:collapse;font-family: 'Gilroy', arial;color:#111;font-size:14px;height:15px;padding:6px 4px;border-radius:2px;min-width:160px;}

table.config_settings {background-color:white;padding:8px;font-size:14px;}
table.config_settings th {padding:8px;border-color:#B6B6B6;background-color:white;}
table.config_settings td {padding:8px;background-color:white;}

table.list_items {background-color:white;border-collapse:collapse;}
table.list_items th {padding:6px;background-color:#444;font-size:12px;text-align: left;color:#fff;}
table.list_items td {padding:6px;font-size:12px;}
table.list_items tr:nth-child(odd) { background-color: #e9eff5; }
table.list_items tr:hover {background-color: #e0e2e5;}

table.list_items2 {background-color:white;border-collapse:collapse;}
table.list_items2 th {padding:6px;background-color:#444;font-size:12px;text-align: left;color:#fff;}
table.list_items2 td {padding:6px;font-size:12px;}
table.list_items2 tr:hover {background-color: #e0e2e5;}

table.pagination {background-color:white;border-collapse:collapse;}
table.pagination th {padding:3px;background-color:#444;font-size:12px;text-align: left;color:#fff;}
table.pagination td {padding:3px;font-size:12px;width:5px;}

h1 {font-size:14px;font-weight:bold;margin-bottom:3px;}
hr {height:1px;border-width:0;color:#999;background-color:#aaa;}

.content_table {
	border-width:0px; border-spacing:2px; border-style:ridge; border-color:#d6d6d6; border-collapse:collapse;
	font-size:11px;
}
.content_table tr {
	background: #CEE5C5;
}
.content_table tr:nth-child(even) {
	background: #CEE5C5;
}
.content_table tr:nth-child(odd) {
	background: #f0f0f0;
}
.content_table th {
	background-color:gray;padding:4px;
	font-weight:bold;color:white;
	text-decoration:none;
	text-align: left;
}
.content_table td {
	border-width:0px;padding:4px;
	border-style:ridge;
	border-color:#d6d6d6;
	text-align: left;
}

.led-red { margin:6px auto;width:12px;height:12px;background-color:#940;border-radius: 50%;}
.led-yellow { margin:6px auto;width:12px;height:12px;background-color:#A90;border-radius: 50%;}
.led-green { margin:6px auto;width:12px;height:12px;background-color:#690;border-radius: 50%;}
.led-blue { margin:6px auto;width:12px;height:12px;background-color:#4AB;border-radius: 50%;}
.led-red2 { color:#940;font-size:13px;}
.led-green2 { color:#690;font-size:13px;}
.led-gray2 { color:gray;font-size:13px;}

#lte-signal-strength {
  height: 24px;
  list-style: none;
  overflow: hidden;
  display:inline-block;
	padding:0;
}
#lte-signal-strength li {
  display: inline-block;
  width: 4px;
  float: left;
  height: 100%;
  margin-right: 1px;
}
#lte-signal-strength li.bar5 { padding-top: 2px; }
#lte-signal-strength li.bar4 { padding-top: 8px; }
#lte-signal-strength li.bar3 { padding-top: 14px; }
#lte-signal-strength li.bar2 { padding-top: 19px; }
#lte-signal-strength li.bar1 { padding-top: 23px; }
#lte-signal-strength li div { height: 100%; background: #99cdfe; }

.top_title_icons { width:18px;height:18px;-webkit-transform: scaleX(-1);transform: scaleX(-1); }

</style>
</head>
<body style="background-color:#ffffff; margin:0px; padding: 0px;font-family: 'Gilroy', arial;letter-spacing:0.2px;color:#111;font-size:14px;overflow:hidden;">
<div style="width:100%;height:68px;" summary="toptitle"><?php include('c_toptitle.php'); ?></div>
<div style="width:180px;background-color:#444;float:left;position:absolute;min-height: 100%;" summary="sidebar"><?php include('c_sidebar.php'); ?></div>
<div style="margin-left:190px;margin-top:10px;float:right;min-width:600px;width: calc(100% - 180px);position:absolute;height:calc(100% - 66px);overflow:scroll;" summary="main content"><b><?php print $page_title; ?></b><?php include($contentfile); ?></div>
</body>
</html>
