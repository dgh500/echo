<?php

class NavigationView extends View {

	function LoadDefault() {
		$this->mPage .= <<<HTMLOUTPUT
		<div id="navigation">
			<ul>
				<li><a href="{$this->mBaseDir}">HOME</a></li>
				<li><a href="{$this->mBaseDir}/department/clearance/24"><u>SALE</u></a></li>
				<li><a href="http://www.echosupplements.com/content/26/delivery">DELIVERY</a></li>
				<li><a href="http://www.echosupplements.com/content/10/about-us">ABOUT US</a></li>
				<li><a href="{$this->mBaseDir}/contact">CONTACT US</a></li>
				<li><a href="{$this->mBaseDir}/advice">ADVICE</a></li>
				<li><a href="{$this->mBaseDir}/blog">BLOG</a></li>
				<li><a href="{$this->mBaseDir}/basket" style="color: #f0ff00">CHECKOUT NOW</a></li>
			</ul>
		</div><!-- End navigation -->
HTMLOUTPUT;
		return $this->mPage;
	}
}

?>