<?xml version="1.0" encoding="ISO-8859-1"?>
<!-- Picks out the product ID specified bt $prod in product.php -->
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:param name="prod" />
<xsl:template match="*">
	<xsl:if test="/Catalog/Product/ProductId=$prod">
 		Title: <strong><xsl:value-of select="/Catalog/Product/DisplayName" /></strong><br />
	</xsl:if>
</xsl:template>

</xsl:stylesheet>