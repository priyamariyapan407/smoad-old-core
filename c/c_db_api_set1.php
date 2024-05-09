<?php 

function db_api_get_row_count($db, $table, $condition)
{	$row_count=0;
	if($condition!=NULL) { $condition = " where $condition"; }
	if(isset($table))
	{	$query = "select count(*) row_count from $table $condition";
		if($res = $db->query($query))
		{	while ($row = $res->fetch_assoc()) 
			{	$row_count = $row['row_count']; }
		}
	}
	
	return $row_count;
}

function db_api_get_value($db, $field, $table, $id)
{	$value=NULL;
	if(isset($field) && isset($table) && isset($id))
	{	$query = "select $field field from $table where id=$id";
		if($res = $db->query($query))
		{	while ($row = $res->fetch_assoc()) 
			{	$value = $row['field']; }
		}
	}
	
	return $value;
}

function db_api_set_value($db, $value, $field, $table, $id, $field_type)
{	//$debug_mode = true;
	$debug_mode = false;
	//$debug_print = true;
	$debug_print = false;
	$value_before=db_api_get_value($db, $field, $table, $id);
	if(isset($value) && isset($field) && isset($table) && isset($id) && isset($field_type))
	{  if($value!=$value_before)
		{  if($field_type=="char") { $value = "\"$value\""; } 
			else if($field_type=="num") { $value = str_replace(',', '', $value); }
			else { print "Operation: wrong field_type: $field_type !<br>"; return false; }
			$query = "update $table set $field = $value  where id=$id";
			if(!$debug_mode) { $db->query($query); }
			if($debug_print) { print "Operation: $query<br>"; }
			return true;
		}
	}
	
	if($debug_print) 
	{	print "Operation: failed - ";
		if(!isset($value)) { print "(value not set) "; }
		if(!isset($field)) { print "(field not set) "; }
		if(!isset($table)) { print "(table not set) "; }
		if(!isset($id)) { print "(id not set) "; }
		if(!isset($field_type)) { print "(field_type not set) "; }
		print "<br>";
	}
	return false;
}

function db_api_del_row($db, $table, $id)
{	if(isset($id))
	{  $query = "delete from $table where id=$id";
			$db->query($query);
			print "Operation: $query<br>";
			return true;
	}
	return false;
}

//get a sum amount for a matching criteria
function db_api_get_total_consolidated_amount_for_id_type_month_year($db, $date_field, $table, $id, $id_field, $type, $amount_field, $month, $year, $format)
{	$_value=0;
	$_condition = "";

	if($id!="" && $id!="noid") 
	{  if($_condition!="") { $_condition .= " and "; }
		$_condition .= " $id_field=$id ";
	}

	if($type!="" && $type!="notype") 
	{  if($_condition!="") { $_condition .= " and "; }
		$_condition .= " type=\"$type\" ";
	}
	
	if($year>0) // if year=0, then ignore year !
	{  if($_condition!="") { $_condition .= " and "; }
		$_condition .= " extract(year from $date_field )=$year ";
	}
	
	if($month>0) // if month=0, then ignore month !
	{  if($_condition!="") { $_condition .= " and "; }
		$_condition .= " extract(month from $date_field )=$month ";
	}

	if($_condition!="") { $_condition="where $_condition"; }
	$query = "select sum($amount_field) _value from $table $_condition ";
	//print "$query <br>";
	if($res = $db->query($query))
	{ while ($row = $res->fetch_assoc()) { $_value=$row['_value']; } }
	
	//money format (nof -> no format)
	if($format=="mf") { $_value = money_format('%!i', $_value); } //Indian Money Format
	else if($format=="nf0") { $_value = number_format($_value, 0); } //Number Format rounded 0
	
	return $_value;
}

//replace 0, with -
// can be used so that it does not clutter and looks more easy to read in some cases
// used in: home_tasks_summary
function db_api_get_remove_zero(&$val) { if($val==0) { $val = "-"; } }

?>
