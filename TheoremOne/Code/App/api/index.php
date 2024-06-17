<?php

// Set the content type header to json data, as that is what we will return upon successful request
header("Content-type: application/json; charset=UTF-8");

// I will assume that the URL will have something like http://localhost/api/foo in a successful request, I need to validate the "foo" part
$parts = explode( "/", $_SERVER['REQUEST_URI'] );

// I want to check that I am doing this right
//print_r( $parts );

// We need at least 3 items in the URI for it to be a valid request.  Item 2 must be foo for it to be valid
if( sizeof( $parts ) < 3 || $parts[2] != "foo" ){
    // Invalid request, return 404
    http_response_code( 404 );
    // Stop executing
    exit;
}
// If valid request
else{
    // Need to grab the JSON file from the blob storage
    $blob = 'https://theo1sample.blob.core.windows.net/greetings/greetings.json';
    $data = file_get_contents( $blob );

    // Return the JSON data retrieved
    echo $data;
}

?>