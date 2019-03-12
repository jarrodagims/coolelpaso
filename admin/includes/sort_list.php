<?php
$dev = 0;

# Debugging 
if($dev){
	print_r($_REQUEST);
	
	//ini_set('display_errors','1');
	//ini_set("error_reporting", E_ALL);
}

# Includes
include("library.php");

extract($_REQUEST);

$moduleClass = new $module();
	
# If moduleClass has parent id's - like HtmlPages
if(method_exists($moduleClass,'getParentId')){
	
	foreach($_REQUEST['menuItem'] as $key => $val){
		if(stristr($key,'>')){
			$key = explode('>',$key);
			$key = $key[1];
			$order[] = $key;
		}else{
			$order[] = $key;
		}	
	}
	
}

# If moduleClass is a Category based module
else{
	
	# Current Depth that is being sorted
	$depth = intval($lvl)-1;
	
	foreach($_REQUEST['menuItem'] as $key => $val){
		
		# Gather elements at current depth
		if(substr_count($key, '>') == $depth){
			
			# Separate key by ">"
			$key = explode('>', $key);

			# Get current id - last element in key
			$key = $key[count($key)-1];

			# Put key into array at proper index
			$order[] = $key;
		}
	}
}

if($dev){
	print_r($order);
}

foreach($_REQUEST['menuItem'] as $key => $val){
	
	# Get Key
	if(stristr($key,'>')){
		$key = explode('>',$key);
		$key = $key[1];
	}
	
	$moduleClass = new $module($key);
	if($moduleClass->getId()){
		$moduleClass->setSortOrder(array_search($key, array_keys(array_flip($order))));
		
		if(method_exists($moduleClass,'getCategory')){
			if($val >= 1 && $moduleClass->getCategory() != $val){
				$moduleClass->setCategory($val);
				$moduleClass->save();
			}
		}elseif(method_exists($moduleClass,'getParentId')){
			if($moduleClass->getParentId() != $val){
				$moduleClass->setParentId($val);
				$moduleClass->save();
			}
		}
		$moduleClass->save();
		// not a good idea to log sort orders each move creates a record for every entry in that module
		//$moduleClass->save(1);
		//success('Sort order for '.$moduleClass->getId().' changed to '.$moduleClass->getSortOrder().' successfully.', '', 'refreshData', array($moduleClass->_moduleName,$moduleClass->getId(),$moduleClass->getId()));
		unset($moduleClass);
	}
}
?>