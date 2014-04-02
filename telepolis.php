<?php

// Include Simple HTML DOM
require 'simple_html_dom.php';

// Set charset to UTF-8
header( 'Content-Type: text/html; charset=utf-8' );

echo "
<html>
  <head>
    <link href='http://fonts.googleapis.com/css?family=Merriweather:400,700italic,700,400italic,300italic,300' rel='stylesheet' type='text/css'>
    <style>
      body {
        font-family: 'Merriweather', serif;
        font-size: 17px;
        line-height: 1.6em;
      }
      h1, h2 {
        line-height: 1.25em;
      }
      h3 {
        line-height: 1.4em;
      }
      h4 {
        margin-bottom: 0;
      }
      .artext {
        width: 85%;
        margin: 50px auto;
      }
      .bu {
        font-size: 12px;
        padding: 10px 0;
      }
    </style>
  </head>
  <body>
";

// Check if URL variable is set
if ( isset( $_GET['u'] ) ) :

  // Get article URL
  $url = $_GET['u'];

  // Define article root for rewriting image links
  preg_match( '/^(http\:\/\/.*\/)(.*)$/', $url, $matches );
  $article_root = $matches[1];

  // Get raw HTML
  $html = file_get_html( $url );

  // Define article object
  $article = $html->find( 'div.artext', 0 );

  // Remove unwanted elements
  $remove = array(
    'div#breadcrumb',
    'div#bottomload-nox',
    'div#topbox',
    'div#bottombox',
    '.bild-plus'
  );

  foreach ( $remove as $selector ) :
    foreach ( $article->find( $selector ) as $e ) :
      $e->outertext = "";
    endforeach;
  endforeach;

  // Rewrite internal links
  foreach ( $article->find('a') as $e ) :
    $search = strpos( $e->href, ':' );
    if ( $search === false ) :
      $tmp = 'http://www.heise.de' . $e->href;
      $e->href = $tmp;
    endif;
  endforeach;

  // Rewrite image links
  foreach ( $article->find('img') as $e ) :
    $search = strpos( $e->src, ':' );
    if ( $search === false ) :
      $tmp = $article_root . $e->src;
      $e->src = $tmp;
    endif;
  endforeach;

  // Output cleaned article
  echo $article;

else :

  echo 'Nothing to parse.';

endif;

echo "
  </body>
</html>
";