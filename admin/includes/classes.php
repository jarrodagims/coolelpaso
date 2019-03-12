<?php
function autoload_class_multiple_directory($class_name){

	//base path
	$root = $GLOBALS['path'].'admin/classes';
	
    // class directories in array.
    $array_paths = array(
		'/database/',
		'/display/',
        '/external/',
		'/process/',
    );

    // require directories.
    foreach($array_paths as $array_path){
        if(file_exists($root.$array_path.$class_name.'.php')){
            require_once $root.$array_path.$class_name.'.php';
        } 
    }
}

// new method replaces autoload: php 5.1.2 and up
spl_autoload_register('autoload_class_multiple_directory');
?>