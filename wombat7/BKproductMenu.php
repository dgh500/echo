<?php
if (! isset ( $_GET ['catalogue'] )) {
	$_GET ['catalogue'] = 'Deep Blue Dive';
}
?>
<link rel="StyleSheet" href="dtree/dtree.css" type="text/css" />
<script type="text/javascript" src="dtree/dtree.js"></script>

<div id="productMenuProductTree"><script type="text/javascript">
		<!-- Next thing to do is make this come from the database!

		d = new dTree('d');

		d.add(0,-1,'<?php
		echo $_GET ['catalogue']?>');
		d.add(1,0,'Packages','editArea/category/1/Packages','','editAreaContainer');
		d.add(2,0,'Accessories','editArea/category/2/Accessories','','editAreaContainer');
		d.add(3,1,'BCD and Reg Packages','editArea/category/3/BCD and Reg Packages','','editAreaContainer');
		d.add(8,1,'Dry suit packages','editArea/category/1/Drysuit packages','','editAreaContainer');
		d.add(13,1,'Fin boots and glove packages','example01.html');		

		d.add(4,0,'Active Water Equipment','editArea/category/3/Active water equipment','','editAreaContainer');
		d.add(5,3,'Buddy Commando Package','editArea/product/1/Commando','','editAreaContainer');
		d.add(14,3,'Buddy Explorer Package','editArea/product/2/Explorer','','editAreaContainer');

		d.add(15,8,'Northern Diver Cortex Package','editArea/product/3/Cortex','','editAreaContainer');
		d.add(16,13,'Mares Quattro Packages','editArea/product/4/Quattro','','editAreaContainer');

		d.add(7,0,'Bags and boxes','editArea/category/3/bags and boxes','','editAreaContainer');
		d.add(9,0,'Batteries','editArea/category/3/batteries','','editAreaContainer');
		d.add(10,9,'Duracell','editArea/product/3/duracell','','editAreaContainer');
		d.add(11,9,'Everlast','editArea/product/3/everlast','','editAreaContainer');
		d.add(12,0,'BCDs and Wings','editArea/category/3/bcds amd wings','','editAreaContainer');

		document.write(d);

		//-->
	</script></div>

</div>