// JavaScript Document

if(window.location.href.indexOf("echosupplements.com") == -1) {
	var localMode = true;
} else {
	var localMode = false;
}
var shopByDeptBorderColor = '#000000';

if(localMode) {
	var baseDir = 'http://localhost/deepblue08/echo';
	var secureBaseDir = 'https://localhost/deepblue08/echo';
} else {
	var baseDir = 'http://www.echosupplements.com';
	var secureBaseDir = 'https://www.echosupplements.com';
}