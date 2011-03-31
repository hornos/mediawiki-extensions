<?php
if( !defined( 'MEDIAWIKI' ) ) die();

/// \fn bibtech_str
/// \brief safe string
function bt_str( $str = "" ) {
  return preg_replace( '/[^a-zA-Z0-9._]/', "", $str );
}

/// \fn bibtech_ttc
/// \brief replace trailing coma
function bt_ttc( $line = "" ) {
  return preg_replace( "/,[[:space:]]*$/", "", $line );
}


/// \fn bibtech_parse
/// \brief parser
function bt_tag( $input = NULL, $args = NULL ) {
  $iarr  = explode( "\n", $input );
  $state = "";
  $tarr  = array();
  $barr  = array();

  if( $input == NULL )
    return $input;

  // line by line parse
  foreach( $iarr as $line ) {

    // 0. strip spaces
    $line = trim( $line );

    // 1. comments empty spaces
    if( $line == "" || preg_match('/^#/', $line ) )
      continue;

    // 2. start processing entry
    if( preg_match('/^@[a-zA-Z]/', $line ) ) {
      $larr = explode( "{", $line, 2 );
      $type = trim( str_replace( "@", "", $larr[0] ) );
      $ckey = trim( str_replace( ",", "", $larr[1] ) );
      $tarr["type"] = $type;
      // redundancy: need for sort
      $tarr["ckey"] = $ckey;
      $state = "entry";

      continue;
    }

    // 3. finish processing entry
    if( preg_match('/^}$/', $line ) ) {
      $barr[$ckey] = $tarr;
      $tarr = array();
      $state = "";

      continue;
    }

    // 4. process tags
    if( $state == "entry" ) {
      $larr = explode( "=", bt_ttc( $line ), 2 );
      $tag  = trim( $larr[0] );
      $val  = trim( preg_replace( '/[\{\}]/', "", $larr[1] ) );
      $tarr[$tag] = $val;

      continue;
    }
  }
  // end foreach( $iarr as $line )

  // return the bib array
  return $barr;
}


function bt_l_tag( $barr = NULL, $args = NULL ) {
  global $wgBibtechBib;
  $lbarr = array();

  if( $barr == NULL )
    return $barr;

  if( ! isset( $args["bib"] ) ) {
    $bib = "page";
  }
  else {
    $bib = bt_str( $args["bib"] );
  }

  if( ! isset( $wgBibtechBib[$bib] ) ) {
    return NULL;
  }

  foreach( $wgBibtechBib[$bib]["ckeys"] as $ckey => $val ) {
    if( ! isset( $barr[$ckey] ) ) {
      $lbarr[$ckey] = array( "type" => "missing", "ckey" => $ckey );
    }
    else {
      $lbarr[$ckey] = $barr[$ckey];
    }
    $lbarr[$ckey] = array_merge( $lbarr[$ckey], $val );
  }
  return $lbarr;
}

?>
