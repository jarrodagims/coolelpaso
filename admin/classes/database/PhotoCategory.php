<?php
class PhotoCategory extends Category{
	// Module Configuration
	public $_moduleClassName = 'Photo';
	public $_moduleCategoryName = 'PhotoCategory';
	// Static Variables
	protected static $_addLabel = 'Add Photo Gallery';
	protected static $_editLabel = 'Edit Photo Gallery';
	protected static $_moduleTitle = 'Photo Gallery';
	protected static $_containerName = 'Gallery';
	protected static $_itemsName = 'Photos';
	protected static $_divSelectorClass = 'photo';
	
	// Inherited Variables
	protected $_dbTable = 'photos_categories';
	protected $_permalinkField = 'permalink';
	protected $_action = 'index';
	
	// Inherited Variables - Category
	protected $_childClass = 'Photo';
	protected $_sortBy = 'sort_order';
	
	// Table Variables
	protected $_id;
	protected $_name;
	protected $_description;
	protected $_permalink;
	protected $_active = '0';
	protected $_sortOrder = '0';
	
	// Instance Variables
	protected $_photos = NULL;
	protected $_requiredFields = array(
									'name'
									);
	protected $_saveFields = array(
									'name',
									'description',
									'permalink',
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
	public static function getEditLabel(){return self::$_editLabel; }

	// Accessor Methods
	public function setId($value){$this->_id = (int) $value; return $this;}
	public function getId(){return $this->_id;}
	public function setName($value){$this->_name = (string) $value; return $this;}
	public function getName(){return $this->_name;}
	public function setDescription($value){$this->_description = (string) $value; return $this;}
	public function getDescription(){return $this->_description;}
	public function setPermalink($value){$this->_permalink = (string) $value; return $this;}
	public function getPermalink(){return $this->_permalink;}
	public function setActive($value){$this->_active = (string) $value; return $this;}
	public function getActive(){return $this->_active;}
	public function setSortOrder($value){$this->_sortOrder = (int) $value; return $this;}
	public function getSortOrder(){return $this->_sortOrder;}

	// Instance Methods
	public function setRecords($value = ''){return $this->setPhotos($value);}
	public function getRecords(){return $this->getPhotos();}

	public function setPhotos($value = ''){
		if(is_array($value)){
			$this->_photos = $value;
		}elseif($this->getId()){
			$faq = new $this->_moduleClassName();
			$this->_photos = $faq->fetchAll("WHERE `category` = ".$this->getId(),"ORDER BY `sort_order`");
		}else{
			$this->_photos = array();
		}
		return $this;
	}
	
	public function getPhotos(){
		if($this->_photos === NULL){
			$this->setPhotos();
		}
		return $this->_photos;
	}

	// Instance Methods
	public function setSaveFields($value){ $this->_saveFields = $value; return $this; }
	public function getSaveFields(){ return $this->_saveFields; }
	
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
		$this->setPhotos();
		if(sizeof($this->getPhotos())){
			ob_start();
			return ob_get_clean();
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
           	<div class="form-group">
            	<label for="name" class="required">Name</label>
                <input type="text" id="name" class="form-control" name="name" size="40" placeholder="Name" value="<?php echo process($this->getName()); ?>" />
               <div class="error name"></div>
        	</div>
        	<div class="form-group">
            	<label for="name">Description</label>
                <textarea id="description" name="description" rows="8" class="form-control" placeholder="Description"><?php echo process($this->getDescription()); ?></textarea>
                <div class="error description"></div>
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
		// options menu items
		ob_start(); ?>
		<ul class="actions">
			<li><a href="#" class="add-files"><i class="fa fa-plus" aria-hidden="true"></i>Add <?php echo self::$_itemsName; ?></a></li>
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
				$filePaths .= '"/'.$child->getFilePath().$child->getDbTable().$child->getId().'.'.$child->getExtension().'?'.rand(1,1000).'",';
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
		<ol>
			<li id="subMenuItem_<?php echo ($this->getId()+1);?>" class="lvl_<?php echo $current_level.' '.self::$_divSelectorClass; ?>">
				<div class="menuDiv input-group" style="width:100%">
					<div class="branch_content" style="padding:0">
						<input 
							id="file_input_modal_<?php echo $this->getId();?>" 
							<?php $image = new Image();
								foreach($image->getImageTypes() as $type){
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
				window.drop_zone_title = 'Images should be <b><?php $child = new Photo(); echo $child->_moduleImageCanvasX.' pixels wide</b> by <b>'.$child->_moduleImageCanvasY; ?> pixels tall</b><br /><br />Drag & drop Images here …';
				
				// generate galleries function
				initGallery();
				
				// gallery menu item - add photos
				$('#content').on('click', '.add-files', function(e){
					e.preventDefault();
					if($(this).parents('.<?php echo get_class($this); ?>').hasClass('mjs-nestedSortable-collapsed')){
						$(this).parents('.branch_content').find('.disclose, .btn-file').trigger('click');
					}
					$(this).parents('.<?php echo get_class($this); ?>').find('input:file').trigger( "click" );
				});
				
				// gallery menu item - delete gallery: removes self unless it has files
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
				
				// gallery menu item - bulk delete: hides initial buttons and shows delete checkbox
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
				
				// bulk delete menu - ok: launches bulk delete confirm modal from element tag, sets glbabl category to delete from
				$('#content').on('click', '.bulk-delete-ok', function(){
					var count = 0;
					var self = $(this).parents('.<?php echo get_class($this); ?>');
					self.find('input:checkbox').each(function (){
						if($(this).is(":checked")){
							count++;
						}
					});
					if(count === 0){
						notify({status: "danger", title: "Failure", message: "No images were selected."});
					}else{
						if(count === 1){
							self.find('input:checkbox').each(function (){
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
				
				// delete confirm modal - ok: removes files and resets gallery to initial state
				$('#confirmModal').on('click', '.btn-ok', function(e){
					var modalDiv = $(e.delegateTarget);
					var activeDiv = $('#menuItem_'+removeCatId);
					modalDiv.modal('hide');
					$(activeDiv).find('input:checkbox').each(function (){
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
			
			// creates image gallery
			function initGallery() {
				$(".file-loading").fileinput({
					browseIcon: '<i class="fa fa-picture-o" aria-hidden="true"></i>',
					browseClass: 'btn btn-custom pull-right',
					showRemove: false,
					showBrowse: false,
					showUpload: false,
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
					maxFileSize: 20000,
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
										actionZoom: '<button type="button" class="kv-file-edit  btn btn-custom hidden" title="Edit" data-toggle="modal" data-target="#moduleModal" data-remote="../../includes/module_form.php?module=Photo&action=edit&id={dataJustKey}" ><i class="fa fa-pencil " aria-hidden="true"></i></button>\n<button type="button" class="kv-file-zoom btn btn-custom" title="{zoomTitle}"><i class="fa fa-search" aria-hidden="true"></i></button>'}
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
			function moduleSpecificRefresh(content,action){
				if(action === 'add'){
					initSortable();
					initGallery();
					initPopOver();
					toggleVisible();
				}
				else if(action === 'edit'){
					var self = $('.file-preview-frame[data-realindex="'+content.id+'"]:first');
					var selfCache = self.next();
					var current_parent = self.parents('.<?php echo get_class($this); ?>');
					var moved_cat_parent = $('#menuItem_'+content.category);
					if(current_parent.attr('id').split('_')[1] != content.category){
						var moved_cat = moved_cat_parent.find('.file-initial-thumbs');
						if(moved_cat_parent.find('.file-preview-thumbnails').children().length){
							self.clone().appendTo(moved_cat);
							selfCache.clone().appendTo(moved_cat);
						}else{
							moved_cat_parent.find('.file-preview-thumbnails').append('<div class="file-initial-thumbs"></div>');
							var clone_to = moved_cat_parent.find('.file-initial-thumbs');
							self.clone().appendTo(clone_to);
							selfCache.clone().appendTo(clone_to);
							
						}
						moved_cat_parent.find('.file-drop-zone-title').remove();
						moved_cat_parent.find('#file_input_modal_'+content.category).fileinput('reInitZoom');
						self.remove();
						selfCache.remove();
						if(current_parent.find('.file-initial-thumbs').children().length < 1){
							current_parent.find('.file-initial-thumbs').remove();
							current_parent.find('.file-drop-zone').append('<div class="file-drop-zone-title">'+drop_zone_title+'</div>');
						}
					}
					self.find('.file-footer-caption').text(content.caption);
					src = self.find(".file-preview-image").attr('src');
					self.find(".file-preview-image").attr("src", src + Math.random());
				}
				
			}
		</script>
	<?php return ob_get_clean();
	}
}
?>