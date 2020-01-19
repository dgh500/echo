<SCRIPT language="JavaScript">
<!--
if (document.images)
{
  pic1= new Image(550,300); 
  pic1.src="http://localhost/deepblue08/dive/images/bannerAbyss.gif"; 

  pic2= new Image(550,300); 
  pic2.src="http://localhost/deepblue08/dive/images/bannerDragon.gif"; 

  pic3= new Image(550,300); 
  pic3.src="http://localhost/deepblue08/dive/images/bannerRaptors.gif"; 

  pic4= new Image(550,300); 
  pic4.src="http://localhost/deepblue08/dive/images/bannerMK25.gif"; 

  pic5= new Image(550,300); 
  pic5.src="http://localhost/deepblue08/dive/images/bannerXTX200.gif"; 

  pic6= new Image(550,300); 
  pic6.src="http://localhost/deepblue08/dive/images/bannerSpectra.gif"; 

  pic7= new Image(550,300); 
  pic7.src="http://localhost/deepblue08/dive/images/bannerD4.gif"; 

}
//-->
</SCRIPT>


$(document).ready(function(){

	$('#gallery').galleryView({
		panel_width: 550,
		panel_height: 300,
		frame_width: 110,
		frame_height: 60,
		transition_speed: 350,		// Time to complete a transition
		easing: 'easeInOutQuad',
		transition_interval: 2500,	// Time Between Trainsitions
		nav_theme: 'light'
	});
	
	
});
