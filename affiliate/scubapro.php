<?php

$fh = fopen ( 'scubapro.xml', 'w+' );
fwrite ( $fh, '<?xml version="1.0" encoding="ISO-8859-1"?>
			<?xml-stylesheet type="text/xsl" href="scubapro.xsl"?>
			<catalog>
				<product>
					<displayName>Scubapro Regulator Bag</displayName>
				</product>
				<product>
					<displayName>Scubapro Dive Bag</displayName>
				</product>				
			</catalog>' );
fclose ( $fh );

?>