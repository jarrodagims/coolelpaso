<?php
class HomepageBanner extends Model{
	// Module Configuration
	public $_moduleName  = 'Slider';
	public $_moduleDir   = 'homepage_banners';
	public $_moduleTable = 'homepage_banners';
	public $_moduleClassName = 'HomepageBanner';
	public $_moduleDescription = 'This section allows administrators to create, modify, and delete Homepage Banners.';
	public $_moduleIcon = 'fa-home';
	public $_moduleThumbCanvasX = 773;
	public $_moduleThumbCanvasY = 350;
	public $_moduleImageCanvasX = 1366;
	public $_moduleImageCanvasY = 695;

	// Static Variables
	protected static $_addLabel = 'Add Homepage Banner';
	protected static $_editLabel = 'Edit Homepage Banner';
	protected static $_moduleTitle = 'Homepage Banners';
	protected static $_containerName = 'Homepage Banner';
	protected static $_itemsName = 'Banners';
	protected static $_divSelectorClass = 'banner';

	protected $_filePath = 'files/homepage_banners/';
	protected $_dbTable = 'homepage_banners';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	protected $_sortBy = 'sort_order';

	// Table Variables
	protected $_id;
	protected $_name = '';
	protected $_url = '';
	protected $_extension = '';
	protected $_description = '';
	protected $_active = '0';
	protected $_sortOrder = 0;

	// Instance Variables
	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
									'name',
									'url',
									'description',
									'extension',
									'active',
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
	public function setUrl($value){$this->_url = (string) $value; return $this;}
	public function getUrl(){return $this->_url;}
	public function setExtension($value){$this->_extension = (string) $value; return $this;}
	public function getExtension(){return $this->_extension;}
	public function setDescription($value){$this->_description = (string) $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}

	public function install(){
		# Register module
		$this->register();

		# Create table
		if(!$this->isInstalled()){
			$query = "
			CREATE TABLE `".$this->_dbTable."` (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `name` varchar(255) NOT NULL DEFAULT '',
			 `url` varchar(255) NOT NULL DEFAULT '',
			 `extension` varchar(255) NOT NULL,
			 `description` varchar(255) NOT NULL DEFAULT '',
			 `active` enum('0','1') NOT NULL DEFAULT '0',
			 `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
			 PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
			";
			$result = $this->create($query);
		}

		# Create Directory for files
		if (!file_exists($GLOBALS['path'].$this->getFilePath())) {
			@mkdir($GLOBALS['path'].$this->getFilePath());
		}

		return $this;
	}

	public function validate(){
		$includeGeneral = 0;
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

	// Action Methods
	public function indexAction(){
		$banners = $this->fetchAll("WHERE `active` = '1'", "ORDER BY `sort_order`");
		$i = 0;
		ob_start();
		foreach($banners as $banner){
			$active = ($i == 0)? ' active' : ''; $i = 1; ?>
				<div class="carousel-item<?php echo $active; ?>">
					<picture>
						  <source srcset="/files/homepage_banners/homepage_banners<?php echo $banner->getId().'.'.$banner->getExtension().'?'.rand(100,999); ?>" media="(min-width: 1400px)">
						  <source srcset="/files/homepage_banners/homepage_banners<?php echo $banner->getId().'.'.$banner->getExtension().'?'.rand(100,999); ?>" media="(min-width: 769px)">
						  <source srcset="/files/homepage_banners/homepage_banners<?php echo $banner->getId().'.'.$banner->getExtension().'?'.rand(100,999); ?>" media="(min-width: 577px)">
						  <img srcset="/files/homepage_banners/homepage_banners<?php echo $banner->getId().'.'.$banner->getExtension().'?'.rand(100,999); ?>" alt="responsive image" class="d-block img-fluid">
					</picture>
					<div class="carousel-caption" data-aos="flip-left" data-aos-easing="ease-out-cubic" data-aos-duration="1000">
						<div class="carousel-caption-container">
							<h2><?php echo $banner->getName(); ?></h2>
							<p><?php echo $banner->getDescription(); ?></p>
							<?php if($banner->getUrl()){ ?>
							<a href="<?php echo $banner->getUrl(); ?>" title="Learn More"><span class="btn btn-sm btn-outline-secondary">Learn More</span></a>
							<?php }; ?>
						</div>
					</div>
				</div>
				<!-- /.carousel-item -->
		<?php 
		}
		return ob_get_clean();
	}

	public function moduleIndexAction(){
		ob_start();?>
		<div class="index-wrapper <?php echo get_class($this); ?>">
			<?php echo $this->defaultListAction(); ?>
			<div class="row">
				<div class="col-xs-12">
					<?php echo $this->buildListOfChildren(); ?>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}


	public function adminAddAction(){
		return $this->buildAdminAddEditHtml('add');
	}

	public function adminEditAction(){
		return $this->buildAdminAddEditHtml('edit');
	}

	protected function buildAdminAddEditHtml($action,$mode = ''){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		$actionLabel = $this->{'get'.ucfirst($action).'Label'}();
		//
		$messages = $this->prepareMessages();
		$this->clearMessages();
		//
		ob_start();
		?>
        <div class="error general"></div>
		<form id="form" action="action.php" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-sm-12">
					<div class="denote-required">
						<i class="fa fa-asterisk"></i>
						Denotes a required field
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<!-- Title -->
					<div class="form-group">
						<label for="name" class="required">Title</label>
						<input type="text" id="name" name="name" size="40" class="form-control required" placeholder="Name" value="<?php echo process($this->getName()); ?>" />
						<div class="error name"></div>
					</div>
					<!-- URL -->
					<div class="form-group">
						<label for="url">URL</label>
						<input id="url" name="url" type="text" size="40" class="form-control" placeholder="URL" value="<?php echo process($this->getUrl()); ?>" />
						<div class="instruction">
							<ul>
								<li>Examples: "/privacy-policy" or "http://www.stantonstreet.com"</li>
							</ul>
						</div>
						<div class="error url"></div>
					</div>
					<!-- Caption -->
					<div class="form-group">
						<label for="description">Description</label>
						<input id="description" name="description" type="text" size="40" class="form-control" placeholder="Description" value="<?php echo process($this->getDescription()); ?>" />
						<div class="error description"></div>
					</div>

					<!-- Image -->
					<?php $image = new Image();
					$image->setDirectory(DIRECTORY_SEPARATOR.$this->getFilePath());
					echo $image->manageImage($this->getDbTable(),$this->getId(),0,$this->getExtension(),$this->_moduleImageCanvasX,$this->_moduleImageCanvasY); ?>
				</div>
			</div>
			<?php if($action == 'edit'): ?>
			<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
			<?php endif; ?>
			<input type="hidden" name="action" value="<?php echo $action; ?>" />
		</form>
		<?php
				$form = ob_get_clean();

		$modal = new Module();
		return $modal->buildInnerModal($actionLabel, $form);
	}

	public function adminAddJavascriptAction(){
		return $this->buildAdminAddEditJavascript('add');
	}

	public function adminEditJavascriptAction(){
		return $this->buildAdminAddEditJavascript('edit');
	}

	protected function buildAdminAddEditJavascript($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}

	}

	public function buildCss(){
	ob_start();?><style>
		#import{display:none;}
		#menuItem_1{background:#FCFCFC; border:1px solid #ddd;}
		.gear_menu{padding-bottom:7px;padding-right:3px;}
		.kv-file-content{height:inherit;overflow:hidden;}
		.kv-file-content .kv-preview-data{width:<?php echo $this->_moduleThumbCanvasX;?>px !important; max-height:<?php echo $this->_moduleThumbCanvasY;?>px !important;}
		.lvl_2 .menuDiv:hover{background: #FCFCFC;}
	</style>
	<?php return ob_get_clean();
	}



	public function adminNewRecord($category){
	}

	protected function bulkDeleteConfirmAction(){
		$title = 'Confirm Deletion';
		ob_start();?>
			<p>Are you sure you want to delete all selected Images?</p>
		<?php
		$body = ob_get_clean();
		$modal = new Module();
		return $modal->buildInnerConfirmModal($title, $body);
	}

	public function defaultListAction(){
		// options menu items
		ob_start(); ?>
		<ul class="actions">
			<li><a href="#" class="add-files"><i class="fa fa-picture-o" aria-hidden="true"></i>Add <?php echo self::$_itemsName; ?></a></li>
			<li><a href="#" class="bulk-delete"><i class="fa fa-times" aria-hidden="true"></i>Bulk Delete <?php echo self::$_itemsName; ?></a></li>
		</ul>
		<?php $popover_menu = ob_get_clean();

		// category bar
		ob_start(); ?>
			<div class="row banner-actions">
				<div class="col-sm-12">
					<a href="<?php echo $this->buildModalUrl('add', $this->getId()); ?>" data-toggle="modal" data-target="#moduleModal" class="btn btn-green pull-left"><i class="fa fa-picture-o" aria-hidden="true"></i>Add <?php echo self::$_itemsName; ?></a>
					<a href="#" class="bulk-delete btn btn-green pull-left"><i class="fa fa-times" aria-hidden="true"></i>Bulk Delete <?php echo self::$_itemsName; ?></a>
				</div>
			</div>
        <?php
		return ob_get_clean();
	}

	public function buildListOfChildren($current_level = 1){
		$children = $this->fetchAll("","ORDER BY `sort_order`,`id`");
		$fileCatData = '{"category_id":"1", "name":"'.self::$_containerName.'"}';
		if(sizeof($children)){
			$filePaths = '';
			$fileConfigs = '';
			foreach($children as $child){
				$filePaths .= '"'.DIRECTORY_SEPARATOR.$child->getFilePath().$child->getDbTable().$child->getId().'.'.$child->getExtension().'?'.rand(1,1000).'",';
				$fileConfigs .=	'
							{
								"width": "'.$child->_moduleThumbCanvasX.'px",
								"height": "'.$child->_moduleThumbCanvasY.'px",
								"caption": "'.htmlspecialchars($child->getName(), ENT_QUOTES).'",
								"url": "action.php?action=delete&id='.$child->getId().'",
								"key": '.$child->getId().'
							},';
			}
		}
		ob_start(); ?>
			<div id="menuItem_<?php echo ($this->getId()+1);?>" >
				<input
					id="file_input_modal"
					<?php $image = new Image();
						foreach($image->getImageTypes() as $type){
						$listTypes .= ',.'.$type;
					}; ?>
					accept="<?php echo ltrim($listTypes, ','); ?>"
					name="file_input_modal[]"
					type="file"
					multiple
					class="file-loading"
					<?php if(sizeof($children)){ ?>data-initial-preview='[<?php echo rtrim($filePaths,','); ?>]'
					data-initial-preview-config='[<?php echo rtrim($fileConfigs,','); ?>]'
					<?php } ?>data-upload-extra-data='<?php echo $fileCatData; ?>'
				>
			</div>
		<?php
		return ob_get_clean();
	}

	public function buildAdminJavascript(){
		$children = $this->fetchAll("WHERE `active` = '1'");
		foreach($children as $child){$disable .= ' ,#toggle_'.$child->getId();}
		ob_start();?>
		<script>
			$(document).ready(function(){

				// "global" title for dropzone
				window.drop_zone_title = 'Images should be <b><?php echo $child->_moduleImageCanvasX.' pixels wide</b> by <b>'.$child->_moduleImageCanvasY; ?> pixels tall</b><br /><br />Drag & drop Images here …';

				// generate galleries function
				initGallery();

				// toggle disabled
				$('<?php echo ltrim($disable,' ,');?>').toggleClass("slider-off slider-on");

				// gallery menu item - add photos
				$('#content').on('click', '.add-files', function(e){
					e.preventDefault();
					$(this).parents('.<?php echo get_class($this); ?>').find('input:file').trigger( "click" );
				});

				// gallery menu item - bulk delete: hides initial buttons and shows delete checkbox
				$('#content').on('click', '.bulk-delete', function(e){
					e.preventDefault();
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					self.find('.btn-file, .file-footer-buttons, .file-drag-handle').hide();
					self.find('.checkbox').removeClass('hidden');
					self.find('.bulk-delete-apply, .checkbox').show('slide', {direction:'right'}, 200);

				});

				// bulk delete menu - select all: toggles all checkboxes in gallery
				$('#content').on('click','.bulk-delete-select-all', function(e){
					e.preventDefault();
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					if (self.find(".bulk-delete-select-all i").hasClass("fa-check-square-o")){
						self.find('input:checkbox').prop('checked',true);
					}else{
						self.find('input:checkbox').prop('checked',false);
					}
					$(this).html($(this).html() == '<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All' ? '<i class="fa fa-square-o" aria-hidden="true"></i>Select None' : '<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All');
				});

				// bulk delete menu - cancel: reverts back to initial state
				$('#content').on('click', '.bulk-delete-cancel', function(){
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					self.find('.bulk-delete-apply, .checkbox').hide();
					self.find('.btn-file, .file-footer-buttons, .file-drag-handle').show('slide', {direction:'right'}, 200);
					self.find('input:checkbox').prop('checked',false);
					self.find('.bulk-delete-select-all').html('<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All');
				});

				// bulk delete menu - ok: launches bulk delete confirm modal from element tag, sets global category to delete from
				$('#content').on('click', '.bulk-delete-ok', function(){
					var count = 0;
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					self.find('input:checkbox').each(function () {
						if($(this).is(":checked")){
							count++;
						}
					});
					if(count === 0){
						notify({status: "danger", title: "Failure", message: "No images were selected."});
					}else{
						if(count === 1){
							self.find('input:checkbox').each(function () {
								if($(this).is(":checked")){
									$(this).parents('.file-actions').find('.kv-file-remove').trigger('click');
								}
							});
						}else{
							$(this).next().trigger('click');
						}
					}
				});

				// delete confirm modal - ok: removes files and resets gallery to initial state
				$('#confirmModal').on('click', '.btn-ok', function(e){
					var modalDiv = $(e.delegateTarget);
					var activeDiv = $('#menuItem_1');
					modalDiv.modal('hide');
					$(activeDiv).find('input:checkbox').each(function () {
						if($(this).is(":checked")){
							$(this).parents('.file-actions').find('.kv-file-remove').trigger('click');
						}
					});
					$(activeDiv).find('.bulk-delete-apply, .checkbox').hide();
					$(activeDiv).find('.btn-file, .file-footer-buttons, .file-drag-handle').show('slide', {direction:'right'}, 200);
					$(activeDiv).find('.bulk-delete-select-all').html('<i class="fa fa-check-square-o" aria-hidden="true"></i>Select All');
					$(activeDiv).find('input:checkbox').prop('checked',false);
				});

				//resize width of image preview modal to image width
				$('.file-zoom-dialog').on('show.bs.modal', function(){
					$('<img>').attr('src', $(this).find('.file-preview-image').attr('src')).load(function(){
						var width = this.width+32; //offset for total padding of modal
						if(width < $(window).width()){ //only apply if image width is less than the window's width
							$('.file-zoom-dialog').find('.preview-modal-mod').css('width', width);
						}
				  	});
				});

				//file sort
				$('#content').on('mousedown', '.drag-handle-init', function(e){
					$(this).parents('.<?php echo get_class($this); ?>').find('input:file').on('filesorted', function(event, params) {
						var sortElements = [];
						// array of thumbnail container image id from the realindex data attribute tag
						$(this).parents('#menuItem_1').find( ".file-preview-frame" ).each(function() {
							if($(this).parent().attr('class') !== 'kv-zoom-cache'){
								sortElements.push($(this).data('realindex'));
							}
						});
						$.ajax({
							method: "POST",
							url:"action.php?action=sort",
							data: {elements:sortElements},
							success:function(data){
								//console.log(data);
							}
						});
					});
				});
			});
			//end document onload

			// creates image gallery
			function initGallery() {
				$(".file-loading").fileinput({
					browseIcon: '<i class="fa fa-picture-o" aria-hidden="true"></i>',
					browseClass: 'btn btn-custom pull-right',
					showRemove: false,
					showUpload: false,
					showBrowse: false,
					uploadUrl: 'action.php?action=upload',
					theme: "fa",
					deleteUrl: "action.php?action=delete",
					showCaption: false,
        			showClose: false,
					showPreview: true,
					uploadAsync: false,
					allowedFileExtensions: <?php $image = new Image(); echo json_encode($image->getImageTypes());?>,
					dropZoneTitle: drop_zone_title,
					removeFromPreviewOnError:true,
					maxFileSize: 5000,
					overwriteInitial: false,
					initialPreviewAsData: true,
					initialPreviewFileType: 'image',
					purifyHtml: true,
						layoutTemplates:{main2:
										'<div class="clearfix manage-bar">{remove}\n{cancel}\n{upload}\n{browse}\n'+
										'<div class="bulk-delete-apply">\n' +
										'	<button type="button" title="Toggle Selection" class="btn btn-custom bulk-delete-select-all"><i class="fa fa-check-square-o" aria-hidden="true"></i>Select All</button>\n' +
										'	<button type="button" tabindex="500" title="Bulk Delete" class="btn btn-custom bulk-delete-ok pull-right" href="#"><span class="hidden-xs">Ok</span></button>\n' +
										'	<button type="button" class="hidden bulk-delete-confirm" href="<?php echo $this->buildModalUrl('confirm','bulk_delete_confirm'); ?>" data-toggle="modal" data-target="#confirmModal"></button>\n' +
										'	<button type="button" title="Cancel" class="btn btn-default bulk-delete-cancel padded pull-right">Cancel</button>\n' +
										'</div>\n'+
										'</div>{preview}\n<div class="kv-upload-progress hide"></div>\n',
										actionZoom: '<a href="action.php?action=toggle&id=toggle_{dataJustKey}" class="slider slider-off pull-left" id="toggle_{dataJustKey}"><div><span>Disabled</span><span>Active</span></div></a><button type="button" class="kv-file-edit  btn btn-custom hidden" title="Edit" data-toggle="modal" data-target="#moduleModal" data-remote="../../includes/module_form.php?module=HomepageBanner&action=edit&id={dataJustKey}" ><i class="fa fa-pencil " aria-hidden="true"></i></button>\n<button type="button" class="kv-file-zoom btn btn-custom" title="{zoomTitle}"><i class="fa fa-search" aria-hidden="true"></i></button>'}
				}).on("filebatchselected", function(event, files){
					// trigger upload method immediately after files are selected
					if($(this).parents('.<?php echo get_class($this); ?>').find('.kv-error-close').length){
						return;
					}
					$(this).fileinput('upload');

				}).on('filebatchuploadcomplete', function(event, data, previewId, index){
					$(this).parents('.<?php echo get_class($this); ?>').find('.kv-upload-progress').empty().addClass('hide');
					// toggle disabled
				$('<?php echo ltrim($disable,' ,');?>').toggleClass("slider-off slider-on");
				});
			}

			//processes module's unique content after an entry is created, modified, or removed
			function moduleSpecificRefresh(content,action){
				if(action === 'replace'){
					initGallery();
				}
				if(action === 'edit'){
					var self = $('.file-preview-frame[data-realindex="'+content.id+'"]:first');
					self.find('.file-footer-caption').text(content.caption);
					var src = self.find(".file-preview-image").attr('src');
					self.find(".file-preview-image").attr("src", src + Math.random());
				}
			}
		</script>
	<?php return ob_get_clean();
	}
}
?>
