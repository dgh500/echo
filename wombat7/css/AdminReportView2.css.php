<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#adminReportView2Container {
	border: 0px solid #999;
    clear: both;
    width: 900px;
    padding: 10px;
    position: relative;
}
#loading {
	position: absolute;
    top: 0px;
    right: 0px;
    width: 40px;
    background-color: #e2e2e2;
    height: 100%;
    border-left: 1px solid #999;
}
#loading img {
	margin-left: 12px;
    margin-top: 20px;
}
.clickableBg {
	width: 40px;
    height: 100%;
    float: left;
    background-color: #e2e2e2;
    background-image: url(../../images/verticalGo.jpg);
    background-repeat: no-repeat;
    background-position: 50% 50%;
    margin-right: 20px;
}
.clickableBg:hover {
	cursor: pointer; cursor: hand;
}
.clickableBgAnd {
	width: 40px;
    height: 100%;
    float: left;
    background-color: #e2e2e2;
    background-image: url(../../images/verticalAnd.jpg);
    background-repeat: no-repeat;
    background-position: 50% 50%;
    margin-right: 20px;
    margin-left: 20px;
}

#adminReportView2Container select {
	margin-top: 5px;
    width: 250px;
}
#catalogueContainer {
	border: 1px solid #999;
    clear: both;
    width: 900px;
    height: 60px;
    position: relative;
}
#catalogueContainer strong {
	margin-top: 8px;
    display: block;
    float: left;
}
#categoryManufacturerContainer {
	display: none;
    border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 900px;
    height: 60px;
}
#categoryManufacturerContainer strong {
	margin-top: 8px;
    display: block;
    float: left;
}
#productContainer {
	display: none;
	border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 900px;
	height: 60px;
}
#productContainer strong {
	margin-top: 8px;
    display: block;
    float: left;
}
#dateRangeContainer {
	display: none;
	border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 900px;
    height: 110px;
}
#dateRangeContainer strong {
	margin-top: 8px;
    display: block;
    float: left;
}
#generateReportContainer {
	display: none;
	border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 900px;
	height: 40px;
    background-color: #e2e2e2;
    background-image: url(../../images/generateSalesReport.jpg);
    background-repeat: no-repeat;
    background-position: 50% 50%;    
}
#generateReportContainer:hover {
	cursor: pointer; cursor: hand;
}
#reportGraphContainer {
	display: none;
	border: 1px solid #999;
    clear: both;
    width: 880px;
    padding: 10px;
    text-align: center;
}
#reportTotalContainer {
	display: none;
	border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 880px;
    padding: 10px;
}
#legendContainer {
	display: none;
	border: 1px solid #999;
    border-top: 0px;
    clear: both;
    width: 880px;
    padding: 10px;
}
#reportTotalContainer #salesReportTable {
	border: 1px solid #ccc;
    width: 100%;
    border-collapse: collapse;
}
#reportTotalContainer #salesReportTable thead th {
	padding: 8px;
    border: 1px solid #ccc;
    background-color: #E2E2E2;
    font-size: 10pt;
}
#reportTotalContainer #salesReportTable tfoot td {
	padding: 8px;
    border: 1px solid #ccc;
    background-color: #E2E2E2;
    font-size: 10pt;
    font-weight: bold;
}
#reportTotalContainer #salesReportTable td {
	padding: 5px;
    border: 1px solid #ccc;
}
#reportTotalContainer #salesReportTable td #cellSales, #reportTotalContainer #salesReportTable td #cellValue {
	width: 100px;
}
.alignLeft {
	text-align: left;
}
.alignCenter {
	text-align: center;
}
.alignRight {
	text-align: right;
}
#seperator {
	border: 0px solid #f00;
    clear: both;
    width: 880px;
    padding: 10px;
    height: 5px;
}

#reportDateRange {
	width: 400px;
	height: 100px;
	display: block;
	float: left;
}

#reportDateRange a {
	color: #000000;
	font-weight: bold;
}

#reportDateRange label,#reportDateRange input {
	width: 70px;
	display: block;
	float: left;
	margin-top: 5px;
}
