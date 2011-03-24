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
$wgHooks['ParserFirstCallInit'][] = 'w_bt_pfci';
// parser extension
$wgHooks['LanguageGetMagic'][] = 'w_bt_lgm';


function w_bt_pfci( &$parser ) {
  $parser->setHook( 'bibtech', 'w_bt_tparser' );
  $parser->setFunctionHook( 'btref', 'w_bt_mparser' );
  return true;
}

function w_bt_lgm( &$magicWords, $langCode ) {
  $magicWords['btref'] = array( 0, 'btref' );
  return true;
}


/// \fn w_bt_tparser
/// \brief tag parser hook
///
/// TODO:
/// latex special chars
/// TODO args:
/// src: external file
/// sty: .sty based styling 
function w_bt_tparser( $input, $args, $parser, $frame ) {
  global $wgBibtechSortBy;

  if( isset( $args["sortby"] ) ) {
    $wgBibtechSortBy = bibtech_str( $args["sortby"] );
  }
  return bibtech_trender( bibtech_tsort( bibtech_tparser( $input ) ), $args );
}

function w_bt_mparser( $parser, $arg1 = NULL, $arg2 = NULL ) {
  return bibtech_mrender( $parser, $arg1, $arg2 );
}

?>
