<?php

$URL = explode( '/', $_GET['url'] );

/****************
    Top Pages
****************/

// Handle Hello
if( $URL[0] == '' )
{
  include 'hello.html';
}

// Handle 404
else
{
  include '404.html';
}

?>