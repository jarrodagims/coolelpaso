<?php
if(!stristr($_SERVER['HTTP_HOST'],'stantonstreethosting.com')){
	if(!strstr($_SERVER['HTTP_HOST'], 'www.') || !stristr($_SERVER['HTTP_HOST'],$GLOBALS['LIVE_DOMAIN'])){
		header("Location: https://".$GLOBALS['LIVE_DOMAIN'].$_SERVER['REQUEST_URI'],true,301);
		exit;
	}
}
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-127800522-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-127800522-1');
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/images/favicon.ico">
    <meta name="msvalidate.01" content="2F2A0AC453AF23BED29DF0B98007C11D" />


		<title><?php
		echo $GLOBALS['SEO_TITLE_DESCRIPTION']?> | <?php echo $GLOBALS['SEO_NAME']; ?> | <?php echo $GLOBALS['SEO_TITLE']; ?></title>
		<?php if(strlen(trim($GLOBALS['SEO_KEYWORDS']))): ?>
		<meta name="keywords" content="<?php echo $GLOBALS['SEO_KEYWORDS']; ?>" />
		<? endif; ?>
		<?php if(strlen(trim($GLOBALS['SEO_DESCRIPTION']))): ?>
		<meta name="description" content="<?php echo $GLOBALS['SEO_DESCRIPTION']; ?>" />
		<? endif; ?>
    <!-- Bootstrap core CSS -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
	<!-- AOS Style -->
	<link href="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="/css/theme.css" rel="stylesheet">
	<!-- TypeKit -->
	<link rel="stylesheet" href="https://use.typekit.net/uwj4vhz.css">
	<script src="https://use.fontawesome.com/1bb9601ba3.js"></script> 
	<?php echo $GLOBALS['CSS']; ?>
	<style>
		.subpage-banner-area{background: url(<?php echo $GLOBALS['BANNER']; ?>) no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;}
	</style>
    <!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-TH8DNJX');</script>
    <!-- End Google Tag Manager -->
    <meta name="google-site-verification" content="JdHgWfJNxO1AJiPNOtDKPFa_dnUGUcdO9crpFNcSekI" />
    
    <!-- Default Statcounter code for Barrett Airworks
https://www.coolelpaso.com/ -->
<script type="text/javascript">
var sc_project=11853214; 
var sc_invisible=1; 
var sc_security="b2c42c62"; 
</script>
<script type="text/javascript"
src="https://www.statcounter.com/counter/counter.js"
async></script>
<noscript><div class="statcounter"><a title="free web stats"
href="http://statcounter.com/" target="_blank"><img
class="statcounter"
src="//c.statcounter.com/11853214/0/b2c42c62/1/" alt="free
web stats"></a></div></noscript>
<!-- End of Statcounter Code -->
    
    <script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "HVACBusiness",
  "name": "Barrett Airworks Service Co.",
  "image": "https://www.coolelpaso.com/images/logo.jpg",
  "@id": "",
  "url": "https://www.coolelpaso.com/",
  "telephone": "915-591-8457",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "3750 Durazno Ave",
    "addressLocality": "El Paso",
    "addressRegion": "TX",
    "postalCode": "79905",
    "addressCountry": "US"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 31.775764,
    "longitude": -106.44800199999997
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": [
      "Monday",
      "Tuesday",
      "Wednesday",
      "Thursday",
      "Friday"
    ],
    "opens": "07:30",
    "closes": "14:30"
  },
  "sameAs": [
    "https://www.facebook.com/Barrett-Airworks-Service-Co-257052545000799/",
    "https://twitter.com/BarrettAirworks"
  ]
}
</script>
    
  </head>
  <body>
  	<!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TH8DNJX"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php $excludePages = array('contact-us','not-found');
					$navigation = new HtmlPage(); ?>
	<header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<?php echo $navigation->navMenuMobileAction($excludePages); ?>
          		</ul>
			</div>
		</nav>
	</header>
	<div class="header-area">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<a href="/" title="Barrett Airworks"><img src="/images/logo.jpg" alt="Barrett Airworks" class="logo" /></a>
				</div>
				<div class="col-md-2 payzer">
					 
                    <img style="outline: none; margin: 0 auto; display: block;" alt=""
                         src="https://www.payzer.com/images/jpp-button-small.png"
                         usemap="#JPPSmallButtonMap" width="150" height="60">
                    <p></p>
                    <map id="JPPSmallButtonMap" name="JPPSmallButtonMap">
                        <area style="outline: none;" title="Make a Payment" alt="Make a Payment with Payzer" coords="62,30,134,40"
                              shape="rect"
                              href="https://www.payzer.com/index.php/Payment/ExternalMake/b/4213">
                        <area style="outline: none;" alt="Apply for Instant Financing with Payzer" title="Apply for Instant Financing"
                              coords="62,46,135,56" shape="rect"
                              href="https://www.payzer.com/index.php/Account/FinancingOptions/b/4213">
                    </map>
         
				</div>         
				<div class="col-lg-4">
					<p class="header-contact-desc aos-init aos-animate" data-aos="fade-right" style="margin-top: 10px;">For Your Heating, Cooling, and Plumbing Needs</p>
					<a href="#" class="header-contact-button aos-init aos-animate" data-aos="fade-up" data-toggle="modal" data-target="#contactModal" style="margin: 0 auto;">Get a Free Quote</a>
					<p class="header-contact-phone aos-init aos-animate" data-aos="fade-left"><a href="tel:9155918457" style="font-size: 18px;"><span>24 hours:</span> (915) 591-8457</a>
					<a href="https://www.facebook.com/Barrett-Airworks-Service-Co-257052545000799/" target="_blank"><img src="/images/facebook-logo.png" alt="Facebook"></a>
					<a href="https://twitter.com/BarrettAirworks" target="_blank"><img src="/images/ba-twitter.png" alt="Facebook"></a>
					<a href="https://www.linkedin.com/company/barrett-airworks-service-co/" target="_blank"><img src="/images/ba-linkedin.png" alt="Facebook"></a></p>
				</div>
			</div>
		</div>  
	</div>

    <main role="main">
		<div class="banner-area">
			<div class="container navigation-container">
				<div class="row">
					<div class="col-md-12">
						<ul class="main-navigation" data-aos="zoom-in-left">
							<?php echo $navigation->navMenuAction($excludePages); ?>
						</ul>
					</div>
				</div>
			</div>
		<?php if($GLOBALS['PAGE_SECTION'] == 'index'){ ?>
		  <div id="carousel" class="carousel slide carousel-fade" data-ride="carousel" data-interval="6000" >
			<ol class="carousel-indicators">
				<li data-target="#carousel" data-slide-to="0" class="active"></li>
				<li data-target="#carousel" data-slide-to="1"></li>
				<li data-target="#carousel" data-slide-to="2"></li>
			</ol>
			<div class="carousel-inner" role="listbox">
				<?php $homepageBanner = new HomepageBanner(); echo $homepageBanner->toHtml(); ?>
			</div>
			<!-- /.carousel-inner -->
			<a class="carousel-control-prev" href="#carousel" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Previous</span>
			</a>
			<a class="carousel-control-next" href="#carousel" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">Next</span>
			</a>
		</div>
		<!-- /.carousel -->
		</div>
		<div class="homepage-center-area">
			<div class="container">
				<div class="row">
					<?php $promo = new Promo(); echo $promo->toHtml(); ?>

				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<h1 class="homepage-center-area-title">Our Services</h1>
					</div>
				</div>
			</div>
			<div class="container-fluid"></div>
				<div class="row" style="margin:20px">
					<div class="col-md-1 text-center">
						<a href="#" title="Heating"><img src="/images/icon-heating.png" alt="Heating" class="homepage-center-area-image" data-aos="zoom-in-right" /></a>
					</div>
					<div class="col-md-2 text-center">
						<h1 class="homepage-center-area-service" data-aos="fade"><a href="/services/heating" title="Heating">Heating</a></h1>
					</div>
					<div class="col-md-1 text-center">
						<a href="/services/heating" title="Heating"><img src="/images/icon-cooling.png" alt="Cooling" class="homepage-center-area-image" data-aos="zoom-in-down" /></a>
					</div>
					<div class="col-md-2 text-center">
						<h1 class="homepage-center-area-service" data-aos="fade"><a href="/services/cooling" title="Cooling">Cooling</a></h1>
					</div>
					<div class="col-md-1 text-center">
						<a href="/services/cooling" title="Heating"><img src="/images/icon-plumbing.png" alt="Plumbing" class="homepage-center-area-image" data-aos="zoom-in-down" /></a>
					</div>
					<div class="col-md-2 text-center">
						<h1 class="homepage-center-area-service" data-aos="fade"><a href="/services/plumbing" title="Plumbing">Plumbing</a></h1>
					</div>
					<div class="col-md-1 text-center">
						<a href="/services/plumbing" title="Heating"><img src="/images/icon-commercial.png" alt="Commercial Servcies" class="homepage-center-area-image" data-aos="zoom-in-down" /></a>
					</div>
					<div class="col-md-2 text-center">
						<h1 class="homepage-center-area-service" data-aos="fade"><a href="/services/commercial" title="Commercial Services" >Commercial Services</a></h1>
					</div>
				</div>
			</div>
		<div class="clearfix"></div>
		</div>
	  <?php } else { ?>
	  <div class="subpage-banner-area">
		  <div class="subpage-banner-area-container">
				<div class="container">
					<div class="row">
						<div class="col-md-12 text-center" style="z-index:0;">
							<h1 class="subpage-banner-title" data-aos="fade-down" ><?php echo $GLOBALS['PAGE_TITLE']; ?></h1>
						</div>
					</div>  
				</div>
		  </div>
	  </div>
	  <?php } ?>
		<div class="content-area">
	  		<div class="container">
			<?php if($GLOBALS['PAGE_SECTION'] == 'index'){ ?>
				<?php echo $GLOBALS['CONTENT']; ?>
			<?php }else{ ?>
				<div class="row">
					<div class="col-md-<?php echo (!strlen(trim($GLOBALS['SIDE_NAVIGATION'])) ? '12' : '9');?>">
						<div id="breadcrumb">
							<?php echo $GLOBALS['BREADCRUMB']; ?>
						</div>
						<?php echo $GLOBALS['CONTENT']; ?>
					</div>
					<?php if(strlen(trim($GLOBALS['SIDE_NAVIGATION']))){ ?>
					<div class="side-content col-sm-3">
						<?php echo $GLOBALS['SIDE_NAVIGATION']; ?>
					</div>
				</div>
					<?php } ?>
			<?php } ?>
			</div>
	 	 </div>
    </main>
  <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13567.172366750798!2d-106.448011!3d31.7761288!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x778f2a2f98d0df34!2sBarrett+%2F+Airworks+Service+Co!5e0!3m2!1sen!2sus!4v1541605285425" width="100%" height="250" frameborder="0" style="border:0" allowfullscreen></iframe>
	<div class="footer">
		<div class="container">
			<div class="row">
				<div class="col-md-2 col-4"><img src="/images/footer-logo.png" alt="Barrett Airworks" class="footer-logo" /></div>
				<div class="col-md-3 col-8">
					<p class="footer-contact">Barrett Airworks<br />3750 Durazno Ave.<br />El Paso, TX 79905<br /><a href="tel:915-591-8457" title="915-591-8457">915-591-8457</a><br /><a href="https://www.facebook.com/Barrett-Airworks-Service-Co-257052545000799/" target="_blank"><img src="/images/facebook-logo.png" alt="Facebook" /></a>
					<a href="https://twitter.com/BarrettAirworks" target="_blank"><img src="/images/ba-twitter.png" alt="Facebook"></a>
					<a href="https://www.linkedin.com/company/barrett-airworks-service-co/" target="_blank"><img src="/images/ba-linkedin.png" alt="Facebook"></a>
					</p>
				</div>
				<div class="col-md-4">
					<img class="footer-logos" src="/images/footer-logos.png" alt="Barrett Airworks" />
				</div>
				<div class="col-md-3"><p class="credit">designed by <a href="https://www.stantonstreet.com" target="_blank" title="stantonstreet">stantonstreet</a>.</p>
				<a href="https://www.carrier.com/residential/en/us/find-a-dealer/schedule-now/?zipcode=79905&dealerId=9002293&country=USA&utm_content=dealersite" target="_blank" class="footer-schedule-button" data-aos="fade-up">Schedule Now</a></div>
			</div>
		</div>
	</div>
	<a href="#" title="Contact Us / Request a FREE Quote" class="contact-button" data-aos="fade-up" data-toggle="modal" data-target="#contactModal">Contact Us /<br />
Request a FREE Quote</a>

	
<div class="modal fade" id="contactModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Request a Quote/Repair/Information</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?php $form = new Form('contact-us'); echo $form->toHtml(); ?>
      </div>
    </div>
  </div>
</div>
	
	
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="/js/popper.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="js/holder.min.js"></script>
	<script src="https://cdn.rawgit.com/michalsnik/aos/2.1.1/dist/aos.js"></script>
	   <script>
			AOS.init({
				easing: 'ease-in-sine',
				duration: 1000
			});
		</script>


  <script type="text/javascript" src="/js/init.js"></script>
  <?php echo $GLOBALS['JAVASCRIPT']; ?>
  </body>
</html>
