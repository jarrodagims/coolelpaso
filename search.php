<?php

# Debugging
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);

# Include Library
include_once('admin/includes/library.php');

# Get Keywords
extract($_GET);
$keywords = addslashes(urldecode($keywords));

ob_start();

//print_r($_GET);
//echo $keywords;

if(!strlen(trim($keywords))){
	header("Location: /");
	exit;
}

$searchArray = array(
	'HtmlPage' => array(
		'table'  => 'html_pages',
		'fields' => '`name`,`html`',
		'title'  => 'Site Pages'
	)
);

$results = array();

$model = new Model();

foreach($searchArray as $class=>$options){
	$sql_select = "MATCH(".$options['fields'].") AGAINST ('*$keywords*') AS `relevance`";
	$sql_filter = "MATCH(".$options['fields'].") AGAINST ('*$keywords*' IN BOOLEAN MODE)";
	if($options['table']=='html_pages'){$sql_filter .= " AND `active` ='1' AND `searchable` ='1' ";}
	$sql = "SELECT `id`,$sql_select FROM `".$options['table']."` WHERE $sql_filter ORDER BY `relevance` DESC";
	//echo $sql;
	//echo "<br />";

	$search_results = $model->query($sql);
	//print_r($search_results);

	if(count($search_results)){
		?>

		<div class="row search-results">
			<?php
			if(strlen(trim($options['title']))){ ?>
				<h2 class="border-bottom purple col-sm-12">
					<?php echo $options['title']; ?>
				</h2>
			<?php
			}

			foreach($search_results as $row){
				$item = new $class($row['id']);

				echo $item->toHtml('search-result');
			}
			?>
		</div>

		<?php
	}
}

$GLOBALS['CONTENT'] = ob_get_clean();

if(!strlen(trim($GLOBALS['CONTENT']))){
	ob_start(); ?>

	<strong>No Results Found</strong>

	<?php
	$GLOBALS['CONTENT'] = ob_get_clean();
}


$GLOBALS['PAGE_SECTION'] = 'search';
$GLOBALS['PAGE_TITLE'] = 'Search';
$GLOBALS['PAGE_BANNER_SRC'] = '/images/banner-default.jpg';
$GLOBALS['SEO_TITLE'] = $GLOBALS['PAGE_TITLE'];

include_once('template.php');
?>
