<?php
header('Content-Type: text/javascript');
require('../autoload.php');
$registry = Registry::getInstance();
$gallery = new GalleryModel($registry->galleryId);
?>

$(document).ready(function(){

	$('#gallery').galleryView({
		panel_width: <?php echo $gallery->GetPanelWidth(); ?>,
		panel_height: <?php echo $gallery->GetPanelHeight(); ?>,
		frame_width: <?php echo $gallery->GetFrameWidth(); ?>,
		frame_height: <?php echo $gallery->GetFrameHeight(); ?>,
		transition_speed: <?php echo $gallery->GetTransitionSpeed(); ?>,		// Time to complete a transition
		easing: 'easeInOutQuad',
		transition_interval: <?php echo $gallery->GetTransitionInterval(); ?>,	// Time Between Trainsitions
		nav_theme: '<?php echo $gallery->GetNavTheme(); ?>'
	});
	
	
});
