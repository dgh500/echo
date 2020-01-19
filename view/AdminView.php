<?php

//! Base view class
class AdminView {

	//! The code to be output
	var $mPage = '';

	//! Loads some helpers and variables for all children to share (NB Must call parent::__construct() if the child constructor over-rides it)
	/*!
	 * @param $title - The <title> tag
	 * @param $cssIncludes - Array[string] - Any extra CSS files
	 *  Default: screen.css, header.css, footer.css, home.css, productSearch.css, accountNavigation.css, otherSites.css, footer.css, shoppingBag.css, shopByDept.css
	 * @param $jsIncludes - Array[string] - Any extra JS files
	 *  Default: jquery.js, shopByBrand.js
	 */
	 function __construct($title=false,$cssIncludes=false,$jsIncludes=false,$enableAdminCheck=true) {
		$this->mRegistry = Registry::getInstance();

		// Load some directories
		$this->mBaseDir 				= $this->mRegistry->baseDir;
		$this->mRootDir 				= $this->mRegistry->rootDir;
		$this->mSecureBaseDir  			= $this->mRegistry->secureBaseDir;
		$this->mFormHandlersDir 		= $this->mRegistry->formHandlersDir;
		$this->mViewDir 				= $this->mRegistry->viewDir;
		$this->mAdminDir 				= $this->mRegistry->adminDir;
		$this->mManufacturerImageDir  	= $this->mRegistry->manufacturerImageDir ;
		$this->mCompanyName				= $this->mRegistry->companyName;

		// Load some helpers
		$this->mValidationHelper 	= new ValidationHelper ( );
		$this->mPresentationHelper 	= new PresentationHelper ( );
		$this->mMoneyHelper 		= new MoneyHelper ( );
		$this->mTimeHelper 			= new TimeHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper;
		$this->mAdminHelper 		= new AdminHelper;

		if($enableAdminCheck && !$this->mAdminHelper->LoginCheck()) {
			die('This Page Is Restricted.');
		}

		// Load the Doctype
		if($title) {
			$this->mPage .= $this->mPublicLayoutHelper->Doctype();
			$this->mPage .= $this->mPublicLayoutHelper->OpenHtml();
			// Open HEAD
			$this->mPage .= $this->mPublicLayoutHelper->OpenHead();
			$this->mPage .= $this->mPublicLayoutHelper->Charset();
			$this->mPage .= $this->mPublicLayoutHelper->Title($title);
			// Load the Javascript Autoload if it isn't disabled
			if(!$this->mRegistry->disableJavascriptAutoload && strpos($this->mPage,'</script>') === FALSE) {
				if(empty($_SERVER['HTTPS'])) {
					$this->IncludeJavascript('autoload.js');
				} else {
					$this->IncludeJavascript('autoload.js',true,true);
				}
			}

			// Get some default JS loaded
			$this->IncludeDefaultJs();

			// If there are any page-specific JS file, include them
			if($jsIncludes) {
				foreach($jsIncludes as $jsFile) {
					$this->IncludeJs($jsFile);
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

			// Close HEAD
			$this->mPage .= $this->mPublicLayoutHelper->CloseHead(true);
		}
	} // End __construct()

	//! Includes the 'usual' javascript needed on all pages
	function IncludeDefaultJs() {
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('AdminTabsView.js');
	}

	//! Includes the 'usual' CSS needed on all pages
	function IncludeDefaultCss() {
#		$this->IncludeCss('css/base.css.php',false);
		$this->IncludeCss('admin.css.php');
		$this->IncludeCss('adminPrint.css.php',true,'print');
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
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/wombat7/css/' . $fileName . '" media="' . $media . '" />';
			} else {
				$htmlCode = '
<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/wombat7/css/' . $fileName . '" />';
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