<?php
class ActivityLog extends Model{
	// Module Configuration
	public $_moduleName  = 'Activity Log';
	public $_moduleDir   = 'activity_log';
	public $_moduleTable = 'activity_log';
	public $_moduleClassName = 'ActivityLog';
	public $_moduleDescription = 'This section allows the super admin to view user activity in this CMS.';
	public $_moduleIcon = 'fa-eye';
	
	// Static Variables
	protected static $_addLabel = '';
	protected static $_editLabel = '';

	// Inherited Variables
	protected $_dbTable = 'activity_log';
	//protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_user;
	protected $_ip;
	protected $_country = '';
	protected $_countryStatus = '';
	protected $_userAgent;
	protected $_queryExecuted;
	protected $_date;
	protected $_activity;
	
	// Instance Variables
	protected $_requiredFields = array(
									);
									
	protected $_saveFields = array(
									'user',
									'ip',
									'country',
									'country_status',
									'user_agent',
									'query_executed',
									'date',
									'activity');
	
	// Constructor
	public function __construct($id = 0)
	{
		parent::__construct($id);
	}
	
	// Static Methods
	public static function setAddLabel($v){self::$_addLabel = (string) $v;}
	public static function getAddLabel(){ return self::$_addLabel;}
	public static function setEditLabel($v){self::$_editLabel = (string) $v;}
	public static function getEditLabel(){ return self::$_editLabel;}

	// Accessor Methods
	public function setId($v){$this->_id = (int) $v; return $this;}
	public function getId(){ return $this->_id;}
	public function setUser($v){$this->_user = (string) $v; return $this;}
	public function getUser(){ return $this->_user;}
	public function setIp($v){$this->_ip = (string) $v; return $this;}
	public function getIp(){ return $this->_ip;}
	public function setCountry($v){$this->_country = (string) $v; return $this;}
	public function getCountry(){ return $this->_country;}
	public function setCountryStatus($v){$this->_countryStatus = (string) $v; return $this;}
	public function getCountryStatus(){ return $this->_countryStatus;}
	public function setUserAgent($v){$this->_userAgent = (string) $v; return $this;}
	public function getUserAgent(){ return $this->_userAgent;}
	public function setQueryExecuted($v){$this->_queryExecuted = (string) $v; return $this;}
	public function getQueryExecuted(){ return $this->_queryExecuted;}
	public function setDate($v){$this->_date = (string) $v; return $this;}
	public function getDate(){ return $this->_date;}
	public function setActivity($v){$this->_activity = (string) $v; return $this;}
	public function getActivity(){ return $this->_activity;}


	// Instance Methods	
	public function install(){
		# Register module
		$this->register('','1','1');
		
		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(11) NOT NULL AUTO_INCREMENT,
			 `user` varchar(255) NOT NULL,
			 `ip` varchar(255) NOT NULL,
			 `country` varchar(255) NOT NULL,
			 `country_status` varchar(255) NOT NULL,
			 `user_agent` text NOT NULL,
			 `query_executed` text NOT NULL,
			 `date` datetime NOT NULL,
			 `activity` varchar(255) NOT NULL,
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}
		
		return $this;
	}
	
// get location from ip
	public function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
				}
			}
		}
		return $output;
	}
	
	public function formatDateSubmitted(){
		$date = date_create($this->getDate());
		$date = date_format($date, "F j, Y g:i A");
		return $date;
	}

	// Action Methods
	public function moduleIndexAction(){
		# Get the list of records
		$moduleClasses = new $this->_moduleClassName();
		$moduleClasses = $moduleClasses->fetchAll("","ORDER BY `date` DESC");
		ob_start(); ?>
		
		<div class="index-wrapper">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>User</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(count($moduleClasses) <= 0){?>
					<tr>
						<td colspan="2"><div class='no_records'><i class='fa fa-times-circle'></i>No records available.</div></td>
					</tr>
					<?php }
					foreach($moduleClasses as $moduleClass){
						echo $moduleClass->toHtml('row');
					}
					?>
				</tbody>
			</table>
		</div>
		
		<?php
		return ob_get_clean();
	}
	
	public function rowAction(){
		ob_start(); ?>
		
		<tr id="<?php echo $this->_moduleClassName."_".$this->getId();?>">
			<td class="name">
				<?php echo $this->getUser(); ?><br>
				<small><em><?php echo $this->formatDateSubmitted(); ?></em></small>
			</td>
			<td>
				<span class="action_menu">
					<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="<?php echo $this->toHtml('menu'); ?>" data-original-title="" title=""><i class="fa fa-gear" aria-hidden="true"></i></a>
				</span>
			</td>
		</tr>
		
		<?php
		return ob_get_clean();
	}
	
	public function menuAction(){
		ob_start(); ?>
        <ul class="actions">
        	<li><a href="<?php echo $this->buildModalUrl('view'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-search"></i>View</a></li>
        </ul>
        <?php
		$html = ob_get_clean();
		$html = str_replace('"', "'", $html);
		return $html;	
	}
	
	public function adminViewAction(){
		ob_start();?>
		<div class="view-modal">
			<div class="row">
				<div class="col-sm-4">
					<strong>User</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getUser(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Date</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->formatDateSubmitted(); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-4">
					<strong>Activity</strong>
				</div>
				<div class="col-sm-8"><?php echo $this->getActivity(); ?></div>
			</div>
		</div>
		<?php 
		$content = ob_get_clean();
		$modal = new Module();
		return $modal->buildInnerModal("View Activity", $content, 0);
	}
}
?>