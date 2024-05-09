<?php 

function api_ui_pagination_get_total_items_total_pages($db, $table, $where_clause, $items_per_page, &$total_items, &$total_pages)
{	$total_pages=0; $total_items=0;
	$query = "select count(*) total_items from $table $where_clause "; 
	if($res = $db->query($query)) { while($row = $res->fetch_assoc()) { $total_items = $row['total_items']; } }
	$total_pages = ceil($total_items/$items_per_page);
}


function api_ui_pagination_get_pagination_table($db, &$page, $total_pages, $curr_page)
{
	//dont show pagination if the total pages are just 1 (i.e items are less than items per page)
	//or reset pagination, if this is triggered by search (i.e items are less than items per page, but pagination > items)
	if($total_pages<=1) { $page=1; return; }
	
	print "<table class=\"pagination\" style=\"font-size:10px;width:99%\"><tr>";
	for($i=1;$i<=$total_pages;$i++)
	{  if($page==$i) { $page_button_color="a_button_red"; } else { $page_button_color="a_button_black"; }
		$curr_page_with_pagination = $curr_page."&pagination=".$i;
		print "<td><a href=\"$curr_page_with_pagination\" style=\"text-decoration: none;color:white;\"><div class=\"$page_button_color\">$i</div></a></td>";
		if($i%30==0) { print "</tr><tr>"; } 
	}
	print "</tr></table>";
}

?>