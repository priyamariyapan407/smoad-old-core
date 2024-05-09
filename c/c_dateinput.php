<?php

$mons = array(1 => "Jan", 2 => "Feb", 3 => "Mar", 4 => "Apr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Aug", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dec");
print "<table><tr>"; 
print "<td><select name=\"day\" class=\"select_style\">";
for($i=1;$i<=31;$i++) {
  if(date('d')==$i) $selected="selected"; else $selected="";
  print "<option value=\"$i\" $selected> $i </option>";
}
print "</select></td>";
print "<td><select name=\"month\" class=\"select_style\">";
$monthcurrent=date('m');
for($i=1;$i<=12;$i++) {
  if($monthcurrent==$i) $selected="selected"; else $selected="";
  $mname = $mons[$i];
  print "<option value=\"$i\" $selected>$i $mname</option>";
}
print "</select></td>";
print "<td><select name=\"year\" class=\"select_style\">";

$yearcurrent=date("Y");
//$yearcurrent="2017";

for($i=$yearcurrent;$i>=2002;$i--) {
  if($yearcurrent==$i) $selected="selected"; else $selected="";
  print "<option value=\"$i\" $selected>$i</option>";
}
print "</select></td>";
print "</tr></table>";
?>