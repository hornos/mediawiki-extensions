<?php
if( !defined( 'MEDIAWIKI' ) ) die();

/// custom
function bibtech_sty_format_author( $str ) {
  $str = preg_replace( "/[[:space:]]*,[[:space:]]*/", " ", $str );
  $str = preg_replace( "/[[:space:]]+and[[:space:]]+/", ", ", $str );
  // TODO: latex spec chars -> html spec chars
  return $str;
}

function bibtech_sty_format_pages( $str ) {
  return str_replace( "--", "-", $str );
}

function bibtech_sty_format_year( $str ) {
  return "(" . $str . ")";
}

function bibtech_sty_format_url( $str ) {
  return '<a target="_new" href="' . $str . '">link</a>';
}

function bibtech_sty_format_volume( $str ) {
  return '<b>' . $str . '</b>';
}

/// article
///     An article from a journal or magazine.
///     Required fields: author, title, journal, year
///     Optional fields: volume, number, pages, month, note, key
function bibtech_sty_entry_article( $arr ) {
  $fields = array( "author", "journal", "volume", "pages", "year", "url" );
  $first  = true;

  foreach( $fields as $f ) {
    if( ! isset( $arr[$f] ) || trim( $arr[$f] ) == "" )
      continue;

    if( ! $first )
      $out .= ", ";
    else
      $first = false;

    $out .= bibtech_sty_format( $f, $arr[$f] );
  }
  return $out;
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
