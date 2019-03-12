<?php
$htmlPage = new HtmlPage('products');
ob_start();
echo $htmlPage->getHtml();
?>
<!--
<section id="tabs">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 ">
				<nav>
					<div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
						<a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true" data-aos="fade-right">Hot Water Heaters</a>
						<a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false" data-aos="fade-right">Air Conditioners</a>
						<a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false" data-aos="fade-right">Furnaces</a>
						<a class="nav-item nav-link" id="nav-about-tab" data-toggle="tab" href="#nav-about" role="tab" aria-controls="nav-about" aria-selected="false" data-aos="fade-right">Thermostats</a>
					</div>
				</nav>
				<div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
					<div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" data-aos="fade-up">
						Et et consectetur ipsum labore excepteur est proident excepteur ad velit occaecat qui minim occaecat veniam. Fugiat veniam incididunt anim aliqua enim pariatur veniam sunt est aute sit dolor anim. Velit non irure adipisicing aliqua ullamco irure incididunt irure non esse consectetur nostrud minim non minim occaecat. Amet duis do nisi duis veniam non est eiusmod tempor incididunt tempor dolor ipsum in qui sit. Exercitation mollit sit culpa nisi culpa non adipisicing reprehenderit do dolore. Duis reprehenderit occaecat anim ullamco ad duis occaecat ex.
					</div>
					<div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" data-aos="fade-up">
						Et et consectetur ipsum labore excepteur est proident excepteur ad velit occaecat qui minim occaecat veniam. Fugiat veniam incididunt anim aliqua enim pariatur veniam sunt est aute sit dolor anim. Velit non irure adipisicing aliqua ullamco irure incididunt irure non esse consectetur nostrud minim non minim occaecat. Amet duis do nisi duis veniam non est eiusmod tempor incididunt tempor dolor ipsum in qui sit. Exercitation mollit sit culpa nisi culpa non adipisicing reprehenderit do dolore. Duis reprehenderit occaecat anim ullamco ad duis occaecat ex.
					</div>
					<div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" data-aos="fade-up">
						Et et consectetur ipsum labore excepteur est proident excepteur ad velit occaecat qui minim occaecat veniam. Fugiat veniam incididunt anim aliqua enim pariatur veniam sunt est aute sit dolor anim. Velit non irure adipisicing aliqua ullamco irure incididunt irure non esse consectetur nostrud minim non minim occaecat. Amet duis do nisi duis veniam non est eiusmod tempor incididunt tempor dolor ipsum in qui sit. Exercitation mollit sit culpa nisi culpa non adipisicing reprehenderit do dolore. Duis reprehenderit occaecat anim ullamco ad duis occaecat ex.
					</div>
					<div class="tab-pane fade" id="nav-about" role="tabpanel" aria-labelledby="nav-about-tab" data-aos="fade-up">
						Et et consectetur ipsum labore excepteur est proident excepteur ad velit occaecat qui minim occaecat veniam. Fugiat veniam incididunt anim aliqua enim pariatur veniam sunt est aute sit dolor anim. Velit non irure adipisicing aliqua ullamco irure incididunt irure non esse consectetur nostrud minim non minim occaecat. Amet duis do nisi duis veniam non est eiusmod tempor incididunt tempor dolor ipsum in qui sit. Exercitation mollit sit culpa nisi culpa non adipisicing reprehenderit do dolore. Duis reprehenderit occaecat anim ullamco ad duis occaecat ex.
					</div>
				</div>
			
			</div>
		</div>
	</div>
</section>-->
<div class="row">
	<div class="col-md-3"><a class="product-title mx-auto d-block" href="/products/air-conditioners"><img class="img-fluid" style="padding-top:7px; padding-left:45px; padding-right:45px;" src="/images/products-1.png" />Air Conditioners</a></div>
    <div class="col-md-3"><a class="product-title mx-auto d-block" href="/products/furnaces"><img style="padding-top:13px;" class="img-fluid mx-auto d-block" src="/images/products-2.gif" />Furnaces</a></div>
    <div class="col-md-3"><a class="product-title mx-auto d-block" href="/products/hot-water-heaters"><img style="padding-top:5px;" class="img-fluid mx-auto d-block" src="/images/products-3.png" />Hot Water Heaters</a></div>
	<div class="col-md-3"><a class="product-title mx-auto d-block" href="/products/thermostats"><img class="img-fluid mx-auto d-block" style="padding-left:35px; padding-right:35px;" src="/images/products-4.jpg" />Thermostats</a></div>
</div>
<?php $GLOBALS['CONTENT'] = ob_get_clean();
$GLOBALS['PAGE_SECTION'] = $htmlPage->getPermalink();
$GLOBALS['PAGE_TITLE'] = $htmlPage->getName();
$GLOBALS['BANNER'] = $htmlPage->buildImageSrc();
$GLOBALS['SIDE_NAVIGATION'] = '';
$GLOBALS['SEO_TITLE'] = $GLOBALS['PAGE_TITLE'];
if(!strlen(trim(($GLOBALS['SIDE_NAVIGATION'])))){
	$GLOBALS['FULL_PAGE'] = true;
}
include_once('template.php');
?>