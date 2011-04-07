<?php
if( !defined( 'MEDIAWIKI' ) ) die();

/// \fn bt_str
/// \brief safe string
function bt_str( $str = "" ) {
  return preg_replace( '/[^a-zA-Z0-9._]/', "", $str );
}

/// \fn bt_num
/// \brief safe number
function bt_num( $str = "" ) {
  return preg_replace( '/[^0-9.-]/', "", $str );
}

/// \fn bt_ttc
/// \brief replace trailing coma
function bt_ttc( $line = "" ) {
  return preg_replace( "/,[[:space:]]*$/", "", $line );
}

/// \fn bt_tag
/// \brief tag parser
function bt_tag( $input = NULL, $args = NULL ) {
  $src_input = false;

  // read file source
  if( isset( $args["src"] ) ) {
    $src = bt_str( $args["src"] );
    $src = __DIR__ . "/bib/" . $src;
    if( is_readable( $src ) ) {
      $src_input = file_get_contents( $src );
    }
    else {
      return NULL;
    }
    if( ! $src_input ) {
      return NULL;
    }
    $input = $src_input;
  }
  $iarr  = explode( "\n", $input );
  $state = "";
  $tarr  = array();
  $barr  = array();

  if( $input == NULL ) {
    return $input;
  }

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

/// \fn bt_l_tag
/// \brief lookup
function bt_l_tag( $barr = NULL, $args = NULL ) {
  global $wgBibtechBib;
  global $wgBibtechRoot;

  $lbarr = array();

  if( $barr == NULL )
    return $barr;

  if( ! isset( $args["bib"] ) ) {
    $bib = $wgBibtechRoot;
  }
  else {
    $bib = bt_str( $args["bib"] );
  }

  if( ! isset( $wgBibtechBib[$bib] ) ) {
    return NULL;
  }

  // bc is the bibliography counter
  $bc = $wgBibtechBib[$bib]["bc"];
  foreach( $wgBibtechBib[$bib]["ckeys"] as $ckey => $val ) {
    $val["bc"] = $bc;
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
