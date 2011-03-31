<?php
if( !defined( 'MEDIAWIKI' ) ) die();

// hard load
require_once( "Parser.php" );
require_once( "Render.php" );
// autoload

/// \var wgBibtechBib
/// \brief lookup table for btref
$wgBibtechBib = array();

$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => 'Bibtech',
    'author' => '[mailto:tom.hornos@gmail.com Tom Hornos]',
    'url' => 'http://www.mediawiki.org/wiki/Extension:Bibtech',
    'description' => 'Brave new Bibtex',
    'version' => '1.0'
);

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

  if( isset( $args["debug"] ) ) {
    // $r = bt_l_tag( bt_tag( $input ) );
    $r = bt_tag( $input, $args );
    ob_start();
    var_dump( $r );
    return ob_get_clean();
  }
  else {
    return bt_r_tag( bt_l_tag( bt_tag( $input, $args ), $args ), $args );
  }
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
