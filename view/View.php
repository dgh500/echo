<?php

//! Base view class
class View {

	//! The code to be output
	var $mPage = '';

	//! Loads some helpers and variables for all children to share (NB Must call parent::__construct() if the child constructor over-rides it)
	/*!
	 * @param $title - The <title> tag
	 * @param $cssIncludes - Array[string] - Any extra CSS files
	 *  Default: screen.css, header.css, footer.css, home.css, productSearch.css, accountNavigation.css, otherSites.css, footer.css, shoppingBag.css, shopByDept.css
	 * @param $jsIncludes - Array[string] - Any extra JS files
	 *  Default: jquery.js, shopByBrand.js
	 * @param $metaDescription 	String		- The META description to be used for the page
	 * @param $secureJs - Boolean - Whether to load the javascript securely
	 */
	function __construct($title=false,$cssIncludes=false,$jsIncludes=false,$metaDescription=false,$secureJs=false,$canonical=false) {
		$this->mRegistry = Registry::getInstance();

		// Load some directories
		$this->mBaseDir 			= $this->mRegistry->baseDir;
		$this->mRootDir 			= $this->mRegistry->rootDir;
		$this->mFormHandlersDir 	= $this->mRegistry->formHandlersDir;
		$this->mViewDir 			= $this->mRegistry->viewDir;
		$this->mManufacturerImageDir= $this->mRegistry->manufacturerImageDir;
		$this->mTagImageDir 		= $this->mRegistry->tagImageDir;
		$this->mSecureBaseDir 		= $this->mRegistry->secureBaseDir;

		// Load some helpers
		$this->mValidationHelper 	= new ValidationHelper;
		$this->mPresentationHelper 	= new PresentationHelper;
		$this->mMoneyHelper 		= new MoneyHelper;
		$this->mTimeHelper 			= new TimeHelper;
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper;
		$this->mSessionHelper		= new SessionHelper;

		// Load the Doctype
		if($title) {
			$this->mPage .= $this->mPublicLayoutHelper->Doctype();
			$this->mPage .= $this->mPublicLayoutHelper->OpenHtml();
			// Open HEAD
			$this->mPage .= $this->mPublicLayoutHelper->OpenHead();
			$this->mPage .= $this->mPublicLayoutHelper->Charset();
			if(!$metaDescription) {
				$this->mPage .= $this->mPublicLayoutHelper->MetaDescription($this->mRegistry->metaDescription);
			} else {
				$this->mPage .= $this->mPublicLayoutHelper->MetaDescription($metaDescription);
			}
			$this->mPage .= $this->mPublicLayoutHelper->MetaKeywords($this->mRegistry->metaKeywords);
			$this->mPage .= $this->mPublicLayoutHelper->Title($title);
			// Load the Javascript Autoload if it isn't disabled
			if(!$this->mRegistry->disableJavascriptAutoload && strpos($this->mPage,'</script>') === FALSE) {
				if(empty($_SERVER['HTTPS']) && !$secureJs) {
					$this->IncludeJavascript('autoload.js');
				} else {
					$this->IncludeJavascript('autoload.js',true,true);
				}
			}

			// Get some default JS loaded
			if(!$secureJs) {
				$this->IncludeDefaultJs();
			}
			// If there are any page-specific JS file, include them
			if($jsIncludes) {
				foreach($jsIncludes as $jsFile) {
					$this->IncludeJs($jsFile,true,$secureJs);
				}
			}

			// Include default CSS
			$this->IncludeDefaultCss();
			// If there are any page-specific CSS files, include them
			if($cssIncludes) {
				foreach($cssIncludes as $cssFile) {
					$this->IncludeCss($cssFile);
				}
			}

			// If canonical URL specified, then include it
			if($canonical) {
				$this->mPage .= '<link rel="canonical" href="'.$canonical.'" />';
			}

			// Close HEAD
			$this->mPage .= $this->mPublicLayoutHelper->CloseHead();
		}
	} // End __construct()

	//! Includes the 'usual' javascript needed on all pages
	function IncludeDefaultJs() {
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('jquery.corner.js');
		$this->IncludeJs('jquery.suggest.js');
		$this->IncludeJs('common.js');
	}

	//! Includes the 'usual' CSS needed on all pages
	function IncludeDefaultCss() {
		$this->IncludeCss('echoStyles.css.php');
		$this->mPage .= '<!--[if IE 7]><link rel="stylesheet" type="text/css" href="'.$this->mBaseDir.'/css/IE7styles.css" /><![endif]-->';
	}

	//! Loads the header part of the page for a given catalogue
	function LoadHeaderSection($catalogue,$checkout=false) {
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeader();
			$this->mPage .= $this->mPublicLayoutHelper->OpenHeaderLeft();
			$this->mPage .= $this->mPublicLayoutHelper->HeaderLogo($catalogue->GetUrl(),$catalogue->GetDisplayName(),$checkout);
			$this->mPage .= $this->mPublicLayoutHelper->CloseHeaderLeft ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeaderRight ();
		if(!$checkout) {
			$this->LoadAccountNavigation();
			$this->LoadProductSearch();
		} else {
			$this->LoadAccountNavigation();
			#$this->LoadProductSearch();
			/*$this->mPage .= '
			<script type="text/javascript">
				var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
				document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
			</script>
			<script type="text/javascript">
			try {
			    var pageTracker = _gat._getTracker("'.$this->mRegistry->GoogleAnalyticsTrackerKey.'");
			    pageTracker._trackPageview();
			  } catch(err) {
			  }
			</script>
			<script src="http://checkout.google.com/files/digital/ga_post.js" type="text/javascript"></script>
			';*/
		}
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeaderRight ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeader ();
	} // End LoadHeaderSection()

	//! Loads AccountNavigationView
	function LoadAccountNavigation() {
		$accountNavigation = new AccountNavigationView();
		$this->mPage .= $accountNavigation->LoadDefault($this->mSessionHelper->GetBasket ());
	}

	//! Loads ProductSearchView
	function LoadProductSearch() {
		$productSearchView = new ProductSearchView;
		$this->mPage .= $productSearchView->LoadDefault();
	}

	//! Loads the horizontal navigation bar
	function LoadNavigation() {
		$navView = new NavigationView;
		$this->mPage .= $navView->LoadDefault();
	}

	//! Loads the left column, including ShopByDepartmentView
	function LoadLeftColumn() {
		$shopByDeptView = new ShopByDepartmentView ( );
		$newsLetterView = new NewsletterView;
		$this->mPage .= $this->mPublicLayoutHelper->OpenLeftCol ();
		$this->mPage .= $shopByDeptView->LoadDefault ( $this->mCatalogue );
		$this->mPage .= $newsLetterView->LoadDefault();
		$this->LoadSocialNetworking();
		$this->LoadFeaturedIn();
		$this->mPage .= $this->mPublicLayoutHelper->CloseLeftCol ();
	}

	function LoadFeaturedIn() {
		$this->mPage .= <<<EOT
			<img src="http://www.echosupplements.com/images/magMuscleAndFitness.jpg" width="156" height="235" style="margin-left: 5px;" />
			<img src="http://www.echosupplements.com/images/magMensFitness.jpg" width="156" height="220" style="margin-left: 5px;" />
			<img src="http://www.echosupplements.com/images/magWomensFitness.jpg" width="156" height="220" style="margin-left: 5px;" />
			<img src="http://www.echosupplements.com/images/magFlex.jpg" width="156" height="220" style="margin-left: 5px;" />
			<img src="http://www.echosupplements.com/images/magAttitude.jpg" width="156" height="220" style="margin-left: 5px;" />
EOT;
	}

	function LoadSocialNetworking() {
		$this->mPage .= <<<EOT
<!-- Trustpilot Widget Script -->
<div class="tpc_widget" id="tp_widget">
	<a href="http://www.trustpilot.co.uk/review/www.echosupplements.com" id="tp_widget_link">Reviews of  echosupplements.com</a>
	<div class="tpc_bg">&nbsp;</div>
	<div class="tpc_gradient">&nbsp;</div>
	<div class="tpc_top">
		<div class="tpc_top_left"></div>
		<div class="tpc_top_center"><div class="tpc_top_img">&nbsp;</div></div>
		<div class="tpc_top_right"></div>
	</div>
	<div class="tpc_rating">
		<div class="tpc_rating_speaker"></div>
		<div class="tpc_rating_bubble">
			<div class="tpc_rating_startext"></div>
			<div class="tpc_rating_star"></div>
			<div class="tpc_rating_rating"></div>
		</div>
		<div class="tpc_rating_count"></div>
		<div class="tpc_rating_counttext"></div>
	</div>
	<ul class="tpc_review" id="tp_review">
		<li class="tpc_review_review" id="tp_review_id">
			<div class="tpc_review_hr">&nbsp;</div>
			<div class="tpc_review_stars">[STARS]</div>
			<p class="tpc_review_time">[TIME]</p><br />
			<a href="[LINK]" class="tpc_review_title">[TITLE]</a><br />
			<a href="[LINK]" class="tpc_review_message">[MESSAGE]</a><br />
			<div class="tpc_review_spacer">&nbsp;</div>
			<p class="tpc_review_author">[AUTHOR]</p><br />
		</li>
	</ul>
	<div class="tpc_bottom">
		<div class="tpc_bottom_hr">&nbsp;</div>
		<div class="tpc_bottom_img">&nbsp;</div>
	</div>
</div>
<script type="text/javascript">
 var tpLang = "en-GB";
 var tpSpeak = "man";
 var tpJsHost = (("https:" == document.location.protocol) ? "https://ssl" : "http://trustbox");
 document.write(unescape("%3Cscript defer='defer' src='"+tpJsHost+".trustpilot.com/w/echosupplements.com.js' type='text/javascript'%3E%3C/script%3E"));
 document.write(unescape("%3Clink href='"+tpJsHost+".trustpilot.com/widget.css' rel='stylesheet' type='text/css'%3E%3C/link%3E"));
</script>
<style type="text/css">
.tpc_widget {
 display: none;
 position: relative;
 left: 6px;
 width: 160px;
 height: 310px;
 margin-bottom: 5px;
}
#tp_widget .tpc_bg {
 background-color: #47EB42;
}
#tp_widget .tpc_bottom {
 background-color: #47EB42;
}
</style>
<!-- /Trustpilot Widget Script -->
	<a href="http://www.twitter.com/EchoSupplements">
		<img src="{$this->mBaseDir}/images/follow_us.png" width="160" height="27" alt="Follow EchoSupplements on Twitter" style="margin-left: 5px" />
	</a>
	<a href="http://www.facebook.com/pages/Echo-Supplements/133559329990582">
		<img src="{$this->mBaseDir}/images/facebook.gif" style="border: 0px; margin-left: 15px;" width="144" height="44" />
	</a>
<!--	<div style="text-align: center;">
		<iframe src="http://www.facebook.com/plugins/like.php?app_id=156304131110243&amp;href=www.echosupplements.com&amp;send=false&amp;layout=standard&amp;width=50&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:35px;" allowTransparency="true"></iframe>
		<g:plusone count="false"></g:plusone>
	</div>-->
<!--	<a href="http://www.echosupplements.com/content/22/echo-supplements-testimonials">
		<img src="{$this->mBaseDir}/images/echoTestimonials.gif" style="border: 0px; margin-left: 15px;" width="144" height="39" />
	</a>
	<a href="http://www.trustpilot.co.uk/review/www.echosupplements.com">
		<img src="{$this->mBaseDir}/images/buttonTrustpilot.png" style="border: 0px; margin-left: 5px;" width="156" height="183" />
	</a> -->
EOT;
	}

	//! This function includes a javascript file, if default is omitted, then it is included from the default JS directory - if set to false then the $fileName is treated as a path from the root directory. If you want to include REMOTE javascript then use $this->IncludeRemoteJavascript()
	function IncludeJavascript($fileName, $default = true, $secure = false) {
		if ($secure) {
			$this->mBaseDir = $this->mSecureBaseDir;
		}
		if ($default) {
			$this->mPage .= '
<script type="text/javascript" src="'.$this->mBaseDir.'/js/'.$fileName.'"></script>';
		} else {
			$this->mPage .= '
<script type="text/javascript" src="'.$this->mBaseDir.'/'.$fileName.'"></script>';
		}
	}

	//! Include remote javascript
	function IncludeRemoteJavascript($path) {
		$this->mPage .= '
<script type="text/javascript" src="'.$path.'"></script>
';
	}

	//! Synonym of IncludeJavascript
	function IncludeJs($fileName, $default = true, $secure = false) {
		$this->IncludeJavascript ( $fileName, $default, $secure );
	}

	//! This function includes a CSS file, if default is omitted, then it is included from the default css directory - all of the parameters after the fileName are optional
	/*!
	 * @param $fileName	- String, the filename of the CSS file
	 * @param $default	- Whether or not the CSS file is in the root directory (Def=true) if not then the fileName is used as the path from the base directory
	 * @param $media	- What media the CSS file is for (Eg. screen/print) (Def=false/screen)
	 * @param $secure	- Whether the CSS file must be secure (Eg. for checkout pages) (Def=false)
	 * @param $linkIn	- Whether the function should just add the CSS to the current mPage variable that is always inherited, defaults to true, otherwise will return the code for usage
	 * @return String/Void dependant on the $linkIn parameter
	 */
	function IncludeCss($fileName, $default = true, $media = false, $secure = false, $linkIn = true) {
		if ($secure) {
			$this->mBaseDir = $this->mSecureBaseDir;
		}
		if ($default) {
			if ($media) {
				$htmlCode = '
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/css/' . $fileName . '" media="' . $media . '" />';
			} else {
				$htmlCode = '
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/css/' . $fileName . '" />';
			}
		} else {
			if ($media) {
				$htmlCode = '
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/' . $fileName . '" media="' . $media . '" />';
			} else {
				$htmlCode = '
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/' . $fileName . '" />';
			} // End $media
		} // End $default
		if ($linkIn) {
			$this->mPage .= $htmlCode;
		} else {
			return $htmlCode;
		}
	} // End IncludeCss

	//! Really an abstract function; each child is expected to use whatever parameters etc. are appropriate. Once this is done the $mPage variable should be returned
	function LoadDefault() {
		return $this->mPage;
	}

}

?>