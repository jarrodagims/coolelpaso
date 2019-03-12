<?php
class Calendar
{
	protected $events_ob = 'Event';						//calendarAction() and listingAction() needed in class
	protected $events_category_ob = 'EventCategory';
	protected $events_start_date_field = 'start_date';	//field used to compare start dates
	protected $events_end_date_field = 'end_date';		//field used to compare end dates
	protected $ajax_file = '/admin/modules/events/ajax.php';

	protected $_year;
	protected $_month;
	protected $_today;
	
	protected $_categories = array();

	// Accessor Methods
	public function __construct(){
		$this->setYear(intval(date('Y')))
			 ->setMonth(intval(date('m')))
			 ->setToday(getdate());
	}
	
	public function setYear($value){ $this->_year = (int) $value; return $this; }
	public function getYear(){ return $this->_year; }
	public function setMonth($value){ $this->_month = (int) $value; return $this; }
	public function getMonth(){ return $this->_month; }
	public function setToday($value){ $this->_today = $value; return $this; }
	public function getToday(){ return $this->_today; }
	
	public function setCategories(){
		$events = new $this->events_ob();
		$events = $events->fetchAll("GROUP BY `category`", "", "", "`category`");				
		$category_ids = '';
		for($i = 0; $i < count($events); $i++){
			$category_ids .= (($i > 0)? ',' : '') . $events[$i]->getCategory();
		}
		$categories = new $this->events_category_ob(); 
		$this->_categories = $categories->fetchAll("WHERE `id` IN(".$category_ids.")");
	}
	
	public function getCategories(){
		if(!sizeof($this->_categories)){
			$this->setCategories();
		}
		return $this->_categories;
	}
	
	//returns the first day of the week in the current month
	public function getFirstDay(){$day = getdate(mktime(0, 0, 0, $this->getMonth(), 1, $this->getYear())); return $day['wday'];}
	
	//returns the number of days in a month
	public function getNumberOfDays($month = ""){ $month = ($month == "")? $this->getMonth(): $month; return cal_days_in_month(CAL_GREGORIAN,$month,$this->getYear());}
	
	//returns the numeric value of the next month
	public function getNextMonth(){
		if($this->getMonth() == 12){return 1;}
		else{return $this->getMonth()+1;}
	}
	
	//returns the numeric value of the previous month
	public function getPrevMonth(){
		if($this->getMonth() == 1){return 12;}
		else{return $this->getMonth()-1;}
	}
	
	//returns the name of the next month
	public function getNextMonthName(){ return $this->getMonthName($this->getNextMonth());}
	
	//returns the name of the previous month
	public function getPrevMonthName(){ return $this->getMonthName($this->getPrevMonth());}
	
	//returns the name of a given month based on numeric value
	public function getMonthName($month = ""){
		//if a month is provided - otherwise, use current
		$month = ($month == "")? $this->getMonth(): $month; 
		$month = intval($month);
		$month_name = date('F', mktime(0, 0, 0, $month, 1, $this->getYear()));
		return $month_name;
	}
	
	// determines if a given date is past today's date
	// returns 'past' if the given date is passed today's date
	// returns 'today' if the given date is today's date
	// returns empty string if the given date after today's date
	// $date must be a string in the following format - yyyy-mm-dd
	public function isPastDate($date){
		$date = explode('-', $date);
		$year = intval($date[0]);
		$month = intval($date[1]);
		$day = intval($date[2]);
		
		$today = $this->getToday();
		$currentYear = intval($today['year']);
		$currentMonth = intval($today['mon']);
		$currentDay = intval($today['mday']);
		
		if($year == $currentYear){
			if($month == $currentMonth){
				if($day < $currentDay){
					//past day same month and year
					return 'past';	
				}
				else if($day == $currentDay){
					return 'today';
				}
			}
			else if($month < $currentMonth){
				//past month same year
				return 'past';	
			}
		}
		else if($year < $currentYear){
			return 'past';
		}
		return '';
	}
	
	// returns date in the following format - MMM DD, YYYY
	// $date must be a string in the following format - yyyy-mm-dd
	public function getDate($date){
		$date = explode('-', $date);
		$year = intval($date[0]);
		$month = $this->getMonthName(intval($date[1]));
		$day = intval($date[2]);
			
		return $month . ' ' . $day . ', ' . $year;
	}
	
	
	// returns the events occurring on a given date
	// $date must be a string in the following format - yyyy-mm-dd
	public function getDayEvents($date){
		$object = $this->events_ob;
		$event = new $object();
		$events = array();
		
		# Get Non-Reoccurring events
		$query = "WHERE `start_date` = '".$date."' AND `repeating` = '0' AND `active` = '1'";
		$events = array_merge($events, $event->fetchQuery($query));
		
		# Get Reoccurring events
		# Get Events that Reoccur on a daily basis
		$query = "WHERE `repeating` = '1' AND `start_date` <= '".$date."' AND (`repeat_end` >= '".$date."' OR `repeat_end` = '0000-00-00') AND `repeat_type` = 'daily' AND `repeat_every` <> 0 AND MOD(TIMESTAMPDIFF(DAY, `start_date`, '".$date."'), `repeat_every`) = 0 AND `active` = '1'";
		$events = array_merge($events, $event->fetchQuery($query));
		
		# Get Events that Reoccur on a weekly basis
		$query = "WHERE `repeating` = '1' AND `start_date` <= '".$date."' AND (`repeat_end` >= '".$date."' OR `repeat_end` = '0000-00-00') AND `repeat_type` = 'weekly' AND `repeat_wednesday` = '1' AND `repeat_every` <> 0 AND MOD(TIMESTAMPDIFF(WEEK, `start_date`, '".$date."'), `repeat_every`) = 0 AND `active` = '1'";
		$events = array_merge($events, $event->fetchQuery($query));
		
		# Get Events that Reoccur on a monthly basis
		$query = "WHERE `repeating` = '1' AND `start_date` <= '".$date."' AND (`repeat_end` >= '".$date."' OR `repeat_end` = '0000-00-00') AND `repeat_type` = 'monthly' AND ((`repeat_by` = 'day_of_month' AND DAY(`start_date`) = '16') OR (`repeat_by` = 'day_of_week' AND DAYOFWEEK(`start_date`) = '4' AND MOD((TIMESTAMPDIFF(WEEK, '2017-08-01', '".$date."')+1), `repeat_every`) = 0)) AND `repeat_every` <> 0 AND `active` = '1'";
		$events = array_merge($events, $event->fetchQuery($query));
		
		# Get Events that Reoccur on a yearly basis
		$query = "WHERE `repeating` = '1' AND `start_date` <= '".$date."' AND (`repeat_end` >= '".$date."' OR `repeat_end` = '0000-00-00') AND `repeat_type` = 'yearly' AND DAY(`start_date`) = '16' AND MONTH(`start_date`) = '8' AND `repeat_every` <> 0 AND MOD(TIMESTAMPDIFF(YEAR, `start_date`, '".$date."'), `repeat_every`) = 0 AND `active` = '1'";
		$events = array_merge($events, $event->fetchQuery($query));
		
		return $events;
	}
	
	
	public function init($view){
		ob_start();?>
      	<div id="events_calendar_container">
        	<div class="loading"><div><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div></div>
            <?php echo $this->$view(); ?>
        </div>
        <?php
        return ob_get_clean();	
	}
	
	// returns a responsive calendar head with navigation buttons, view links, and legend
	public function buildCalendarHead(){
		ob_start();?>
        	<tr id="view_as">
                <td colspan="7">
                <?php 
				if($_SESSION['view'] == 'list'){
					echo '<a class="view_as_calendar"><i class="fa fa-calendar"></i>View as Calendar</a>';	
				}
				else {
					echo '<a class="view_as_list"><i class="fa fa-list"></i>View as List</a>';	
				}
                ?>
                </td>
            </tr>
            <tr id="calendar_title">
                <th colspan="7">
                    <table width="100%">
                        <tr>
                            <th class="left">
                                <a class="prev_month_link large_button btn btn-primary"><i class="fa fa-chevron-left"></i><?php echo $this->getPrevMonthName(); ?></a>
                                <a class="prev_month_link small_button btn btn-primary"><i class="fa fa-chevron-left"></i><br /><?php echo $this->getPrevMonthName(); ?></a>
                            </th>
                            <th><h2><?php echo $this->getMonthName() . ' ' . $this->getYear();?></h2></th>
                            <th class="right">
                                <a class="next_month_link large_button btn btn-primary"><?php echo $this->getNextMonthName(); ?><i class="fa fa-chevron-right"></i></a>
                                <a class="next_month_link small_button btn btn-primary"><i class="fa fa-chevron-right"></i><br/><?php echo $this->getNextMonthName(); ?></a>
                            </th>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr id="calendar_legend">
                <td colspan="7">
                   	<?php 
						$categories = $this->getCategories();						
						foreach($categories as $c){ ?>
							<span class="category-indicator">
								<i class="fa fa-circle" style="color: <?php echo $c->getColor(); ?>;"></i>
								<span class="category-name"><?php echo $c->getName(); ?></span>
							</span>
						<?php
						}
					?>
                </td>
            </tr>
        <?php
		return ob_get_clean();
	}


	// returns a responsive calendar grid with events
	public function buildCalendar(){

		$first_day = $this->getFirstDay();						//Determine what day of the week the month starts on
		$current_number_of_days = $this->getNumberOfDays();		//Determine the number of days in the month

		$number_of_previous_days = $first_day;										//Determine the number of days needed to complete the first week
		$prev_month_number_of_days = $this->getNumberOfDays($this->getMonth()-1); 	//Determine the number of days in the previous month
		
		$_SESSION['month'] = $this->getMonth();
		$_SESSION['year'] = $this->getYear();
		$_SESSION['view'] = 'calendar';

		ob_start();?>
        <table border="0" id="events_calendar">
            <?php echo $this->buildCalendarHead() ?>
            <tr id="events_calendar_weekdays">
                <th>
					<div class="full_week_name">Sunday</div>
					<div class="short_week_name">Sun</div>
					<div class="abrev_week_name">S</div>
                </th>
                <th>
					<div class="full_week_name">Monday</div>
					<div class="short_week_name">Mon</div>
					<div class="abrev_week_name">M</div>
                </th>
                <th>
					<div class="full_week_name">Tuesday</div>
					<div class="short_week_name">Tue</div>
					<div class="abrev_week_name">T</div>
                </th>
                <th>
					<div class="full_week_name">Wednesday</div>
					<div class="short_week_name">Wed</div>
					<div class="abrev_week_name">W</div>
                </th>
                <th>
					<div class="full_week_name">Thursday</div>
					<div class="short_week_name">Thu</div>
					<div class="abrev_week_name">T</div>
                </th>
                <th>
					<div class="full_week_name">Friday</div>
					<div class="short_week_name">Fri</div>
					<div class="abrev_week_name">F</div>
                </th>
                <th>
					<div class="full_week_name">Saturday</div>
					<div class="short_week_name">Sat</div>
					<div class="abrev_week_name">S</div>
                </th>
            </tr>
            <?php
            $dayCounter = 0;											//Keeps count of the days in a week

            /*** PREVIOUS MONTH CELLS ***/
            for($day = $number_of_previous_days; $day >= 1; $day--){	//For each day in the previous month needed for a full week
                $dayNumber = $prev_month_number_of_days - ($day-1);		
                
                //Get full date (YYYY-MM-DD)
                $year = ($this->getPrevMonth() == 12)? ($this->getYear()-1): $this->getYear();
                $full_date = $year . '-' . $this->getPrevMonth() . '-' . $dayNumber;
                
                $dayCounter ++;
                if($dayCounter == 1){					//Start new row
                    echo '<tr>';
                }
                
                echo $this->buildCalendarCell($full_date, $dayNumber, 'prev_month');	//Create Calendar Cell

                if($dayCounter == 7){					//End new row
                    echo '</tr>';
                    $dayCounter = 0;
                }
            }

            /*** CURRENT MONTH CELLS ***/
            for($day = 1; $day <= $current_number_of_days; $day++){		//For each day in the current month
                $full_date = $this->getYear() . '-' . $this->getMonth() . '-' . $day;
                $dayCounter ++;
                if($dayCounter == 1){					//Start new row
                    echo '<tr>';
                }

                echo $this->buildCalendarCell($full_date, $day, 'current_month');	//Create Calendar Cell

                if($dayCounter == 7){					//End new row
                    echo '</tr>';
                    $dayCounter = 0;
                }
            }

            /*** NEXT MONTH CELLS ***/
            for($day = 1; $day <= $dayCounter; $day++){	//For each day in the current month
                //Get full date (YYYY-MM-DD)
                $year = ($this->getNextMonth() == 1)? ($this->getYear()+1): $this->getYear();
                $full_date = $year . '-' . $this->getNextMonth() . '-' . $day;
                
                $dayCounter ++;
                if($dayCounter == 1){					//Start new row
                    echo '<tr>';
                }

                echo $this->buildCalendarCell($full_date, $day, 'next_month');	//Create Calendar Cell

                if($dayCounter == 7){					//End new row
                    echo '</tr>';
                    $dayCounter = 0;
                }
            }
            ?>
        </table>
        <style>
			<?php 
			foreach($this->getCategories() as $c){?>
			#events_calendar .event.category_<?php echo $c->getId(); ?>:before{color: <?php echo $c->getColor(); ?> !important;}
			<?php } ?>
		</style>
        <?php
		return ob_get_clean();
	}

	// returns callendar cell of a given date
	// $full_date must be a string in the following format - yyyy-mm-dd
	// $day is the numeric date of the month
	// $class is added to the calendar cell if specified
	public function buildCalendarCell($full_date, $day, $class = ""){
		
		// get events for specified dates
		$events = $this->getDayEvents($full_date);
		
		// determine if specified date has passed
		$pastDate = $this->isPastDate($full_date);
		
		ob_start();?>
        	<td class="calendar_cell <?php echo $class . ' ' . $pastDate?>" id="<?php echo $full_date; ?>">
            	<div class="day_of_month"><?php echo $day;?></div>
                <div class="events">
                <?php
		
					foreach($events as $key => $event){
						echo $event->toHtml('calendar');
					}
				?>
                </div>
                <div class="events_compact">
                <?php 
					foreach($events as $key => $event){?>
						<i class="fa fa-circle"></i>	
                    <?php
					}
				?>
                </div>
            </td>
        <?php
		return ob_get_clean();
	}
	
	// returns list of events for the current month and year
	public function buildCalendarList(){
		
		$first_day = $this->getFirstDay();						//Determine what day of the week the month starts on
		$current_number_of_days = $this->getNumberOfDays();		//Determine the number of days in the month

		$number_of_previous_days = $first_day;										//Determine the number of days needed to complete the first week
		$prev_month_number_of_days = $this->getNumberOfDays($this->getMonth()-1); 	//Determine the number of days in the previous month
		
		$_SESSION['month'] = $this->getMonth();
		$_SESSION['year'] = $this->getYear();
		$_SESSION['view'] = 'list';
		
		ob_start();?>
        <table border="0" id="events_calendar" class="calendar_list">
           <?php echo $this->buildCalendarHead() ?>
            <tr>
                <td>
                    <?php 
                        /*** CURRENT MONTH CELLS ***/
                        $first_day = $this->getYear() . '-' . $this->getMonth() . '-01';
                        $last_day = $this->getYear() . '-' . $this->getMonth() . '-' . $current_number_of_days;
                        
                        $object = $this->events_ob;
                        $month_events = new $object();
                        $month_events = $month_events->fetchAll("WHERE DATE(" . $this->events_end_date_field . ") >= '" . $first_day . "' AND DATE(" . $this->events_end_date_field . ") <= '" . $last_day . "'", "ORDER BY `" . $this->events_end_date_field . "` DESC");
                        
                        if(count($month_events) > 0){
                            for($day = 1; $day <= $current_number_of_days; $day++){		//For each day in the current month
                                $full_date = $this->getYear() . '-' . $this->getMonth() . '-' . $day;
                            
                                $events = $this->buildDayList($full_date);	//get events for each day
                                if(strlen(trim($events))){
                                ?>
                                    <div class="day">
                                        <?php echo $events; ?>
                                    </div>
                                <?php
                                }
                            }
                        }
                        else{?>
                       		<h5><?php echo 'No events in ' . $this->getMonthName() . ' ' . $this->getYear();?></h5>
                        <?php
                        }
                    ?>   
                </td>
            </tr>
        </table>
        <?php
        return ob_get_clean();
	}

	// returns a list of events for a given date
	// $date must be a string in the following format - yyyy-mm-dd
	public function buildDayList($date){
		$events = $this->getDayEvents($date);
		$count = 0;
		
		ob_start();
		if(count($events) > 0){
			foreach($events as $key => $event){			
				$recurringDays = $event->getRecurringDays();
						
				$dayOfWeek = date_create($date);
				$dayOfWeek = strtolower(date_format($dayOfWeek, "l"));
				//echo $dayOfWeek;

				if(count($recurringDays)){
					if(in_array($dayOfWeek, $recurringDays)){
						echo '<h4>Events for ' . $this->getDate($date) . '</h4>';
						echo $event->toHtml('listing');
						echo '<hr />';
					}
				}
				else{
					echo '<h4>Events for ' . $this->getDate($date) . '</h4>';
					echo $event->toHtml('listing');
					echo '<hr />';
				}
					
				$count++;
			}
		}
		return ob_get_clean();
	}
	
	// returns the javascript needed to: 
	//		- navigate through months
	//		- show events on click of calendar cell (mobile only)
	//		- switch views (calendar grid and calendar list)
	public function buildCalendarScripts(){
		ob_start();?>
        	<script>
				
				//on calendar cell click 
				$(document).on('click', '.calendar_cell', function(e){
					if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
						var fullDate = $(this).attr('id');
						
						$.ajax({
						  url:'<?php echo $this->ajax_file; ?>',
						  data:{action:'get_events', date: fullDate},
						  async:false,
						  success:function(data, textStatus, jqXHR) {
							  if (data) {
								  $('#day_events').replaceWith(data);
							  }
						  }
						});	
					}
				});
				
				//on view as calendar click
				$(document).on('click', '.view_as_calendar', function(e){
					$.ajax({
					  url:'<?php echo $this->ajax_file; ?>',
					  data:{action:'get_calendar'},
					  async:false,
					  success:function(data, textStatus, jqXHR) {
						  if (data) {
							  $('#events_calendar').replaceWith(data);
						  }
					  }
					});	
				});
				
				//on view as list click
				$(document).on('click', '.view_as_list', function(e){
					$.ajax({
					  url:'<?php echo $this->ajax_file; ?>',
					  data:{action:'get_calendar_list'},
					  async:false,
					  success:function(data, textStatus, jqXHR) {
						  if (data) {
							  $('#day_events').html('');
							  $('#events_calendar').replaceWith(data);
						  }
					  }
					});	
				});
				
				//on previous month link click
				$(document).on('click', '.prev_month_link', function(e) {
					e.preventDefault();
					
					getCalendar('previous');
				});
				
				//on next month link click
				$(document).on('click', '.next_month_link', function(e) {
					e.preventDefault();
					
					getCalendar('next');
				});
				
				function getCalendar(direction){
					$('.loading').show();
					$.ajax({
					  url:'<?php echo $this->ajax_file; ?>',
					  data:{action:'get_month',direction: direction},
					  async:false,
					  success:function(data, textStatus, jqXHR) {
						  if (data) {
							  $('.loading').delay( 800 ).hide();
							  $('#day_events').html('');
							  $('#events_calendar').replaceWith(data);
						  }
					  }
					});	
				}
            </script>
        <?php
		return ob_get_clean();
	}
}
?>
