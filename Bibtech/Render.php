<?php
if( !defined( 'MEDIAWIKI' ) ) die();

require_once( "Bibtech.i18n.php" );

function bibtech_msg( $msg = "" ) {
  global $wgLang;
  global $wgBibtechMessages;
  $code = $wgLang->getCode();
  if( ! isset( $wgBibtechMessages[$code] ) ) {
    $code = "en";
  }
  $dict = $wgBibtechMessages[$code];
  if( isset( $dict[$msg] ) )
    return $dict[$msg];

  return "nan";
}

function bibtech_render( $barr = NULL, $args = NULL ) {
  global $wgScriptPath;
  // load Style class implementing call.type
  $no  = 1;
  $out = "";
  $sty = "default";
  $sty_src = "sty/" . $sty . ".php";
  if( isset( $args["style"] ) ) {
    $sty = bibtech_str( $args["style"] );
    $sty_src = "sty/" . $sty . ".php";
  }

  // basic access check
  if( ! is_readable( __DIR__ . "/" . $sty_src ) )
    return bibtech_msg( "style_error" ) . ": " . $sty_src;

  // hook css
  $bibtech_css     = "Bibtech.css";
  $bibtech_sty_css = "sty/" . $sty . ".css";
  if( ! is_readable( __DIR__ . "/" . $bibtech_css ) )
    return bibtech_msg( "style_error" ) . " " . $bibtech_css;
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
  $out .= bibtech_sty_begin( $args );

  // ITERATE {call.type$}
  foreach( $barr as $ckey => $arr ) {
    $arr["no"] = $no;
    $ckey = bibtech_str( $ckey );

    $out .= bibtech_sty_entry_begin( $ckey, $arr, $args );
    $out .= bibtech_sty_entry( $ckey, $arr, $args );
    $out .= bibtech_sty_entry_end( $ckey, $arr, $args );
    ++$no;
  }

  // EXECUTE {end.bib}
  $out .= bibtech_sty_end( $args );

  // finish styling
  return $out;
}

function bibtech_id( $args = NULL ) {
  if( ! isset( $args["prefix"] ) )
    return "bibtech_";

  $prefix = bibtech_str( $args["prefix"] );
  return "bibtech_" . $prefix;
}


function bibtech_sty_begin( $args = NULL ) {
  $id   = bibtech_id( $args );
  $ret  = '<div id="' . $id . "\">\n";
  $ret .= "<h3><span class=\"mw-headline\">";
  $ret .= bibtech_msg( "bibliography" );
  $ret .= "</span></h3>\n";
  return $ret;
}

function bibtech_sty_end( $args = NULL ) {
  return "</div>";
}


function bibtech_sty_entry_begin( $ckey, $arr = NULL, $args = NULL ) {
  $id   = bibtech_id( $args ) . "_" . $ckey;
  $ret .= '<div class="bibtech_entry">';
  $ret .= "<a name=\"" . $id . "\"></a>";
  $ret .= "<div class=\"bibtech_entry_no\"><span class=\"bibtech_entry_no\">" . $arr["no"] . "</span></div>";
  $ret .= "<div class=\"bibtech_entry_txt\" id=\"" . $id . "\">";
  return $ret;
}

function bibtech_sty_entry_end( $ckey, $arr = NULL, $args = NULL ) {
  return "</div></div>\n";
}

function bibtech_sty_entry( $ckey, $arr = None ) {
  // return bibtech_sty_entry_r( $arr );
  $type = bibtech_str( $arr["type"] );
  $efunc = "bibtech_sty_entry_" . $type;
  if( ! function_exists( $efunc ) ) {
    $efunc = "bibtech_sty_entry_article";
  }
  return call_user_func_array( $efunc, array( $arr ) );
}

function bibtech_sty_format( $tag, $str = "" ) {
  $tag = bibtech_str( $tag );
  // custom formatting
  $ffunc = "bibtech_sty_format_" . $tag;
  if( function_exists( $ffunc ) ) {
    $str = call_user_func_array( $ffunc, array( $str ) );
  }
  return '<span class="bibtex_' . $tag . '">' . $str . '</span>';
}


function bibtech_sty_entry_r( $arr ) {
  $out = "";
  foreach( $arr as $tag => $str ) {
    $out .= "<div>";
    $out .= "<span>" . $tag . "</span> ";
    $out .= "<span>" . $str . "</span>";
    $out .= "</div>";
  }
  return $out;
}

?>
