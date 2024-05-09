<?php


function sm_get_device_port_branching_by_serialnumber($port)
{	$model = $GLOBALS['G_device_model'];
	$model_variant = $GLOBALS['G_device_model_variant'];
   if($port == "WAN")
   {	//wan1 port is there for all variants
	  	return true; 
   }
	elseif($port == "WAN2")
	{	if(($model=='vm' && $model_variant=="l2") || ($model=='vm' && $model_variant=="l3") ||
         ($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") ||
         ($model=='spider2' && $model_variant=="l2") || ($model=='spider2' && $model_variant=="l3") ||
         ($model=='beetle' && $model_variant=="l2") || ($model=='beetle' && $model_variant=="l3") ||
         ($model=='bumblebee' && $model_variant=="l2") || ($model=='bumblebee' && $model_variant=="l3"))
         return true; 	
	}
	elseif($port == "WAN3")
   {	if(($model=='spider2' && $model_variant=="l3"))
   	return true; 
   }	
   elseif($port == "LTE1")
   {	if(($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") || ($model=='spider' && $model_variant=="l2w1l2") ||
	      ($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3") ||
	      ($model=='beetle' && $model_variant=="l2") ||($model=='beetle' && $model_variant=="l3") ||
	      ($model=='bumblebee' && $model_variant=="l2") || ($model=='bumblebee' && $model_variant=="l3"))
	      return true;      
   }
   elseif($port == "LTE2")
   {	if(($model=='spider' && $model_variant=="l2") || ($model=='spider' && $model_variant=="l3") || ($model=='spider' && $model_variant=="l2w1l2") ||
         ($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3"))
         return true; 
   }
   elseif($port == "LTE3")
   {	if(($model=='spider2' && $model_variant=="l2") ||($model=='spider2' && $model_variant=="l3"))
   	return true; 
   }
   elseif($port == "LAN")
   {	//lan port is there for all variants
   	return true;
   }
   elseif($port == "WIRELESS")
   {	//wifi port is there for all variants
   	return true;
   }
   elseif($port == "SD-WAN")
   {	//sdwan port is there for all variants
   	return true;
   }
	
	return false;
} /* sm_get_device_port_branching_by_serialnumber */

?>