<?php
/*
 * This file is used as part of the 3D-Secure process with Protx, and just shows a form to people whose browsers don't support frames
 */
?>
<SCRIPT LANGUAGE="Javascript">
	function OnLoadEvent() { document.form.submit(); }
</SCRIPT>
<html>
<head>
<title>3D Secure Verification</title>
</head>
<body OnLoad="OnLoadEvent();">
<form name="form" action="<?php
echo $_GET ['ACSURL'];
?>" method="post">
<input type="hidden" name="PaReq" id="PaReq"
	value="<?php
	echo $_GET ['PAREQ'];
	?>" /> <input type="hidden"
	name="TermUrl" id="TermUrl" value="<?php
	echo $_GET ['TERM_URL'];
	?>" />
<input type="hidden" name="MD" id="MD"
	value="<?php
	echo $_GET ['MD'];
	?>" />
<NOSCRIPT>
<center>
<p>Please click button below to Authenticate your card</p>
<input type="submit" value="Go" />
</p>
</center>
</NOSCRIPT>
</form>
</body>
</html>