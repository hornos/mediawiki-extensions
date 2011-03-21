<?php
if( !defined( 'MEDIAWIKI' ) ) die();

/// \fn bibtech_str
/// \brief safe string
function bibtech_str( $str = "" ) {
  return preg_replace( '/[^a-zA-Z0-9_]/', "", $str );
}

/// \fn bibtech_ttc
/// \brief replace trailing coma
function bibtech_parser_ttc( $line = "" ) {
  return preg_replace( "/,[[:space:]]*$/", "", $line );
}


/// \fn bibtech_parse
/// \brief parser
function bibtech_parser( $input, $args = NULL ) {
  $iarr  = explode( "\n", $input );
  $state = "";
  $tarr  = array();
  $barr  = array();

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
      $larr = explode( "=", bibtech_parser_ttc( $line ), 2 );
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


/// \fn bibtech_cmp
/// \brief comparator by tag
function bibtech_cmp( $a, $b ) {
  global $wgBibtechSortBy;
  $tag = $wgBibtechSortBy;
  $ta  = strtolower( $a[$tag] );
  $tb  = strtolower( $b[$tag] );
  return strcasecmp( $ta, $tb );
}


/// \fn bibtech_sort
/// \brief sort bib array by a tag
function bibtech_sort( $barr ) {
  uasort( $barr, "bibtech_cmp" );
  return $barr;
}

?>
