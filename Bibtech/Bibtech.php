<?php
if( !defined( 'MEDIAWIKI' ) ) die();

// hard load
require_once( "Parser.php" );
require_once( "Render.php" );
// autoload

/// \var wgBibtechBib
/// \brief lookup table for btref
$wgBibtechBib = array();

// register
$wgExtensionCredits['validextensionclass'][] = array(
  'name'   => 'Bibtech',
  'author' => 'Tom Hornos', 
  'url'    => '', 
  'description' => 'Brave new bibtex'
);

// tag extensions
$wgHooks['ParserFirstCallInit'][] = 'wg_bt_pfci';

// parser extension
$wgHooks['LanguageGetMagic'][] = 'wg_bt_lgm';

function wg_bt_pfci( &$parser ) {
  $parser->setHook( 'bibtech', 'wg_bt_tag' );
  $parser->setFunctionHook( 'btref', 'wg_bt_m_btref' );
  return true;
}

function wg_bt_lgm( &$magicWords, $langCode ) {
  $magicWords['btref'] = array( 0, 'btref' );
  return true;
}

/// \fn wg_bt_tag
/// \brief tag parser hook
///
function wg_bt_tag( $input, $args, $parser, $frame ) {
  // lookput asn render

/*
  $r = bt_l_tag( bt_tag( $input ) );
  ob_start();
  var_dump( $r );
  return ob_get_clean();
*/
  return bt_r_tag( bt_l_tag( bt_tag( $input ) ), $args );
}

function wg_bt_m_btref( $parser, $arg1 ) {
  // render
  return bt_r_m_btref( $parser, $arg1 );
}

/*
  ob_start();
  var_dump( $r );
  return ob_get_clean();
*/
?>
