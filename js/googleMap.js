function initialize() {
  if (GBrowserIsCompatible()) {
	var map = new GMap2(document.getElementById("map_canvas"));
	var marker = new GMarker(new GLatLng(51.52724977453233,-0.6388391554355621));	// Lower moves right
	map.setCenter(new GLatLng(51.52724977453233,-0.6388391554355621), 16);
	map.addOverlay(marker);
	map.addControl(new GLargeMapControl());
	map.addControl(new GMapTypeControl());
	map.addControl(new GScaleControl());
  }
}