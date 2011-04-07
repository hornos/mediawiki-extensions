<?php
if( !defined( 'MEDIAWIKI' ) ) die();

// hard load
require_once( "Parser.php" );
require_once( "Render.php" );

/// \var wgBibtechBib
/// \brief lookup table for btref
$wgBibtechBib  = array( "_bc" => 0 );

/// \var wgBibtechRoot
/// \brief default bib id
$wgBibtechRoot = "root";

// register
$wgExtensionCredits['parserhook'][] = array(
    'path' => __FILE__,
    'name' => 'Bibtech',
    'author' => '[mailto:tom.hornos@gmail.com Tom Hornos]',
    'url' => 'http://www.mediawiki.org/wiki/Extension:Bibtech',
    'description' => 'Brave new Bibtex',
    'version' => '1.0.1'
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
/// \brief bibtech tag parser hook
function wg_bt_tag( $input, $args, $parser, $frame ) {
  // debug levels: tag, lookup
  if( isset( $args["debug"] ) ) {
    $args["debug"] = bt_str( $args["debug"] );
    ob_start();
    if( $args["debug"] == "tag" ) {
      $r = bt_tag( $input, $args );
    }
    elseif( $args["debug"] == "lookup" ) {
      $r = bt_l_tag( bt_tag( $input, $args ), $args );
    }
    else {
      $r = bt_r_tag( bt_l_tag( bt_tag( $input, $args ), $args ), $args );
    }
    var_dump( $r );
    return ob_get_clean();
  }
  else {
    // 1. bt_tag: build bib array from bibtex
    // 2. bt_l_tag: lookup and pick referred entries from bt_tag
    // 3. bt_r_tag: render the result
    return bt_r_tag( bt_l_tag( bt_tag( $input, $args ), $args ), $args );
  }
}

/// \fn wg_bt_m_btref
/// \brief btref magic word hook
function wg_bt_m_btref( $parser, $ckey, $bib = NULL ) {
  // 1. render
  return bt_r_m_btref( $parser, $ckey, $bib );
}

?>
