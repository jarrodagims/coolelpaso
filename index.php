<?php
//ini_set('display_errors','1');
//ini_set("error_reporting", E_ALL);
include_once('admin/includes/library.php');
ob_start();
?>
<div class="row">
    <div class="col-md-6">
        <h2 class="homepage-intro-title" data-aos="fade-right"><span>We Are</span><br />BARRETT AIRWORKS</h2>
    </div>
    <div class="col-md-6">
        <img src="images/image1.jpg" alt="Image 1" class="image-one" data-aos="fade-left" />
    </div>
    <div class="col-md-4">
        <p class="homepage-intro-one" data-aos="fade-left">Barrett Airworks is licensed and qualified with HVAC in
            installing, converting or replacing your home's air conditioning systems. Plus our technicians can install,
            repair or replace any brand and we stand behind our work 100%.</p>
    </div>
    <div class="col-md-5">
        <img src="images/image2.jpg" alt="Image 2" class="image-two" data-aos="fade-right" />
    </div>
</div>
<div class="row" class="intro-two-row">
    <div class="col-md-4">
        <p class="homepage-intro-since" data-aos="fade-right">since</p>
        <p class="homepage-intro-since-year" data-aos="fade-right">1952</p>
    </div>
    <div class="col-md-8">
        <p class="homepage-intro-two" data-aos="fade-left">Serving El Paso, Las Cruces, Van Horn, Anthony with reliable
            24-hour service and technicians who receive hands-on continuing education to stay current on all the latest
            technology advancements in heating and cooling systems. </p>

    </div>
</div>
<div class="row">
    <div class="col-md-12 homepage-testimonial-container" data-aos="fade">
        <div class="homepage-testimonial">
            <?php $testimonial = new Testimonial(); echo $testimonial->toHtml(); ?>
        </div>
    </div>
</div>

<?php 
$GLOBALS['CONTENT'] = ob_get_clean();
//
$GLOBALS['PAGE_SECTION'] = 'index';
$GLOBALS['PAGE_TITLE'] = '';
$GLOBALS['BANNER_IMAGE'] = '';
//
require_once('template.php');