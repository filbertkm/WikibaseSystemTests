<?php
/**
 * Initialization for WikibaseSystemTests extension
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not a valid MediaWiki entry point.' );
}

if ( !defined( 'WBL_VERSION' ) ) {
	die( 'WikibaseSystemTests requires WikibaseLib extension.' );
}

/* Setup */
require __DIR__ . '/vendor/autoload.php';

call_user_func( function() {
	global $wgExtensionCredits, $wgExtensionMessagesFiles;

	$wgExtensionCredits['wikibase'][] = array(
		'path' => __FILE__,
		'name' => 'WikibaseSystemTests',
		'author' => array( 'Katie Filbert' ),
		'version'  => '0.0.1',
		'url' => 'https://www.mediawiki.org/wiki/Extension:WikibaseSystemTests',
		'descriptionmsg' => 'wikibasesystemtests-desc',
	);

	$wgExtensionMessagesFiles['WikibaseSystemTests'] = __DIR__ . '/WikibaseSystemTests.i18n.php';
} );
