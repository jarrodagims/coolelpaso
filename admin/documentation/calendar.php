<?php
# Debugging
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);

include_once("../includes/library.php");

$GLOBALS['page_title'] = "Calendar";

ob_start();
?>

<?php 
$calendar = new Calendar(); 
$calendar->setYear(2017)
		 ->setMonth(07)
		 ->setToday(01);
echo $calendar->init('buildCalendar');
?>

<?php $content = ob_get_clean();

if($_SESSION['session_logged_in'] != true){
	ob_start();?>

	<div class="container">
		<div class="row">
			<div class="col-sm-12" id="content">
				<div id="toast-container">
					<div class="toast toast-prototype">
						<i class="fa"></i>
						<div class="toast-content">
							<div class="toast-title"></div>
							<div class="toast-message"></div>
						</div>
					</div>
				</div>
				<h1><?php echo $GLOBALS['page_title']; ?></h1>
			</div>
		</div>
		<?php echo $content ?>
	</div>

	<?php

	$content = ob_get_clean();
}

ob_start(); ?>
<link rel="stylesheet" href="/admin/stylesheets/calendar.css">
<?php
$content .= ob_get_clean();

$GLOBALS['JAVASCRIPT'] = $calendar->buildCalendarScripts();

include_once("../template.php");
?>
