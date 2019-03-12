<?php
class Image
{
	protected $_image;
	protected $_type;
	protected $_directory;
	protected $_filename;
	protected static $_imageTypes = array(
									'jpg',
									'jpeg',
									'gif',
									'png'
									//'bmp',
									//'tif',
									//'tiff'
									);

	public function __construct($filename = ''){
		$this->_directory = $GLOBALS['upload_path'];
		$this->load($filename);
	}

	public static function getImageTypes(){ return self::$_imageTypes;}
	protected function _render(){
		switch($this->getType()){
			case IMAGETYPE_JPEG:
				return imagecreatefromjpeg($this->_filename);
				break;
			case IMAGETYPE_GIF:
				return imagecreatefromgif($this->_filename);
				break;
			case IMAGETYPE_PNG:
				return imagecreatefrompng($this->_filename);
				break;
			case IMAGETYPE_BMP:
				return imagecreatefrombmp($this->_filename);
				break;
			default:
				return imagecreatefromjpeg($this->_filename);
				break;
		}
	}

	protected function _normalizePath($path){
		if(strstr($path,"/") || strstr($path,"\\")){
			return $path;
		}else{
			return $this->_directory.$path;
		}
	}

	public function setType(){
		$info = getimagesize($this->_filename);
		$this->_type = $info[2];
	}
	public function getType(){
		return $this->_type;
	}
	public function setDirectory($directory){
		$this->_directory = $directory;
	}
	public function getDirectory(){
		return $this->_directory;
	}
	public function setFilename($filename){
		$this->_filename = $filename;
	}
	public function getFilename(){
		return $this->_filename;
	}

	public function load($filename){
		if(!empty($filename)){
			$this->_filename = $this->_normalizePath($filename);
			$this->setType();
			$this->_image = $this->_render();
		}
	}

	public function save($filename = '',$quality = 80, $permissions = NULL, $type = NULL){
		if(empty($filename)){
			$filename = $this->_filename;
		}
		$filename = $this->_normalizePath($filename);
		// Verify that the type is valid
		if(!$type || !in_array($type,array(IMAGETYPE_JPEG,IMAGETYPE_GIF,IMAGETYPE_PNG))){
			$type = $this->getType();
		}
		//
		switch($type){
			case IMAGETYPE_JPEG:
				imagejpeg($this->_image,$filename,$quality);
				break;
			case IMAGETYPE_GIF:
				imagegif($this->_image,$filename);
				break;
			case IMAGETYPE_PNG:
				imagepng($this->_image,$filename);
				break;
			case IMAGETYPE_BMP:
				imagebmp($this->_image,$filename);
				break;
			default:
				imagejpeg($this->_image,$filename,$quality);
				break;
		}
		if($permissions != null) {
			chmod($filename,$permissions);
		}
	}

	public function output($imageType=IMAGETYPE_JPEG){
		switch($this->getType()){
			case IMAGETYPE_JPEG:
				imagejpeg($this->_image);
				break;
			case IMAGETYPE_GIF:
				imagegif($this->_image);
				break;
			case IMAGETYPE_PNG:
				imagepng($this->_image);
				break;
			case IMAGETYPE_BMP:
				imagebmp($this->_image);
				break;
			default:
				imagejpeg($this->_image);
				break;
		}
	}

	public function getWidth(){
		return imagesx($this->_image);
	}

	public function getHeight(){
		return imagesy($this->_image);
	}

	public function scale($scale){
		$width = $this->getWidth() * $scale/100;
		$height = $this->getHeight() * $scale/100;
		$this->resize($width,$height);
	}

	public function getExt(){
		$extension = image_type_to_extension($this->getType(), false);
		$replace = array(
        	'jpeg' => 'jpg',
        	'tiff' => 'tif',
    	);
		return str_replace(array_keys($replace), array_values($replace), $extension);
	}

	public function resizeToHeight($height){
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	public function resizeToWidth($width){
		$ratio = $width / $this->getWidth();
		$height = $this->getHeight() * $ratio;
		$this->resize($width,$height);
	}

	public function resize($width, $height){
		$newImage = imagecreatetruecolor($width, $height);
		imagealphablending($newImage, false);
		imagesavealpha($newImage,true);
		if($this->getType() == 'IMAGETYPE_GIF'){
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
		}else{
			imagecopyresampled($newImage, $this->_image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		}
		$this->_image = $newImage;
	}


	public function crop($width, $height, $xOffset=NULL, $yOffset=NULL, $bgColor='#000000'){
		$newImage = imagecreatetruecolor($width, $height);
		$bgColor = hex2rgb($bgColor);
		$background = imagecolorallocatealpha($newImage, $bgColor[0], $bgColor[1], $bgColor[2], 127);
		if($xOffset === NULL){
			$xOffset = ($this->getWidth()-$width)/2;
		}
		if($yOffset === NULL){
			$yOffset = ($this->getHeight()-$height)/2;
		}
		imagealphablending($newImage, false);
		imagesavealpha($newImage,true);
		if($this->getType() == 'IMAGETYPE_GIF'){
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
		}else{
			imagecopyresampled($newImage, $this->_image, 0, 0, $xOffset, $yOffset, $width, $height, $width, $height);
		}
		
		if($width > $this->getWidth() || $height > $this->getHeight()){
			imagefill($newImage,0,0,$background);
			imagefill($newImage,$width-1,$height-1,$background);
		}
		$this->_image = $newImage;
	}

	/* Send this the width, height, TRUE for scaling the image to the largest size such that both its width and its height can fit inside the canvas size provided. FALSE to scale the image to be as large as possible so that the canvas is completely covered by the image. Some parts of the image may be cropped off outside the canvas's area. If set to TRUE, you can provide an additional hex color variable in this format: "#F0B0AD" as a background fill color in the event the image and canvas aspect ratios don’t match. */
	function canvas($w, $h, $contain=FALSE, $bgColor='#000000'){
		if($this->getWidth() != $width && $this->getHeight() != $height){
			//if($this->getType()==IMAGETYPE_GIF){$this->processGif();}
			$origAspect =  $this->getWidth() / $this->getHeight();
			$newAspect = $w / $h;
			if($contain){
				if($this->getWidth() > $this->getHeight()){
					if($origAspect < $newAspect){
						$this->resizeToHeight($h);
					}else{
						$this->resizeToWidth($w);
					}
					$this->crop($w,$h,NULL,NULL,$bgColor);
				}else{
					if($origAspect < $newAspect){
						$this->resizeToHeight($h);
					}else{
						$this->resizeToWidth($w);
					}
					$this->crop($w,$h,NULL,NULL,$bgColor);
				}
			}else{
				if($origAspect > $newAspect){
					$this->resizeToHeight($h);
				}else{
					$this->resizeToWidth($w);
				}
				$this->crop($w,$h);
			}
		}
	}



	// Determines if an image is a specified type.
	public function checkImageType($file,$valid_types = array('')){
		$newFile = new File($_FILES[$file]);
		return $newFile->checkFileTypes($valid_types);
	}

	/**
	 * function manageImage()
	 *
	 * This function generates an image preview thumbnail with a checkbox for deletion.
	 * @param string Name of the database table that contains the module data
	 * @param integer The database record 'id' value that represents which record this file is associated with
	 * @param integer Either a 0 or 1, 0 - do not show delete checkbox, 1 - show delete checkbox
	 * @return string Returns a thumbnail to preview the file along with a delete checkbox, if specified
	 */
	public function manageImage($module_table,$id,$delete_id = 0,$ext = 'jpg',$width = 0,$height = 0,$class = "img-responsive",$input = 'image',$label = 'Manage Image'){
		$listTypes = '';
		foreach($this->getImageTypes() as $type){
			$listTypes .= ',.'.$type;
		};
		if($delete_id == 0){
			$required = 'class="required"';
		}else{
			$required = '';
		}
		ob_start();?>
						<div class="form-group">
							<label for="<?php echo $input;?>" <?php echo $required;?>><?php echo $label;?></label>
							<?php if(is_file($GLOBALS['path'].$this->getDirectory().$module_table.$id.'.'.$ext)){?>
							<img src="<?php echo $this->getDirectory().$module_table.$id.'.'.$ext."?".rand(1,1000); ?>" title="Image Preview" alt="Image Preview" class="<?php echo $class; ?> module-image-preview" />
							<label class="btn btn-custom btn-file">
								<i class="fa fa-picture-o" aria-hidden="true"></i> Replace Image ...  <input type="file" accept="<?php echo ltrim($listTypes, ','); ?>" id="<?php echo $input;?>" name="<?php echo $input;?>" style="display: inline-block!important;" />
							</label>
							<span class="file-name"></span>
							<br />
							<div class="instruction"><ul><li>Image should be <b><?php echo $width;?> pixels wide</b> by <b><?php echo $height;?> pixels tall</b>, or it may appear stretched or have edges cut off.</li></ul></div>
							<?php if($delete_id != 0){
									echo FormComponent::checkBox('checkbox'.$delete_id,'on','','checkbox'.$delete_id,'Delete Image');
								} ?>
							<?php }else{ ?>
							</br>
							<label class="btn btn-custom btn-file">
								<i class="fa fa-picture-o" aria-hidden="true"></i> Add Image ...
								<input type="file" accept="<?php echo ltrim($listTypes, ','); ?>" id="<?php echo $input;?>" name="<?php echo $input;?>" style="display: inline-block!important;" />
							</label>
							<span class="file-name"></span>
							<br />
							<div class="instruction">
								<ul>
									<li>Image should be <b><?php echo $width;?> pixels wide</b> by <b><?php echo $height;?> pixels tall</b>, or it may appear stretched or have edges cut off.</li>
								</ul>
							</div>
							<?php } ?>
							<div class="error <?php echo $input;?>"></div>
						</div>
		<?php return ob_get_clean();
	}

	/**
	 * function deleteImage()
	 *
	 * This function deletes an image relative to $GLOBALS['upload_path']
	 * @param string $GLOBALS['module_table'] value
	 * @param integer The database record 'id' value that represents which record this file is associated with
	 * @see globals.php
	 */
	public function deleteImage($module_table,$id,$ext = 'jpg'){
		@unlink($this->getDirectory().$module_table.$id.'.'.$ext);
		return;
	}


}
?>
