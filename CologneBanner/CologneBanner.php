<?php

if ( function_exists( 'wfLoadSkin' ) ) {
	wfLoadSkin( 'CologneBanner' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['CologneBanner'] = __DIR__ . '/i18n';
	/* wfWarn(
		'Deprecated PHP entry point used for CologneBanner skin. Please use wfLoadSkin instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return true;
} else {
	die( 'This version of the CologneBanner skin requires MediaWiki 1.25+' );
}
