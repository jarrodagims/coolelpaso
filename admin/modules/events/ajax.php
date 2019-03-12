<?php 
# Debugging
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);

include_once("../../includes/library.php");
Security::xssProtect();

extract($_GET);


if ($action == 'get_month') {
	$calendar = new Calendar();
	
	if($direction == 'next'){
		$month = (intval($_SESSION['month']) == 12)? 1: intval($_SESSION['month'])+1;
		$year = (intval($month) == 1)? intval($_SESSION['year'])+1: intval($_SESSION['year']);
	}
	else if($direction == 'previous'){
		$month = (intval($_SESSION['month']) == 1)? 12: intval($_SESSION['month'])-1;
		$year = (intval($month) == 12)? intval($_SESSION['year'])-1: intval($_SESSION['year']);
	}
	
	
	$calendar->setYear($year);
	$calendar->setMonth($month);
	$calendar->setToday(getdate());
	
	if($_SESSION['view'] == 'calendar'){
		echo $calendar->buildCalendar();	
	}
	else if($_SESSION['view'] == 'list'){
		echo $calendar->buildCalendarList();
	}
} 

else if($action == 'get_events'){
	$calendar = new Calendar();
	
	ob_start(); ?>
	<div id="day_events" class="row">
		<div class="twelve columns"><?php echo $calendar->buildDayList($date);?></div>
	</div>
    <?php 
	echo ob_get_clean();
}

else if($action == 'get_calendar_list'){
	$month = intval(intval($_SESSION['month']));
	$year = intval(intval($_SESSION['year']));
	$calendar = new Calendar();
	$calendar->setYear($year);
	$calendar->setMonth($month);
	$calendar->setToday(getdate());
	echo $calendar->buildCalendarList();
}

if ($action == 'get_calendar') {
	$month = intval(intval($_SESSION['month']));
	$year = intval(intval($_SESSION['year']));
	$calendar = new Calendar();
	$calendar->setYear($year);
	$calendar->setMonth($month);
	$calendar->setToday(getdate());
	echo $calendar->buildCalendar();
}
?>