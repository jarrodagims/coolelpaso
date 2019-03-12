<?php
class DocumentCategory extends Category{
	// Module Configuration
	public $_moduleClassName = 'Document';
	public $_moduleCategoryName = 'DocumentCategory';
	// Static Variables
	protected static $_addLabel = 'Add Document Category';
	protected static $_editLabel = 'Edit Document Category';
	protected static $_moduleTitle = 'Document Category';
	protected static $_containerName = 'Category';
	protected static $_itemsName = 'Documents';
	protected static $_divSelectorClass = 'Document';
	protected static $_staticChildClass = 'Document';
	// Inherited Variables"
	protected $_dbTable = 'documents_categories';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Inherited Variables - Category
	protected $_childClass = 'Document';
	protected $_sortBy = 'sort_order';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_permalink;
	protected $_locked = '0';
	protected $_sortOrder = '0';	
	
	// Instance Variables
	protected $_items = NULL;
	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
									'name',
									'permalink',
									'locked',
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
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setLocked($value){$this->_locked = (string) $value; return $this;}
	public function getLocked(){return $this->_locked;}
	public function setSortOrder($value){ $this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function setSaveFields($value){$this->_saveFields = $value; return $this;}
	public function getSaveFields(){return $this->_saveFields;}
	public function setRecords($value = ''){return $this->setItems($value);}
	public function getRecords(){return $this->getItems(); }
	
	public function setItems($value = ''){
		if(is_array($value)){
			$this->_items = $value;
		}elseif($this->getId()){
			$file = new $this->_moduleClassName();
			$this->_items = $file->fetchAll("WHERE `category` = ".$this->getId(),"ORDER BY `sort_order`,`id`");
		}else{
			$this->_items = array();
		}
		return $this;
	}
	
	public function getItems(){
		if($this->_items === NULL){
			$this->setItems();
		}
		return $this->_items;
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
	
	
	public function buildHref(){
		return '/documents/'.$this->getPermalink();
	}
	
	// Action Methods
	public function indexAction(){
		if($this->exists($this->getPermalink())){
			$this->setItems();
			if(sizeof($this->getItems())){
				ob_start();
				?>
				<h3><?php echo $this->getName(); ?></h3>
				<ul>
				<?php foreach($this->getItems() as $item){ ?>
						<li>
							<?php echo $item->buildLink(); ?>
							<?php if(strlen(trim($item->getDescription()))){ ?>
								<p><?php echo nl2br($item->getDescription()); ?>
							<?php } ?>
						</li>
				<?php } ?>
				</ul>
				<?php 
				return ob_get_clean();
			}
		}
	}
	
	public function moduleIndexAction(){
		ob_start();?>
		<div class="index-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<a class="btn btn-green pull-left" href="<?php echo $this->buildModalUrl('add'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-plus"></i>Create New <?php echo self::$_moduleTitle; ?></a>
				</div>
			</div>
			<?php 
			echo $this->buildSortingStructure();
			?>
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
	
	protected function buildAdminAddEditHtml($action){
		if(!in_array($action,array('add','edit'))){
			return '';
		}
		$actionLabel = $this->{'get'.ucfirst($action).'Label'}();
		//
		$messages = $this->prepareMessages();
		$this->clearMessages();
		$GLOBALS['page_title'] = $actionLabel;
		//
		ob_start();
		?>
        <div class="error general"></div>
		<form id="form" action="action_categories.php" method="post" enctype="multipart/form-data">
       		<div class="row">
				<div class="col-sm-12">
					<div class="denote-required">
						<i class="fa fa-asterisk"></i>
						Denotes a required field
					</div>
				</div>
			</div>
        	<div class="row">
            	<div class="col-sm-6">
            		<label for="name" class="required">Name</label>
					<input type="text" id="name" class="form-control" name="name" size="40" placeholder="Name" value="<?php echo process($this->getName()); ?>" />
				   <div class="error name"></div>
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
		$child_item = new self::$_staticChildClass();

		// options menu items
		ob_start(); ?>
		<ul class="actions">
			<li><a href="<?php echo $child_item->buildModalUrl('add', $this->getId()); ?>" data-toggle="modal" data-target="#moduleModal" class="add-single-file"><i class="fa fa-file-o" aria-hidden="true"></i>Add <?php echo rtrim(self::$_itemsName, 's'); ?></a></li>
			<li><a href="#" class="add-files"><i class="fa fa-files-o" aria-hidden="true"></i>Add Multiple <?php echo self::$_itemsName; ?></a></li>
			<li><a href="<?php echo $this->buildModalUrl('edit'); ?>" data-toggle="modal" data-target="#moduleModal"><i class="fa fa-pencil"></i>Edit <?php echo self::$_containerName; ?></a></li>
			<li><a href="#" class="category-delete" id="<?php echo $this->getId(); ?>"><i class="fa fa-trash-o"></i>Delete <?php echo self::$_containerName; ?></a></li>
			<li><a href="#" class="bulk-delete"><i class="fa fa-times" aria-hidden="true"></i>Bulk Delete <?php echo self::$_itemsName; ?></a></li>
		</ul>
		<?php $popover_menu = ob_get_clean();
		
		// category bar
		ob_start(); ?>
			<div class="menuDiv input-group">
				<span title="Drag item to change sort order" class="input-group-addon draghandle">
					<i class="fa fa-arrows" aria-hidden="true"></i>
				</span>
				<div class="branch_content">
					<span title="Click to show/hide sub-items" class="disclose hidden">
						<i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
					</span>
					<span data-id="<?php echo $this->getId(); ?>" class="itemTitle" id="itemTitle_<?php echo $this->getId(); ?>">
						<?php echo ucwords($this->getName()); ?>
					</span>
					<span data-id="<?php echo $this->getId(); ?>" class="action_menu popover-menu">
						<a tabindex="0" class="pull-right" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content='<?php echo $popover_menu; ?>'><i class="fa fa-gear"></i></a>
					</span>

				</div>
			</div>
        <?php
		return ob_get_clean();	
	}
	
	public function buildListOfChildren($current_level){
		$children = $this->getChildren();
		$fileCatData = '{"category_id":"'.$this->getId().'", "name":"'.$this->getName().'"}';
		if(sizeof($children)){
			$filePaths = '';
			$fileConfigs = '';
			foreach($children as $child){
					// Process URL override
				if(trim($child->getUrl())){
					$type = 'text';
					//set extension to web fileinput.js plugin uses the caption extension to determine file icon to use
					//dertermine if URL is internal or external and set fileinput.js configs accordingly
					if(substr($child->getUrl(), 0, 1) === '/'){
						$filePaths .= '"http://'.$_SERVER['HTTP_HOST'].$child->getUrl().'",';
					}else{
						$extension = explode('.', $child->getUrl());
						$file = new File();
						if($file->isAcceptedExtension(end($extension),$child->getFileTypes())){$type = 'html';}
						$filePaths .= '"'.$child->getUrl().'",';
					}
					//create file config for display
					$fileConfigs .=	'
								{
									"type": "'.$type.'",
									"width": "'.$child->_moduleThumbCanvasX.'px",
									"height": "'.$child->_moduleThumbCanvasY.'px",
									"caption": "'.htmlspecialchars($child->getTitle(), ENT_QUOTES).'.web", 
									"url": "action.php?action=delete&id='.$child->getId().'", 
									"key": '.$child->getId().'
								},';
				}else{
					$filePaths .= '"'.$GLOBALS['file_path'].$child->getDbTable().$child->getId().'.'.$child->getExtension().'?'.rand(1,1000).'",';
					$fileConfigs .=	'
								{
									"type": "pdf",
									"width": "'.$child->_moduleThumbCanvasX.'px",
									"height": "'.$child->_moduleThumbCanvasY.'px",
									"caption": "'.htmlspecialchars($child->getTitle(), ENT_QUOTES).'.'.$child->getExtension().'", 
									"url": "action.php?action=delete&id='.$child->getId().'", 
									"key": '.$child->getId().'
								},';
				}
			}
		}
		ob_start(); ?>
		<ol>
			<li id="subMenuItem_<?php echo ($this->getId()+1);?>" class="lvl_<?php echo $current_level.' '.self::$_divSelectorClass; ?>">
				<div class="menuDiv input-group" style="width:100%">
					<div class="branch_content" style="padding:0">
						<input 
							id="file_input_modal_<?php echo $this->getId();?>" 
							<?php $subClass = new self::$_staticChildClass();
								foreach($subClass->getFileTypes() as $type){
								$listTypes .= ',.'.$type;
							}; ?>
							accept="<?php echo ltrim($listTypes, ','); ?>" 
							name="file_input_modal_<?php echo $this->getId();?>[]" 
							type="file" 
							multiple 
							class="file-loading" 
							<?php if(sizeof($children)){ ?>data-initial-preview='[<?php echo rtrim($filePaths,','); ?>]' 
							data-initial-preview-config='[<?php echo rtrim($fileConfigs,','); ?>]' 
							<?php } ?>data-upload-extra-data='<?php echo $fileCatData; ?>'
						>
					</div>
				</div>
			</li>
		</ol>
		<?php 
		return ob_get_clean();
	}	
	
	public function buildAdminJavascript(){	
		ob_start();?>
		<script>
			$(document).ready(function(){
				// "global" title for dropzone
				$('body').on('click', '.external', function (e){
					e.stopImmediatePropagation();
					e.preventDefault();				
					var href = $(this).attr('href');
					window.open(document.location.origin+href, '_blank');
				});
				window.drop_zone_title = 'Drag & drop files here …';
				
				// generate categories function
				initGallery();

				// category menu item - add single file or url
				$('#content').on('click', '.add-single-file', function(e){
					e.preventDefault();
					if($(this).parents('.<?php echo get_class($this); ?>').hasClass('mjs-nestedSortable-collapsed')){
						$(this).parents('.branch_content').find('.disclose, .btn-file').trigger('click');
					}
				});
				// category menu item - add file
				$('#content').on('click', '.add-files', function(e){
					e.preventDefault();
					if($(this).parents('.<?php echo get_class($this); ?>').hasClass('mjs-nestedSortable-collapsed')){
						$(this).parents('.branch_content').find('.disclose, .btn-file').trigger('click');
					}
					$(this).parents('.<?php echo get_class($this); ?>').find('input:file').trigger( "click" );
				});
				
				// category menu item - delete category: removes self unless it has files
				$('#content').on('click', '.category-delete', function(e){
					e.preventDefault();
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					$.ajax({
						url:"action_categories.php?action=delete&id="+$(this).attr('id'), 
						success:function(data){
							//console.log(data);
							data = jQuery.parseJSON(data);
							notify(data.formOutcome);
							if(data.formOutcome.status === 'success'){
								$(self).remove();
							}
						}
					});
				});
				
				// category menu item - bulk delete: hides initial buttons and shows delete checkbox
				$('#content').on('click', '.bulk-delete', function(e){
					e.preventDefault();
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					if(self.hasClass('mjs-nestedSortable-collapsed')){
						$(this).parents('.branch_content').find('.disclose').trigger('click');
					}
					self.find('.btn-file, .file-footer-buttons, .file-drag-handle').hide();
					self.find('.checkbox').removeClass('hidden');
					self.find('.bulk-delete-apply, .checkbox').show('slide', {direction:'right'}, 200);
					
				});
				
				// bulk delete menu - select all: toggles all checkboxes in category
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
				
				// bulk delete menu - ok: launches bulk delete confirm modal from element tag, sets globabl category to delete from
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
							window.removeCatId = self.attr('id').split("_")[1];
							$(this).next().trigger('click');
						}
					}
				});
				
				// delete confirm modal - ok: removes files and resets category to initial state
				$('#confirmModal').on('click', '.btn-ok', function(e){
					var modalDiv = $(e.delegateTarget);
					var activeDiv = $('#menuItem_'+removeCatId);
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
					var width = 900; //offset for total padding of modal
					if(width < $(window).width()){ //only apply if image width is less than the window's width
						$('.file-zoom-dialog').find('.preview-modal-mod').css('width', width);
					}
				});
				
				// overrides fileinput.js link clicking suppression
				$('#content').on('click', '.kv-file-download', function(){
					location.href = $(this).attr('href');
				});
				
				//file sort
				$('#content').on('mousedown', '.drag-handle-init', function(e){
					$(this).parents('.<?php echo get_class($this); ?>').find('input:file').on('filesorted', function(event, params) {
						var sortElements = [];
						// array of thumbnail container image id from the realindex data attribute tag
						$(this).parents('.<?php echo get_class($this); ?>').find( ".file-preview-frame" ).each(function() {
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

			// creates file category
			function initGallery() {
				$(".file-loading").fileinput({
					browseIcon: '<i class="fa fa-file-o" aria-hidden="true"></i>',
					browseClass: 'btn btn-custom pull-right',
					showRemove: false,
					showUpload: false,
					showBrowse: false,
					showCancel: false,
					initialPreviewFileType: 'other',
					uploadUrl: 'action.php?action=upload',
					theme: "fa",
					deleteUrl: "action.php?action=delete",
					showCaption: false,
        			showClose: false,
					uploadAsync: false,
					showPreview: true,
					previewFileIcon: '<i class="fa fa-file"></i>',
					preferIconicPreview: true,
					allowedPreviewTypes: null,
					allowedFileExtensions: <?php $child = new self::$_staticChildClass(); echo json_encode($child->getFileTypes());?>,
					previewFileIconSettings: { // configure your icon file extensions
						'doc': '<div class="file-icon-wrapper file-doc"><i class="fa fa-file-word-o"></i></div>',
						'xls': '<div class="file-icon-wrapper file-xls"><i class="fa fa-file-excel-o"></i></div>',
						'ppt': '<div class="file-icon-wrapper file-ppt"><i class="fa fa-file-powerpoint-o"></i></div>',
						'pdf': '<div class="file-icon-wrapper file-pdf"><i class="fa fa-file-pdf-o"></i></div>',
						'txt': '<div class="file-icon-wrapper file-txt"><i class="fa fa-file-text-o"></i></div>',
						'rtf': '<div class="file-icon-wrapper file-rtf"><i class="fa fa-file-rtf-o"></i></div>',
						'web': '<div class="file-icon-wrapper file-web"><i class="fa fa-file-globe-o"></i></div>',
						'zip': '<div class="file-icon-wrapper file-zip"><i class="fa fa-file-archive-o"></i></div>'
					},
					previewFileExtSettings: { // configure the logic for determining icon file extensions
						'web': function(ext) {
							return ext.match(/(com|net|gov|edu|org|web)$/i);
						},
						'doc': function(ext) {
							return ext.match(/(doc|docx)$/i);
						},
						'xls': function(ext) {
							return ext.match(/(xls|xlsx|xlsm)$/i);
						},
						'ppt': function(ext) {
							return ext.match(/(ppt|pptx)$/i);
						},
						'txt': function(ext) {
							return ext.match(/(txt)$/i);
						},
						'rtf': function(ext) {
							return ext.match(/(rtf)$/i);
						},
						'zip': function(ext) {
							return ext.match(/(zip)$/i);
						}
					},
					dropZoneTitle: drop_zone_title,
					msgZoomModalHeading: 'Preview of Basic Content Found in: ',
					removeFromPreviewOnError:true,
					maxFileSize: 20000,
					overwriteInitial: false, 
					initialPreviewAsData: true,
					purifyHtml: true,
						layoutTemplates:{main2: 
										'<div class="clearfix manage-bar">{remove}\n{cancel}\n{upload}\n{browse}\n' +
										'<div class="bulk-delete-apply">\n' +
										'	<button type="button" title="Toggle Selection" class="btn btn-custom bulk-delete-select-all"><i class="fa fa-check-square-o" aria-hidden="true"></i>Select All</button>\n' +
										'	<button type="button" tabindex="500" title="Bulk Delete" class="btn btn-custom bulk-delete-ok pull-right" href="#"><span class="hidden-xs">Ok</span></button>\n' +
										'	<button type="button" class="hidden bulk-delete-confirm" href="<?php echo $this->buildModalUrl('confirm','bulk_delete_confirm'); ?>" data-toggle="modal" data-target="#confirmModal"></button>\n' +
										'	<button type="button" title="Cancel" class="btn btn-default bulk-delete-cancel padded pull-right">Cancel</button>\n' +
										'</div>\n'+
										'</div>{preview}\n<div class="kv-upload-progress hide"></div>\n',
										actionZoom: '<a class="kv-file-download btn btn-custom" title="Download" href="/admin/includes/get_file.php?class=<?php echo self::$_staticChildClass; ?>&id={dataJustKey}"><i class="fa fa-download" aria-hidden="true"></i></a>\n<button type="button" class="kv-file-edit  btn btn-custom hidden" title="Edit" data-toggle="modal" data-target="#moduleModal" data-remote="../../includes/module_form.php?module=Document&action=edit&id={dataJustKey}" ><i class="fa fa-pencil " aria-hidden="true"></i></button>'}
				}).on("filebatchselected", function(event, files) {
					// trigger upload method immediately after files are selected
					
					if($(this).parents('.<?php echo get_class($this); ?>').find('.kv-error-close').length){
						return;
					}
					
					$(this).fileinput('upload');

				}).on('filebatchuploadcomplete', function(event, data, previewId, index) {
					$(this).parents('.<?php echo get_class($this); ?>').find('.kv-upload-progress').empty().addClass('hide');
					
				});
			}
			
			//processes module's unique content after an entry is created, modified, or removed
			function moduleSpecificRefresh(content,action,selector){
				if(action === 'add'){
					initSortable();
					initGallery();
					initPopOver();
					toggleVisible();
					if(content.category != ''){
						$('#menuItem_'+content.category).find('.file-drop-zone-title').remove();
						$('#file_input_modal_'+content.category).fileinput('addExternal',content);
					}
				}
				else if(action === 'edit'){
					var self = $('.file-preview-frame[data-realindex="'+content.id+'"]:first');
					var selfCache = self.next();
					var current_parent = self.closest('.<?php echo get_class($this); ?>');
					var moved_cat_parent = $('#menuItem_'+content.category);
					if(current_parent.attr('id').split('_')[1] != content.category){
						self.remove();
						selfCache.remove();
						if(current_parent.find('.file-initial-thumbs').children().length < 1){
							current_parent.find('.file-initial-thumbs').remove();
							current_parent.find('.file-drop-zone').append('<div class="file-drop-zone-title">'+drop_zone_title+'</div>');
						}
						if(moved_cat_parent.find('.file-initial-thumbs').children().length > 0){
							$('#menuItem_'+content.category).find('.file-drop-zone-title').remove();
						}
					}
					$('#file_input_modal_'+content.category).fileinput('addExternal',content);
					moved_cat_parent.find('#file_input_modal_'+content.category).fileinput('reInitZoom');
				}
			}
		</script>
	<?php return ob_get_clean();
	}
}
?>