<?php
class Category extends Model{
	# Inherited Variables
	// Do not default any of these
	protected $_childClass;
	protected $_sortBy;
	
	// Instance Methods
	public function getChildren(){
		return $this->getRecords();
	}
	
	public function buildSortingStructure($max_levels = 'all', $root_parent_id = 0, $sorting_enabled = 1, $currentLevel = 1, $single = ''){
		if($max_levels != 'all'){
			$max_levels	= intval($max_levels);
		}
		$className = get_class($this);
		$branches = new $className();
		if(is_numeric($single)){
			$branches = $branches->fetchAll("WHERE `id` = ".$single."", "ORDER BY `".$this->_sortBy."`");
			if($this->hasChildren() == true){
				$singleClass = 'mjs-nestedSortable-branch mjs-nestedSortable-collapsed';
			}
			else{
				$singleClass = 'mjs-nestedSortable-leaf';
			}
		}else{
			$branches = $branches->fetchAll("", "ORDER BY `".$this->_sortBy."`, `id`");
			$singleClass = '';
		}
		$list_items = '';
		$tree = '';
		
		//print_r($branches);
		
		if(count($branches)){
			ob_start();
			if(!is_numeric($single)){
				echo '<ol class="'.($currentLevel==1 ? 'sortable ' : '').'">';
			}
			
			# Foreach branch
			foreach($branches as $branch){
				echo '<li id="menuItem_'.$branch->getId().'" class="lvl_'.$currentLevel.' '.$className.' '.$singleClass.'">'; 
				echo $branch->toHtml('default_list');
					
				# Create instance of child class
				$childClassName = $this->_childClass;
				$childClass = new $childClassName();

				# If all levels are requested
				if($max_levels == 'all'){

					# Determine if children are also Categories
					if($branch->hasCategoryChildren()){
						# Call buildSortingStructure from the child class
						echo $childClass->buildSortingStructure('all', $branch->getId(), 1, $currentLevel+1);
					}
					# Otherwise build list of children
					else{
						echo $branch->buildListOfChildren($currentLevel+1);
					}

				}
				else if($max_levels > 1){

					# Determine if children are also Categories
					if($branch->hasCategoryChildren()){
						# Call buildSortingStructure from the child class
						echo $childClass->buildSortingStructure($max_levels-1, $branch->getId(), 1, $currentLevel+1);		
					}
					# Otherwise build list of children
					else{
						echo $branch->buildListOfChildren($currentLevel+1);			
					}
				}
				
				echo '</li>';
			}
			if(!is_numeric($single)){
				echo '</ol>';
			}
			
			$list_items = ob_get_clean();	
						
			if($root_parent_id == 0){
				ob_start();
				echo $list_items; 
                $tree = ob_get_clean();		
			}
			else{
				$tree = $list_items;	
			}
		}
		# There are no categories
		else{
			if(!is_numeric($single)){
			ob_start();
			?>
			<div class="no_records"><i class="fa fa-times-circle"></i>No records available.</div>
			<ol class="<?php echo ($currentLevel==1 ? 'sortable ' : ''); ?>"></ol>
			<?php
				echo ob_get_clean();
			}
		}
				
		return $tree;
	}
	
	public function hasChildren(){
		# Get children of the current object
		$children = $this->getChildren();
		
		if(sizeof($children)){
			return true;
		}
		return false;
	}
	
	public function hasCategoryChildren(){
		# Create instance of child class
		$childClassName = $this->_childClass;
		$childClass = new $childClassName();

		# Determine if children are also Categories
		return is_subclass_of($childClass, 'Category');
	}
	
	public function buildListOfChildren($currentLevel){
		# Get children of the current object
		$children = $this->getChildren();
		
		//print_r($children);
		
		# Build list of children
		$list = '';
		
		if(sizeof($children)){
			foreach($children as $child){
				$className = get_class($child);
				$list .= $this->buildChild($child, $currentLevel, $className);
			}

			$list = '<ol>'.$list.'</ol>';
		}
		else{
			$list = '<ol></ol>';
		}

		return $list;
	}	
	
	public function buildChild($child, $currentLevel, $className){
		return '<li id="menuItem_'.$child->getCategory().'>'.$child->getId().'" class="lvl_'.$currentLevel.' '.$className.'">'.$child->toHtml('default_list').'</li>';
	}
}

?>