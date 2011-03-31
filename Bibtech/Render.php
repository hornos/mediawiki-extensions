<?php
if( !defined( 'MEDIAWIKI' ) ) die();

require_once( "Bibtech.i18n.php" );

function bt_msg( $msg = "" ) {
  global $wgLang;
  global $wgBibtechMessages;
  $code = $wgLang->getCode();
  if( ! isset( $wgBibtechMessages[$code] ) ) {
    $code = "en";
  }
  $dict = $wgBibtechMessages[$code];
  if( isset( $dict[$msg] ) )
    return $dict[$msg];

  return NULL;
}

function bt_r_tag( $barr = NULL, $args = NULL ) {
  global $wgScriptPath;
  // load Style class implementing call.type
  $no  = 1;
  $out = "";
  $sty = "default";
  $sty_src = "sty/" . $sty . ".php";
  if( isset( $args["style"] ) ) {
    $sty = bt_str( $args["style"] );
    $sty_src = "sty/" . $sty . ".php";
  }

  // basic access check
  if( ! is_readable( __DIR__ . "/" . $sty_src ) )
    return bt_msg( "style_error" ) . ": " . $sty_src;

  // hook css
  $bibtech_css     = "Bibtech.css";
  $bibtech_sty_css = "sty/" . $sty . ".css";
  if( ! is_readable( __DIR__ . "/" . $bibtech_css ) )
    return bt_msg( "style_error" ) . " " . $bibtech_css;
  else {
    $__url = $wgScriptPath . "/extensions/Bibtech/" . $bibtech_css;
    $out .= '<link rel="stylesheet" type="text/css" href="' . $__url . '" />' . "\n";
  }
  if( is_readable( __DIR__ . "/" . $bibtech_sty_css ) ) {
    $__url = $wgScriptPath . "/extensions/Bibtech/" . $bibtech_sty_css;
    $out .= '<link rel="stylesheet" type="text/css" href="' . $__url . '" />' . "\n";
  }

  // begin styling
  require_once( $sty_src );

  // EXECUTE {begin.bib}
  $out .= bt_r_tag_begin( $args );

  if( $barr == NULL ) {
    $out .= bt_r_frm_err( bt_msg( "internal_err" ) );
  }
  else {
    // ITERATE {call.type$}
    foreach( $barr as $ckey => $arr ) {

    $ckey = bt_str( $ckey );
    $out .= bt_r_entry_begin( $ckey, $arr, $args );
    $out .= bt_r_entry( $ckey, $arr, $args );
    $out .= bt_r_entry_end( $ckey, $arr, $args );

    }
  }

  // EXECUTE {end.bib}
  $out .= bt_r_tag_end( $args );

  // finish styling
  return $out;
}

function bt_r_m_btref( $parser, $ckey, $bib = NULL ) {
  global $wgBibtechBib;
  $ckey = bt_str( $ckey );
  $bib  = bt_str( $bib );
  $id   = bt_eid( $ckey, $bib == NULL ? NULL : array( "bib" => $bib ) );

  if( $bib == NULL )
    $bib = "page";

  if( ! isset( $wgBibtechBib[$bib] ) ) {
    $wgBibtechBib[$bib] = array( "ckeys" => array(), "c" => 1 );
  }

  if( ! isset( $wgBibtechBib[$bib]["ckeys"][$ckey] ) ) {
    $wgBibtechBib[$bib]["ckeys"][$ckey] = array( "rc" => 1, "no" => $wgBibtechBib[$bib]["c"] );
    ++$wgBibtechBib[$bib]["c"];
  }
  else {
    ++$wgBibtechBib[$bib]["ckeys"][$ckey]["rc"];
  }

  $out = "<a href=\"#" . $id . "\">[".$wgBibtechBib[$bib]["ckeys"][$ckey]["no"]."]</a>";
  return $parser->insertStripItem( $out, $parser->mStripState );
  // return array( $out, 'noparse' => true, 'isHTML' => true );
}

function bt_id( $args = NULL ) {
  if( ! isset( $args["bib"] ) )
    return "bt";

  $id = bt_str( $args["bib"] );
  return "bt_" . $id;
}

function bt_eid( $ckey, $args = NULL ) {
  return bt_id( $args ) . "_" . $ckey;
}

function bt_r_tag_begin( $args = NULL ) {
  $id   = bt_id( $args );
  $out  = '<div id="' . $id . "\">\n";
  $out .= "<h3><span class=\"mw-headline\">";
  $out .= bt_msg( "bibliography" );
  $out .= "</span></h3>\n";
  return $out;
}

function bt_r_tag_end( $args = NULL ) {
  return "</div>";
}

function bt_r_entry_begin( $ckey, $arr = NULL, $args = NULL ) {
  $id   = bt_eid( $ckey, $args );
  $out  = '<div class="bibtech_entry">';
  $out .= "<a name=\"" . $id . "\"></a>";
  $out .= "<div class=\"bibtech_entry_no\"><span class=\"bibtech_entry_no\">[" . $arr["no"] . "]</span></div>";
  $out .= "<div class=\"bibtech_entry_txt\" id=\"" . $id . "\">";
  return $out;
}

function bt_r_entry_end( $ckey, $arr = NULL, $args = NULL ) {
  return "</div></div>\n";
}

function bt_r_entry( $ckey, $arr = None ) {
  // return bibtech_sty_entry_r( $arr );
  $type = bt_str( $arr["type"] );
  $efunc = "bt_r_entry_" . $type;
  if( ! function_exists( $efunc ) ) {
    $efunc = "bt_r_entry_article";
  }
  return call_user_func_array( $efunc, array( $arr ) );
}

function bt_r_frm( $tag, $str = "" ) {
  $tag = bt_str( $tag );
  // custom formatting
  $ffunc = "bt_r_frm_" . $tag;
  if( function_exists( $ffunc ) ) {
    $str = call_user_func_array( $ffunc, array( $str ) );
  }
  return '<span class="bibtech_' . $tag . '">' . $str . '</span>';
}


function bt_entry_r( $arr ) {
  $out = "";
  foreach( $arr as $tag => $str ) {
    $out .= "<div>";
    $out .= "<span>" . $tag . "</span> ";
    $out .= "<span>" . $str . "</span>";
    $out .= "</div>";
  }
  return $out;
}

function bt_r_frm_err( $str ) {
  return '<span class="bibtech_error">' . $str . '</span>';
}

?>
