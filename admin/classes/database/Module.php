<?php
class Module extends Model{
	public $_moduleName	= 'CMS Modules';
	// Module Config
	public $_moduleDir = 'modules';
	public $_moduleTable = 'modules';
	public $_moduleClassName = 'CmsModule';
	public $_moduleDescription = 'This section allows the super admin to manage CMS Modules.';
	public $_moduleIcon = 'fa-key';
	
	// Static Variables
	protected static $_addLabel = '';
	protected static $_editLabel = '';
	
	// Inherited Variables
	protected $_dbTable	= 'modules';
	protected $_permalinkField = 'class';
	protected $_action = 'index';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_dir;
	protected $_modTable;
	protected $_class;
	protected $_description;
	protected $_icon;
	protected $_enabled = '0';
	protected $_super = '0';
	protected $_sortOrder = '0';
	
	// Instance Variables
	protected $_requiredFields = array(
	);
	protected $_saveFields = array(
									'id',
									'name',
									'dir',
									'mod_table',
									'class',
									'description',
									'icon',
									'enabled',
									'super',
									'sort_order'
							);
	
	// Constructor
	public function __construct($id = 0){
		parent::__construct($id);
	}
	
	// Static Methods
	public static function setAddLabel($value){self::$_addLabel = (string) $value;}
	public static function getAddLabel(){return self::$_addLabel;}
	public static function setEditLabel($value){self::$_editLabel = (string) $value;}
	public static function getEditLabel(){return self::$_editLabel;}
	
	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setDir($value){$this->_dir = (string) $value; return $this;}
	public function getDir(){return $this->_dir;}
	public function setModTable($value){$this->_modTable = (string) $value; return $this;}
	public function getModTable(){return $this->_modTable;}
	public function setClass($value){$this->_class = (string) $value; return $this;}
	public function getClass(){return $this->_class;}
	public function setDescription($value){$this->_description = (string) $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setIcon($value){$this->_icon = (string) $value; return $this;}
	public function getIcon(){return $this->_icon;}
	public function setEnabled($value){ $this->_enabled = (string) $value; return $this;}
	public function getEnabled(){return $this->_enabled;}
	public function setSuper($value){ $this->_super = (string) $value; return $this;}
	public function getSuper(){return $this->_super;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}
	
	public function install(){
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `dir` varchar(255) NOT NULL DEFAULT '',
			 `mod_table` varchar(255) NOT NULL DEFAULT '',
			 `class` varchar(255) NOT NULL,
			 `description` text NOT NULL,
			 `icon` varchar(255) NOT NULL,
			 `enabled` enum('0','1') NOT NULL DEFAULT '0',
			 `super` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` smallint(6) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";

			$this->create($query);
		}
		
		return;
	}
	
	public function indexAction(){
		$moduleClassName = $this->getClass();
		$moduleClass = new $moduleClassName();
				
		echo $moduleClass->toHtml('module-index');
		
		if(method_exists($moduleClass, "buildAdminJavascript")){
			$GLOBALS['JAVASCRIPT'] .= $moduleClass->buildAdminJavascript();
		}
		
	}
	
	public function modalAction(){
		ob_start(); ?>
		<div id="moduleModal" class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
        		</div>
			</div>
		</div>
        <?php 
		echo $this->buildConfirmModal();
		return ob_get_clean();
	}
	
	public function buildInnerModal($title, $content, $includeButtons=1){
		ob_start(); ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8">
			<title><?php echo $title; ?></title>  
		</head>
		<body>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $title; ?></h4>
			</div>
			<div class="modal-body">
			<?php echo $content; ?>
			</div>
			<?php 
			if(intval($includeButtons)){?>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-custom" id="moduleSave"><i class="fa fa-pulse fa-spinner"></i>Save</button>
			</div>
			<?php } ?>
		</body>
		</html>
        <?php return ob_get_clean();
	}
	
	public function buildInnerConfirmModal($title, $content){
		ob_start(); ?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8">
			<title><?php echo $title; ?></title>  
		</head>
		<body>
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php echo $title; ?></h4>
			</div>
			<div class="modal-body">
			<?php echo $content; ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-custom btn-ok" id="moduleSave">Ok</button>
			</div>
		</body>
		</html>
        <?php return ob_get_clean();
	}
	
	public function buildConfirmModal(){
		ob_start(); ?>
		<div id="confirmModal" class="modal fade"  data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
        		</div>
			</div>
		</div>
        <?php return ob_get_clean();
	}
	
	
}
?>
