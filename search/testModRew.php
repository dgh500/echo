
<?php
var_dump($_GET);
include_once('../autoload.php');

echo '<iframe src="https://www.echosupplements.com/search/frameOne.html" height="200" width="200" name="iframeOne">x</iframe>';

if(isset($_GET['id'])) {
	echo '<iframe src="https://www.echosupplements.com/search/frameTwo.php?orderId='.$_GET['id'].'" height="200" width="200" name="iframeTwo">x</iframe>';
} else {
	echo '<iframe src="https://www.echosupplements.com/search/frameTwo.php" height="200" width="200" name="iframeTwo">x</iframe>';
}

?>
