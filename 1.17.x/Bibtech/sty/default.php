<?php
if( !defined( 'MEDIAWIKI' ) ) die();

/// custom
function bt_r_frm_author( $str ) {
  $str = preg_replace( "/[[:space:]]*,[[:space:]]*/", " ", $str );
  $str = preg_replace( "/[[:space:]]+and[[:space:]]+/", ",", $str );
  $str = preg_replace( "/[\'\\\]/", "", $str );
  $sarr = explode( ",", $str );
  $oarr = array();
  foreach( $sarr as $n ) {
    $narr = explode( " ", $n );
    $last = array_pop( $narr );
    $narr = array_merge( array( $last ), $narr );
    $name = implode( " ", $narr ); 
    array_push( $oarr, $name );
  }
  $str = implode( ", ", $oarr );
  return $str;
}

function bt_r_frm_pages( $str ) {
  return str_replace( "--", "-", $str );
}

function bt_r_frm_year( $str ) {
  return "(" . $str . ")";
}

function bt_r_frm_url( $str ) {
  return '<a class="external" target="_new" href="' . $str . '">' . wfMsg( "link" ) . '</a>';
}

function bt_r_frm_volume( $str ) {
  return '<b>' . $str . '</b>';
}

/// article
///     An article from a journal or magazine.
///     Required fields: author, title, journal, year
///     Optional fields: volume, number, pages, month, note, key
function bt_r_entry_article( $arr ) {
  $fields = array( "author", "journal", "volume", "pages", "year", "url" );
  $first  = true;
  $out = "";

  foreach( $fields as $f ) {
    if( ! isset( $arr[$f] ) || trim( $arr[$f] ) == "" ) {
      continue;
    }

    if( ! $first && $f != "author" ) {
      $out .= ", ";
    }
    if( $f == $fields[0] ) {
      $first = false;
    }

    $out .= bt_r_frm( $f, $arr );
  }
  return $out;
}

function bt_r_entry_missing( $arr ) {
  return bt_r_frm_err( wfMsg( "missing" ) . ": " . $arr["ckey"] );
}
/// book
///     A book with an explicit publisher.
///     Required fields: author/editor, title, publisher, year
///     Optional fields: volume, series, address, edition, month, note, key
/// booklet
///     A work that is printed and bound, but without a named publisher or sponsoring institution.
///     Required fields: title
///     Optional fields: author, howpublished, address, month, year, note, key
/// conference
///     The same as inproceedings, included for Scribe compatibility.
///     Required fields: author, title, booktitle, year
///     Optional fields: editor, pages, organization, publisher, address, month, note, key
/// inbook
///     A part of a book, usually untitled. May be a chapter (or section or whatever) and/or a range of pages.
///     Required fields: author/editor, title, chapter/pages, publisher, year
///     Optional fields: volume, series, address, edition, month, note, key
/// incollection
///     A part of a book having its own title.
///     Required fields: author, title, booktitle, year
///     Optional fields: editor, pages, organization, publisher, address, month, note, key
/// inproceedings
///     An article in a conference proceedings.
///     Required fields: author, title, booktitle, year
///     Optional fields: editor, series, pages, organization, publisher, address, month, note, key
/// manual
///     Technical documentation.
///     Required fields: title
///     Optional fields: author, organization, address, edition, month, year, note, key
/// mastersthesis
///     A Master's thesis.
///     Required fields: author, title, school, year
///     Optional fields: address, month, note, key
/// misc
///     For use when nothing else fits.
///     Required fields: none
///     Optional fields: author, title, howpublished, month, year, note, key
/// phdthesis
///     A Ph.D. thesis.
///     Required fields: author, title, school, year
///     Optional fields: address, month, note, key
/// proceedings
///     The proceedings of a conference.
///     Required fields: title, year
///     Optional fields: editor, publisher, organization, address, month, note, key
/// techreport
///     A report published by a school or other institution, usually numbered within a series.
///     Required fields: author, title, institution, year
///     Optional fields: type, number, address, month, note, key
/// unpublished
///     A document having an author and title, but not formally published.
///     Required fields: author, title, note
///     Optional fields: month, year, key
?>
