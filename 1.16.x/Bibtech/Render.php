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

  // hook general css
  $bibtech_css     = "Bibtech.css";
  $bibtech_sty_css = "sty/" . $sty . ".css";
  if( ! is_readable( __DIR__ . "/" . $bibtech_css ) )
    return bt_msg( "style_error" ) . " " . $bibtech_css;
  else {
    $__url = $wgScriptPath . "/extensions/Bibtech/" . $bibtech_css;
    $out .= '<link rel="stylesheet" type="text/css" href="' . $__url . '" />' . "\n";
  }
  // hook style css
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
  global $wgBibtechRoot;
  
  $ckey = bt_str( $ckey );
  $bib  = bt_str( $bib );
  $id   = bt_eid( $ckey, $bib == NULL ? NULL : array( "bib" => $bib ) );
  $bc   = 0;

  if( $bib == NULL ) {
    $bib = $wgBibtechRoot;
  }

  if( ! isset( $wgBibtechBib[$bib] ) ) {
    if( $bib != $wgBibtechRoot ) {
      ++$wgBibtechBib["_bc"];
      $bc = $wgBibtechBib["_bc"];
    }
    else {
      $bc = 0;
    }
    $wgBibtechBib[$bib] = array( "ckeys" => array(), "c" => 1, "bc" => $bc );
  }

  if( ! isset( $wgBibtechBib[$bib]["ckeys"][$ckey] ) ) {
    $wgBibtechBib[$bib]["ckeys"][$ckey] = array( "rc" => 1, "no" => $wgBibtechBib[$bib]["c"] );
    ++$wgBibtechBib[$bib]["c"];
  }
  else {
    ++$wgBibtechBib[$bib]["ckeys"][$ckey]["rc"];
  }

  $no = $wgBibtechBib[$bib]["ckeys"][$ckey]["no"];

/*
  if( $bib != $wgBibtechRoot ) {
    $link = $wgBibtechBib[$bib]["bc"] . ":" . $link;
  }
*/
  $out = "<a href=\"#" . $id . "\">" . bt_r_entry_no( $no, $wgBibtechBib[$bib]["bc"] ) . "</a>";
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
  $out  = '<div class="bibtech_bibliography" id="' . $id . "\">\n";
  $html = "";

  if( isset( $args["ol"] ) ) {
    $html .= '<ol class="bibtech_bibliography">';
  }

  if( isset( $args["ul"] ) ) {
    $html .= '<ul class="bibtech_bibliography">';
  }

  if( ! isset( $args["notitle"] ) ) {
    $out .= '<h3 class="bibtech_headline"><span class="mw-headline">';
    $out .= bt_msg( "bibliography" );
    $out .= "</span></h3>\n";
  }
  // html list
  $out .= $html;
  return $out;
}

function bt_r_tag_end( $args = NULL ) {
  $html = "";
  if( isset( $args["ul"] ) ) {
    $html .= '</ul>';
  }

  if( isset( $args["ol"] ) ) {
    $html .= '</ol>';
  }

  $out  = $html;
  $out .= "</div>";
  return $out;
}

function bt_r_entry_begin( $ckey, $arr = NULL, $args = NULL ) {
  global $wgBibtechRoot;
  $html = "";
  if( isset( $args["ul"] ) ) {
    $html = '<li>';
  }

  $no   = bt_r_entry_no( $arr["no"], $arr["bc"] );
  $id   = bt_eid( $ckey, $args );
  $out  = "";
  $out  = $html;
  $out .= '<div class="bibtech_entry">';
  $out .= "<a name=\"" . $id . "\"></a>";
  $out .= "<div class=\"bibtech_entry_no\"><span class=\"bibtech_entry_no\">" . $no . "</span></div>";
  $out .= "<div class=\"bibtech_entry_txt\" id=\"" . $id . "\">";
  return $out;
}

function bt_r_entry_end( $ckey, $arr = NULL, $args = NULL ) {
  $html = "";
  if( isset( $args["ul"] ) ) {
    $html = '</li>';
  }
  $out  = "</div></div>";
  $out .= $html . "\n";
  return $out;
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

function bt_r_frm( $tag, $arr = NULL, $url = NULL ) {
  $tag = bt_str( $tag );
  $str = $arr[$tag];
  // custom formatting
  $ffunc = "bt_r_frm_" . $tag;
  if( function_exists( $ffunc ) ) {
    $str = call_user_func_array( $ffunc, array( $str ) );
  }
  if( $url != NULL ) {
    $str = '<a class="external" target="_new" href="' . $url . '">' . $str . '</a>';
  }
  return '<span class="bibtech_' . $tag . '">' . $str . '</span>';
}

function bt_r_entry_no( $no = 0, $bc = 0 ) {
  if( $no == 0 ) {
    return "";
  }
  return "[" . ($bc == 0 ? "" : $bc . ":" ) . $no ."]";
}

function bt_r_frm_err( $str ) {
  return '<span class="bibtech_error">' . $str . '</span>';
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

?>
