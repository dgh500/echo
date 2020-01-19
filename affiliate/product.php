<?php

$doc = new DOMDocument ( );
$xsl = new XSLTProcessor ( );

$doc->load ( 'productPage.xsl' );
$xsl->importStyleSheet ( $doc );

$doc->load ( 'productFeed.xml' );
$xsl->SetParameter ( '', 'prod', '426636' );
echo $xsl->transformToXML ( $doc );

?>