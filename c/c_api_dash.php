<?php 


function widget($width, $title, $contents)
{	print "<div style=\"float:left;margin:10px;width:".$width."px; background-color:#fefefe;box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;border-radius:4px;text-align:center;\">";
	print "<div style=\"width:100%;height:20px; padding-top:5px;font-weight:bold;background-color:#444;color:#eee;border-radius:4px 4px 0px 0px;\">$title</div>";
	print $contents;
	print "</div>";
}

function widget2($width, $title, $contents, $color, $textcolor)
{	print "<div style=\"float:left;margin:6px;width:".$width."px; height:84px; background-color:".$color.";color:".$textcolor.";box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;border-radius:4px;text-align:center;padding:8px;\">";
	print $contents;
	print "</div>";
}

function widget2_contents($icon, $title, $details, $misc, $textcolor)
{	$contents = "<table><tr>";
	$contents .= "<td><div style=\"float:left;text-align:center;padding:6px;\">";
	$contents .= "<img src=\"$icon\" />";
	$contents .= "</div></td>";
	$contents .= "<td><div style=\"float:left;color:".$textcolor.";text-align:center;padding:6px;font-size:13px;line-height: 1.4;\">";
	$contents .= "<b>$title</b><br><span style=\"font-size:19px;font-weight:bold;\">$details</span>";
	$contents .= "<br><span style=\"width:100%;font-size:12px;font-style:italic;color:".$textcolor.";\">$misc</span>";
	$contents .= "</div></td>";
	$contents .= "</tr>";
	$contents .= "</table>";
	return $contents;
}

?>