<?php
if( !defined( 'MEDIAWIKI' ) ) die();

// hard load
require_once( "Parser.php" );
require_once( "Render.php" );
// autoload

// globals
/// \var wgBibtechSortBy
/// \brief sort by tag
///
/// TODO: tag array
$wgBibtechSortBy = "ckey";
$wgBibtechCkeys  = array();

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
/// TODO:
/// latex special chars
/// TODO args:
/// src: external file
/// sty: .sty based styling 
function wg_bt_tag( $input, $args, $parser, $frame ) {
  global $wgBibtechSortBy;

  if( isset( $args["sortby"] ) ) {
    $wgBibtechSortBy = bt_str( $args["sortby"] );
  }
  // parse sort render
  return bt_r_tag( bt_s_tag( bt_tag( $input ) ), $args );
}

function wg_bt_m_btref( $parser, $arg1 = NULL, $arg2 = NULL ) {
  // render
  return bt_r_m_btref( $parser, $arg1, $arg2 );
}

?>
