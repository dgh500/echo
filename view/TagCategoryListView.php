<?php

class TagCategoryListView extends View {
	
	function __construct() {
		parent::__construct ();
	}
	
	function SortByParent($a, $b) {
		if ($a->GetParentCategory ()) {
			$aName = $a->GetParentCategory ()->GetDisplayName ();
		} else {
			$aName = $a->GetDisplayName ();
		}
		if ($b->GetParentCategory ()) {
			$bName = $b->GetParentCategory ()->GetDisplayName ();
		} else {
			$bName = $b->GetDisplayName ();
		}
		#echo 'aName: '.$aName.' | bName: '.$bName.' | strcmp: '.strcmp($aName,$bName).'<br />';
		return strcmp ( $aName, $bName );
	}
	
	function LoadDefault($tag) {
		$this->IncludeCss ( 'tag.css' );
		$this->IncludeCss ( 'Category.css.php' );
		$this->mTag = $tag;
		$this->mTagController = new TagController ( );
		$categories = $this->mTagController->GetAllCategoriesIn ( $this->mTag );
		usort ( $categories, array ($this, "SortByParent" ) );
		if (count ( $categories ) > 0) {
			$this->mPage .= '<div id="categoryListContainer">';
			$this->mPage .= '<ul id="categoryList">';
			foreach ( $categories as $category ) {
				$linkTo = $this->mBaseDir . '/';
				$linkTo .= 'tag/' . $this->mValidationHelper->MakeLinkSafe ( trim ( $this->mTag->GetDisplayName () ) ) . '/' . $this->mTag->GetTagId();
				$linkTo .= '/department/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId ();
				if ($category->GetParentCategory ()) {
					$name = $category->GetParentCategory ()->GetDisplayName ();
				} else {
					$name = $category->GetDisplayName ();
				}
				// this is fucking dumb - if its >13 chars just display the subcat name???
				$this->mPage .= '
						<li>
							<a href="' . $linkTo . '">
							' . $name . '
							</a>
						</li>';
			}
			$this->mPage .= '</ul>';
			$this->mPage .= '</div>';
		}
		return $this->mPage;
	} // End LoadDefault


} // End class


?>