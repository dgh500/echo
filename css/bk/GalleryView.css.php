<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* GALLERY LIST */
/* IMPORTANT - Change '#photos' to the ID of your gallery list to prevent a flash of unstyled content */
#work { visibility: hidden; }

/* GALLERY CONTAINER */
.gallery { background: #000; border: 1px solid #000; padding: 0px;}

/* LOADING BOX */
.loader { background: url(loader.gif) center center no-repeat #ddd; }

/* GALLERY PANELS */
.panel { border: 0px !important; }

/* DEFINE HEIGHT OF PANEL OVERLAY */
/* NOTE - It is best to define padding here as well so overlay and background retain identical dimensions */
.panel .panel-overlay,
.panel .overlay-background { 
	height: 60px; 
    padding: 5px; 
    height: 50px; 
    font-family: Arial, Helvetica, sans-serif; 
    font-size: 10pt !important; 
    font-weight: normal; 
    text-align: left;
}

/* PANEL OVERLAY BACKGROUND */
.panel .overlay-background { background: #000; }

/* PANEL OVERLAY CONTENT */
.panel .panel-overlay { color: white; font-size: 0.7em; }
.panel .panel-overlay a { color: white; text-decoration: underline; font-weight: bold; }

/* FILMSTRIP */
/* 'margin' will define top/bottom margin in completed gallery */
.filmstrip { margin: 5px; }

/* FILMSTRIP FRAMES (contains both images and captions) */
.frame {}

/* WRAPPER FOR FILMSTRIP IMAGES */
.frame .img_wrap { border: 1px solid #aaa; }

/* WRAPPER FOR CURRENT FILMSTRIP IMAGE */
.frame.current .img_wrap { border: 1px solid #FFF; }

/* FRAME IMAGES */
.frame img { border: none; }

/* FRAME CAPTION */
.frame .caption { font-size: 11px; text-align: center; color: #888; }

/* CURRENT FRAME CAPTION */
.frame.current .caption { color: #000; }

/* POINTER FOR CURRENT FRAME */
.pointer {
	border-color: #FFF;
}

/* TRANSPARENT BORDER FIX FOR IE6 */
/* NOTE - DO NOT CHANGE THIS RULE */
*html .pointer {
	filter: chroma(color=pink);
}