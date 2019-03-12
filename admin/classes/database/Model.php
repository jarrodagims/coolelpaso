<?php
class Model{
	# Inherited Variables
	// Do not default any of these
	protected $_database;
	protected $_dbTable;
	protected $_categoryObject = NULL;
	protected $_isSortable = 0;
	protected $_action;
	protected $_params;
	protected $_idField = 'id';
	protected $_permalinkField;
	protected $_displayOptions;
	protected $_messages = array();
	protected $_refreshElements = array();
	protected $_messageScope;
	protected $_filePath = 'files';
	protected $_requiredFields;
	protected $_validateNumbers;
	protected $_validateEmails;
	protected $_featuredMode = false;


	public function setDbTable($dbTable){$this->_dbTable = (string) $dbTable; return $this;}
	public function getDbTable(){return $this->_dbTable;}
	public function setAction($action){$this->_action = $action; return $this;}
	public function getAction(){return $this->_action;}
	public function setIsSortable($isSortable){$this->_isSortable = $isSortable; return $this;}
	public function getIsSortable(){return $this->_isSortable;}
	public function setParams($params){$this->_params = $params; return $this;}
	public function getParams(){return $this->_params;}
	public function setFilePath($filePath){$this->_filePath = $filePath;}
	public function getFilePath(){return $this->_filePath;}
	public function setRequiredFields($requiredFields){if(is_array($requiredFields)){ $this->_requiredFields = $requiredFields; return $this;}}
	public function getRequiredFields(){ return $this->_requiredFields;}
	public function setValidateNumbers($validateNumbers){if(is_array($validateNumbers)){ $this->_validateNumbers = $validateNumbers; return $this;}}
	public function getValidateNumbers(){return $this->_validateNumbers;}
	public function setValidateEmails($validateEmails){if(is_array($validateEmails)){ $this->_validateEmails = $validateEmails; return $this;}}
	public function getValidateEmails(){return $this->_validateEmails;}
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}
	public function setFeaturedMode($featuredMode){$this->_featuredMode = $featuredMode; return $this;}
	public function getFeaturedMode(){return $this->_featuredMode;}
	public function setDatabase($value){$this->_database = $value;}
	public function getDatabase(){return $this->_database;}


	public function __construct($options = NULL, $pdo_info = ''){
		if($pdo_info == '') $pdo_info = $GLOBALS['pdo'];
		$database = new Database($pdo_info);
		$this->setDatabase($database);
		if(is_array($options)){
			$this->setOptions($options);
		}else{
			if(is_numeric($options)){
				$options = (int) $options;
			}
			if($this->isInstalled()){
				$this->fetch($options);
			}
		}
	}

	public static function factory($obj,$array){
		foreach($array as $key=>$value){
			$function = 'set'.ucfirst(Model::stringToMethodStatic($key));
			if(method_exists($obj,$function)){
				$obj->$function($value);
			}
		}
		return $obj;
	}

    public function __set($name, $value){
        $name = ucfirst($this->stringToMethod($name));
		$method = 'set' . $name;
        if(('mapper' == $name) || !method_exists($this, $method)){
            throw new Exception('Invalid Model property');
        }
        $this->$method($value);
    }

    public function __get($name){
        $name = ucfirst($this->stringToMethod($name));
		$method = 'get' . $name;
        if(('mapper' == $name) || !method_exists($this, $method)){
            throw new Exception('Invalid Model property');
        }
        return $this->$method();
    }

    public function setOptions(array $options){
        $methods = get_class_methods($this);
        foreach($options as $key => $value){
			$method = 'set' . ucfirst($this->stringToMethod($key));
			if(in_array($method, $methods)){
			  $this->$method($value);
            }else{
			}
        }
        return $this;
	}

	public function formatDate($date, $format = "Y-m-d")
	{
		$date = date_create($date);
		$date = date_format($date, $format);
		return $date;
	}

	public function formatDateTime($dateTime)
	{
		$date = date_create($dateTime);
		$date = date_format($date, "Y-m-d H:i:s");
		return $date;
	}

	public function stringToMethod($str){
		$str = preg_replace("/[^0-9a-zA-Z]/","_",$str);
		$str = implode("",array_map('ucfirst',explode("_",$str)));
		$str{0} = strtolower($str{0});
		return $str;
	}

	public static function stringToMethodStatic($str){
		$str = preg_replace("/[^0-9a-zA-Z]/","_",$str);
		$str = implode("",array_map('ucfirst',explode("_",$str)));
		$str{0} = strtolower($str{0});
		return $str;
	}

	public function isInstalled(){
		$sql = "SHOW TABLES LIKE '".$this->getDbTable()."'";
		$this->getDatabase()->query($sql);
		$this->getDatabase()->execute();
		$tables = $this->getDatabase()->resultset(PDO::FETCH_NUM);
		if(sizeof($tables)){
			return true;
		}
		return false;
	}

	public function verifyInstallation(){
		# Check if the module is installed
		if(!$this->isInstalled()){
			# Install Module
			$this->install();
			$_SESSION['PAGE_RELOAD_TOAST'] = success(process($this->_moduleName).' module installed successfully');
			header('Location: index.php');
			exit;
		}

	}
	
	public function setupInstallation(){
		# Check if the module is installed
		if(!$this->isInstalled()){
			# Install Module
			$this->install();
		}
	}

	public function verifyAccess(){
		# Skip Verify if Defined
		if($_SESSION['skip_module_verify']){
			return;
		}

		# Make sure user has access to the requested module
		if(strlen(trim($this->_moduleClassName))){
			$module = new Module();
			$module = $module->fetchAll("WHERE `dir`='".$this->_moduleDir."'");
			$module = $module[0];
			# Check if module exists
			if(!$module->getId()){
				$_SESSION['PAGE_RELOAD_TOAST'] = failure("This module does not exist.");
				header("Location: /admin/index.php");
				exit;
			}

			# Check if session expired
			if($_SESSION['session_access_levels']){
				# Check that User has access to module
				if($_SESSION['session_access_levels'] != 'All' && !in_array($module->getId(),unserialize($_SESSION['session_access_levels']))){
					$_SESSION['PAGE_RELOAD_TOAST'] = failure("You are not authorized to view the requested page.");
					header("Location: /admin/index.php");
					exit;
				}
			}else{
				header("Location: /admin/timeout.php");
				exit;
			}
		}
		return;
	}

	public function has(){
		$records = $this->fetchAll();
		if(sizeof($records)){
			return true;
		}else{
			return false;
		}
	}

	public function exists($id){
		if(!$this->fetch($id)){
			return false;
		}
		return true;
	}

	public function register($altClassName = "",$super = '0',$enabled = '0'){
		if($altClassName == ""){$altClassName = $this->_moduleClassName;}

		$module = new Module();
		$module->setName($this->_moduleName)
			   ->setDir($this->_moduleDir)
			   ->setModTable($this->getDbTable())
			   ->setClass($altClassName)
			   ->setDescription($this->_moduleDescription)
			   ->setIcon($this->_moduleIcon)
			   ->setSuper($super)
			   ->setEnabled($enabled)
			   ->save();
	}

	public function save($queryLogging = false){
		if($this->getId()){
			foreach($this->getSaveFields() as $field){
				$method = 'get'.ucfirst($this->stringToMethod($field));
				$sqlFields .= ', '."$field = :$field";
				$valuesToBind[$field] = $this->$method();
				if($queryLogging){
					$valuesToLog .= ', `'.$field.'` = \''.$this->$method().'\'';
				}
			}
			$database = $this->getDatabase();
			if($queryLogging){
				$_SESSION['last_query'] = 'UPDATE `'.$this->getDbTable().'` SET '.ltrim($valuesToLog, ', ').' WHERE `id` = \''.$this->getId().'\';';
			}
			$database->query('UPDATE '.$this->getDbTable().' SET '.ltrim($sqlFields, ', ').' WHERE id = :Id');
			foreach($valuesToBind as $key => $value){
				$database->bind(":".$key, $value);
			}
			$database->bind(':Id',$this->getId());
			$database->execute();
		}else{
			foreach($this->getSaveFields() as $field){
				$method = 'get'.ucfirst($this->stringToMethod($field));

				$sqlValues .= ', '.':'.$field;
				$valuesToBind[$field] = $this->$method();
				if($queryLogging){
					$fieldsToLog .= ', `'.$field.'`';
					$valuesToLog .= ', \''.$this->$method().'\'';
				}
			}
			$database = $this->getDatabase();
			if($queryLogging){
				$_SESSION['last_query'] = 'INSERT INTO `'.$this->getDbTable().'` ('.ltrim($fieldsToLog, ', ').') VALUES ('.ltrim($valuesToLog, ', ').');';
			}
			$database->query('INSERT INTO '.$this->getDbTable().' ('.str_replace(':','',ltrim($sqlValues, ', ')).') VALUES ('.ltrim($sqlValues, ', ').')');
			foreach($valuesToBind as $key => $value){
				$database->bind(":".$key, $value);
			}
			$database->execute();
			$this->setId($database->lastInsertId());

			if(method_exists($this, 'setSortOrder')){
				// Set sort order
				$this->setSortOrder($this->getId());
				$database->query("UPDATE ".$this->getDbTable()." SET sort_order= :Sort WHERE id = :Id");
				$database->bind(':Sort', $this->getSortOrder());
				$database->bind(':Id', $this->getId());
				$database->execute();
			}
		}
		return $this;
	}

	public function fetch($id){
		if(strlen(trim($id))){
			// Determine which field to search against
			if(strlen(trim($this->_permalinkField)) && !is_integer($id)){
				$filter = $this->_permalinkField;
			}else{
				// Note that $id may be either an integer or a string here
				$filter = $this->_idField;
			}
			// Get the record
			$database = $this->getDatabase();
			$database->query('SELECT * FROM '.$this->getDbTable().' WHERE '.$filter.' = :Id');
			$database->bind(':Id', $id);
			$result = $database->single();
			if(!$database->rowCount()){
				return false;
			}else{
				$this->setOptions($result);
			}
		}
		return $this;
	}

	public function fetchAll($where = "", $orderBy = "", $limit = "", $fields = "*",$pdoCompliant=false){
		$convertToPdo = "SELECT ".$fields." FROM `".$this->getDbTable()."` ".$where." ".$orderBy." ".$limit;
		$database = $this->getDatabase();
		$parseSql = new PHPSQLParser($convertToPdo, true);
		$valuesToBind = $parseSql->findValues($parseSql->parsed['WHERE']);
		$database->query($database->format(str_replace($valuesToBind, "?", $convertToPdo)));
		foreach($database->format($valuesToBind) as $key => $value){
			$database->bind($key+1, $value);
		}
		$result = $database->resultset();
		if(!$database->rowCount()){
			return array();
		}else{
			foreach($result as $row){
				$class = get_class($this);
				$obj = new $class($row);
				$obj->setDbTable($this->getDbTable());
				$array[] = $obj;
			}
			return $array;
		}
	}
	
	public function fetchQuery($where = "", $orderBy = "", $limit = "", $fields = "*"){
		$sql = "SELECT ".$fields." FROM `".$this->getDbTable()."` ".$where." ".$orderBy." ".$limit;
		$database = $this->getDatabase();
		$database->query($sql);
		$result = $database->resultset();
		if(!$database->rowCount()){
			return array();
		}else{
			foreach($result as $row){
				$class = get_class($this);
				$obj = new $class($row);
				$obj->setDbTable($this->getDbTable());
				$array[] = $obj;
			}
			return $array;
		}
	}

	public function fetchCount($where = "", $fields = "*"){
		$convertToPdo = "SELECT COUNT(".$fields.") FROM `".$this->getDbTable()."` ".$where;
		$database = $this->getDatabase();
		$parseSql = new PHPSQLParser($convertToPdo, true);
		$valuesToBind = $parseSql->findValues($parseSql->parsed['WHERE']);
		$database->query($database->format(str_replace($valuesToBind, "?", $convertToPdo)));
		foreach($database->format($valuesToBind) as $key => $value){
			$database->bind($key+1, $value);
		}
		$result = $database->resultset();
		if(!$result[0]['COUNT(*)']){
		}else{
			return $result[0]['COUNT(*)'];
		}
	}

	public function fetchTables(){
		$sql = "SHOW TABLES";
		$this->getDatabase()->query($sql);
		$tables = $this->getDatabase()->resultset(PDO::FETCH_NUM);
		return $tables;
	}

	public function delete(){
		$database = $this->getDatabase();
		$_SESSION['last_query'] = 'DELETE FROM `'.$this->getDbTable().'` WHERE `id` = \''.$this->getId().'\';';
		$database->query('DELETE FROM '.$this->getDbTable().' WHERE `id` = :Id');
		$database->bind(':Id', $this->getId());
		$database->execute();
		return;
	}

	public function create($query){
		$database = $this->getDatabase();
		$database->query($query);
		$database->execute();
		return;
	}

	public function query($query){
		$database = $this->getDatabase();
		$database->query($query);
		$results = $database->resultset();
		return $results;
	}

	public function toHTML($action = ''){
		if(strlen(trim($action))){
			$function = $this->stringToMethod($action).'Action';
		}else{
			$function = $this->getAction().'Action';
		}

		return $this->$function($this->getParams());
	}

	public function adminNewRecord($param){}

	public function buildModalUrl($action, $optionalParam = '')
	{
		if($action == 'edit'){
			return "/admin/includes/module_form.php?module=".get_class($this)."&action=".$action."&id=".$this->getId();
		}
		else if($action == 'add'){
			if($optionalParam != ''){
				return "/admin/includes/module_form.php?module=".get_class($this)."&action=".$action."&category=".$optionalParam;
			}
			else{
				return "/admin/includes/module_form.php?module=".get_class($this)."&action=".$action;
			}
		}
		else if($action == 'view'){
			return "/admin/includes/module_form.php?module=".get_class($this)."&action=".$action."&id=".$this->getId();
		}
		else if($action == 'confirm'){
			return "/admin/includes/module_form.php?module=".get_class($this)."&action=".$action."&function=".$optionalParam."&id=".$this->getId();
		}
	}

	public function buildToggle($fieldName, $onIcon = "fa-toggle-on", $offIcon = "fa-toggle-off", $href = "action.php")
	{
		if(strtolower($fieldName) == 'featured'){
			$onLabel = "Add to Featured";
			$offLabel = "Remove from Featured";
			$onIcon = "fa-star";
			$offIcon = "fa-star-o";
		}
		else if(strtolower($fieldName) == 'active'){
			$onLabel = "Activate";
			$offLabel = "Deactivate";
		}
		else if(strtolower($fieldName) == 'locked'){
			$onLabel = "Lock";
			$offLabel = "Unlock";
			$onIcon = "fa-lock";
			$offIcon = "fa-unlock";
		}
		else if(strtolower($fieldName) == 'archived'){
			$onLabel = "Archive";
			$onIcon = "fa-archive";
		}
		
		else if(strtolower($fieldName) == 'super_admin'){
			$onLabel = "Super Admin Only";
			$offLabel = "Visible to All";
			$onIcon = "fa-eye-slash";
			$offIcon = "fa-eye";
		}

		# Get getter function
		$getter = 'get'.ucFirst($this->stringToMethod($fieldName));
		ob_start();

		if($this->$getter() == 0){?>
			<a href="<?php echo $href; ?>?action=<?php echo $fieldName; ?>&id=<?php echo $this->getId() ?>" class="ajaxToggle"><i class="fa <?php echo $onIcon; ?>"></i><?php echo $onLabel ?></a>
		<?php
		}
		else{?>
			<a href="<?php echo $href; ?>?action=<?php echo $fieldName; ?>&id=<?php echo $this->getId() ?>" class="ajaxToggle"><i class="fa <?php echo $offIcon; ?>"></i><?php echo $offLabel ?></a>
		<?php
		}

		return ob_get_clean();
	}

	public function buildToggleButton($fieldName = 'active', $href = "action.php")
	{
		$onLabel = "Active";
		$offLabel = "Disabled";
		$class = "";

		if(strtolower($fieldName) == 'featured'){
			$onLabel = "Featured";
			$offLabel = "Not Featured";
			$class = "featured-toggle";
		}

		# Get getter function
		$getter = "get".strtolower(ucFirst($fieldName));

		# Check if object has 'locked' field
		$locked = 0;
		if(method_exists($this, 'getLocked')){
			$locked = $this->getLocked();
		}

		ob_start();

		?>
		<a href="<?php echo $href; ?>?action=<?php echo $fieldName; ?>&id=<?php echo $this->getId() ?>" class="slider<?php echo ($locked)? " locked" : "";?> <?php echo ($this->$getter())? 'slider-on' : 'slider-off' ?> pull-right <?php echo $class;?>">
			<div>
				<span><?php echo $offLabel ?></span>
				<span><?php echo $onLabel ?></span>
			</div>
		</a>
		<?php

		return ob_get_clean();
	}

	public function deleteConfirmAction(){

		if(is_a($this, "Category")){
			$action = "action_categories.php";
		}else{
			$action = "action.php";
		}

		$title = 'Confirm Deletion';
		ob_start();?>
			<p>Are you sure you want to delete?</p>
			<form id="form" action="<?php echo $action; ?>">
				<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
				<input type="hidden" name="action" value="delete" autofocus/>
			</form>
		<?php
		$body = ob_get_clean();
		$modal = new Module();
		return $modal->buildInnerConfirmModal($title, $body);
	}

	public function toArray(){
		foreach($this as $key=>$value){
			if(is_array($value)){
				$tempArray = array();
				foreach($value as $k=>$v){
					if(is_object($v)){
						$tempArray[$k] = $v->toArray();
					}else{
						$tempArray[$k] = $v;
					}
				}
				$array[$key] = $tempArray;
			}elseif(is_object($value)){
				$array[$key] = $value->toArray();
			}else{
				$array[$key] = $value;
			}
		}
		return $array;
	}

	//type = general,info,failure,success
	public function checkRequired()
	{
		// Check required fields
		foreach($this->getRequiredFields() as $field){
			if(!strlen(trim($this->__get($field)))){
				$includeGeneral = 1;
				$this->addMessage($field,array('type'=>'failure','text'=>'Required'));
			}
		}

		if($includeGeneral){
			$this->addMessage('general',array('type'=>'failure','text'=>'Please complete all required fields'));
		}

		return $this;
	}

	public function validateEmail(){
		foreach($this->getValidateEmails() as $email){
			foreach(preg_split("/[;, \n]+/", trim($this->__get($email))) as $line){
				if(strlen(trim($line)) > 1){
					if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', trim($line))){
					}else{
						$this->addMessage($field,array('type'=>'failure','text'=>'Invalid Email'));
						$failure = true;
					}
				}
			}
		}
		if($failure){
			return false;
		}
		return true;
	}

	public function validatePhone(){
		foreach($this->getValidateNumbers() as $number){
			if(strlen(trim($this->__get($number)))){
				if (!preg_match("/^(?:(?:\((?=\d{3}\)))?(\d{3})(?:(?<=\(\d{3})\))?[\s.\/-]?)?(\d{3})[\s\.\/-]?(\d{4})\s?(?:(?:(?:(?:e|x|ex|ext)?\.?\:?|extension\:?)\s?)(?=\d+)(\d+))?$/i", trim($this->__get($number)))) {

					$this->addMessage($field,array('type'=>'failure','text'=>'Invalid Phone Number'));
					$failure = true;
				}
			}
		}
		if($failure){
			return false;
		}
		return true;
	}

	public function validateUrl($fieldName = array('')){
		foreach($fieldName as $field){
			$getter = 'get'.ucFirst($this->stringToMethod($field));
			if($this->$getter()){
				$url = filter_var($this->$getter(), FILTER_SANITIZE_URL);
				if(!filter_var($url, FILTER_VALIDATE_URL) === false || substr($url, 0, 1) === '/'){
				}else{
					$this->addMessage($field,array('type'=>'failure','text'=>$url.' is not a valid URL'));
					$failure = true;
				}
			}
		}
		if($failure){
			return false;
		}
		return true;
	}

	/**
	 * function generatePermalink()
	 *
	 * This function generates a permalink based on the submitted $value
	 * @param string The name of the table for the current module
	 * @param string The value to be converted into a permalink
	 * @return string Returns a permalink, which is the value stripped of all punctuation, all lowercase, with underscores as spaces
	 */
	public function generatePermalink($value,$id='', $field='permalink', $category_id='', $category_field=''){
		$i = '';
		$valid = false;
		$value = strip_tags(trim($value));
		switch(mb_detect_encoding($value)){
			case "UTF-8":
				$utf8_chars  = explode(' ','á é í ó ú Á É Í Ó Ú ü Ü ñ Ñ');
				$ascii_chars = explode(' ','a e i o u A E I O U u U n N');
				$value = utf8_decode(str_replace($utf8_chars,$ascii_chars,$value));
				break;
			default:
				break;
		}
		$permalink = preg_replace("/[^A-Za-z0-9\s\-\/\_\|]/",'',$value);
		$permalink = preg_replace('/\W+/', '-', strtolower($permalink));
		$base_permalink = $permalink;
		if(strlen(trim($id))){
			$category_params = " AND id <> '$id'";
		}
		if(strlen(trim($category_id)) && strlen(trim($category_field))){
			$category_params .= " AND `".$category_field."` = '".$category_id."'";
		}
		while($valid == false){
			$existingPermalink = $this->fetchAll("WHERE `".$field."` = '$permalink' $category_params");
			if(!sizeof($existingPermalink)){
				$valid = true;
			}else{
				$i++;
				$permalink = $base_permalink.'-'.$i;
			}
		}
		return $permalink;
	}

	public function setMessageScope($scope){
		$this->_messageScope = $scope;
		return $this;
	}

	public function getMessageScope(){
		return $this->_messageScope;
	}

	public function addMessage($label,$data){
		$this->_messages[$label] = $data;
		return $this;
	}

	public function getMessages(){
		return $this->_messages;
	}

	public function getMessage($label){
		return $this->_messages[$label];
	}

	public function getMessageType($label){
		return $this->_messages[$label]['type'];
	}

	public function getMessageText($label){
		return $this->_messages[$label]['text'];
	}

	public function clearMessages(){
		$this->_messages = array();
		$this->_messageScope = '';
		return $this;
	}

	public function hasMessage($label){
		return isset($this->_messages[$label]);
	}

	public function hasMessages($type = 'failure'){
		foreach($this->_messages as $label=>$message){
			if($this->getMessageType($label) == $type){
				return true;
			}
		}
		return false;
	}

	public function prepareMessages(){
		$messages = $this->getMessages();
		$array = array();
		foreach($messages as $label=>$message){
			$array[$label] = $this->formatMessage($message);
		}
		return $array;
	}

	public function formatMessage($message){
		ob_start();
		?>
		<span class="<?php echo $message['type']; ?>"><?php echo $message['text']; ?></span>
		<?php
		return ob_get_clean();
	}

	public function addRefreshElement($selector, $content="", $action=""){
		if($action != ""){
			$element = array("selector"=>$selector, "content"=>$content, "action"=>$action);
		}
		else{
			$element = array("selector"=>$selector, "content"=>$content);
		}
		array_push($this->_refreshElements, $element);
		return $this;
	}

	public function getRefreshElements()
	{
		return $this->_refreshElements;
	}
}
?>
