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


// register
$wgExtensionCredits['validextensionclass'][] = array(
  'name'   => 'Bibtech',
  'author' => 'Tom Hornos', 
  'url'    => '', 
  'description' => 'Brave new bibtex'
);

// tag extensions
$wgHooks['ParserFirstCallInit'][] = 'w_bt_pfci';

function w_bt_pfci( &$parser ) {
  $parser->setHook( 'bibtech', 'w_bt_parser' );
  return true;
}

/// \fn w_bt_parser
/// \brief parser hook
///
/// TODO:
/// latex special chars
/// TODO args:
/// src: external file
/// sty: .sty based styling 
function w_bt_parser( $input, $args, $parser, $frame ) {
  global $wgBibtechSortBy;

  if( isset( $args["sortby"] ) ) {
    $wgBibtechSortBy = bibtech_str( $args["sortby"] );
  }
  return bibtech_render( bibtech_sort( bibtech_parser( $input ) ), $args );
}
?>
