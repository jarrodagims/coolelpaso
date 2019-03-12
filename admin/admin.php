<?php
$dev = 0;

# Debugging 
if($dev){
	ini_set('display_errors','1');
	ini_set("error_reporting", E_ALL);
}

include_once("includes/library.php");

if(!$_SESSION['session_logged_in']){
	failure("You are not authorized to view the requested page.");
	header("Location: /admin");
	exit;	
}

Security::validateHttpRequest('none');

$modules = new Module();
$modules = $modules->fetchAll("WHERE `enabled`='1' AND `super` = '0'", "ORDER BY `sort_order`,`name`,`id`");

//page title 
$GLOBALS['page_title'] = $GLOBALS['template_title'] . ' Overview'; 

ob_start();
?>
   
    <?php 
	$i = 0;
    foreach($modules as $module){
		
		if($_SESSION['session_access_levels'] == 'All' || in_array($module->getId(),unserialize($_SESSION['session_access_levels']))){
			
		?>
    	
            <div class="col-md-4 col-sm-6 overview-container">
                <a class="overview-item" href="/admin/modules/<?php echo $module->getDir(); ?>/index.php">
                    <div class="overview-item-content">
                    	<i class="fa <?php echo $module->getIcon(); ?>"></i>
                        <div class="overview-name"><?php echo $module->getName(); ?></div>
                        <div class="overview-description"><?php echo $module->getDescription(); ?></div>
                    </div>
                </a>
            </div>
    
    <?php 
		}
    }?>

<?php
$content .= ob_get_clean();

include_once("template.php");
?>