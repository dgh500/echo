<?php

$doc = new DOMDocument ( );
$xsl = new XSLTProcessor ( );

$doc->load ( 'scubapro.xsl' );
$xsl->importStyleSheet ( $doc );

$doc->load ( 'scubapro.xml' );
echo $xsl->transformToXML ( $doc );

?>