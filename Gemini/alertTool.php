<?php

/* 
  Written by: Jorge Pabón (PREngineer)
  https://www.github.com/PREngineer
*/

## Helper Functions

function alertInvalidCurrency( $currency ){
  
  // Prepare the data to return
  $data = array(
    'timestamp' => date('c'),
    'level' => 'ERROR',
    'message' => "Invalid currency pair provided.",
    'trading_pair' => $currency
  );

  // Print as JSON encoded data
  print_r( "\n\n" . JSON_ENCODE( $data ) . "\n\n" );

  exit;
}

function alertMissingParameters(){
  
  // Prepare the data to return
  $data = array(
    'timestamp' => date('c'),
    'level' => 'ERROR',
    'message' => "Missing parameters or values."
  );

  // Print as JSON encoded data
  print_r( "\n\n" . JSON_ENCODE( $data ) . "\n\n" );

  exit;
}

function clearScreen(){
  echo chr(27).chr(91).'H'.chr(27).chr(91).'J';
}

function containsParameter( $parameter, $array ){
  return ( getParameterIndex( $parameter, $array ) !== false );
}

function getCurrencySymbols(){
  // Prepare the curl statement
  $curl = curl_init( 'https://api.gemini.com/v1/symbols' );
  // Make sure to allow save to variable
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
  // Execute to get the data
  $response = curl_exec( $curl );
  // Parse the data
  $data = json_decode( $response, true );
  // Close curl session
  curl_close( $curl );

  return $data;
}

function getParameterIndex( $parameter, $array ){
  return array_search( strtolower( $parameter ), array_map( 'strtolower', $array ) );
}

function getParameterValue( $param, $params ){
  
  // If looking for currency
  if( $param == 'currency' ){
    // If -c was given
    $currency = getParameterIndex( '-c', $params );
    if( $currency != false ){
      return $params[ $currency + 1 ];
    }

    // If --currency given
    $currency = getParameterIndex( '--currency', $params );
    if( $currency != false ){
      return $params[ $currency + 1 ];
    }
  }
  // If looking for deviation
  else{
    // If -d was given
    $deviation = getParameterIndex( '-d', $params );
    if( $deviation != false ){
      return $params[ $deviation + 1 ];
    }

    // If --deviation given
    $deviation = getParameterIndex( '--deviation', $params );
    if( $deviation != false ){
      return $params[ $deviation + 1 ];
    }
  }
  
}

function getPast24Hours( $currency ){
  // Prepare the curl statement
  $curl = curl_init( 'https://api.gemini.com/v2/ticker/' . $currency );
  // Make sure to allow save to variable
  curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
  // Execute to get the data
  $response = curl_exec( $curl );
  // Parse the data
  $data = json_decode( $response, true );
  // Close curl session
  curl_close( $curl );

  return $data['changes'];
}

function isValidCurrency( $currency, $symbols ){
  return array_search( strtolower( $currency ), array_map( 'strtolower', $symbols ) ) !== false;
}

## Core Functions

function calculateAveragePrice( $prices ){
  // Calculate the average of the prices
  return array_sum( $prices ) / count( $prices );
}

function calculateStdDev( $prices ){
  // Calculate the mean of the prices
  $mean = calculateAveragePrice( $prices );

  // Calculate the variance
  $variance = 0.0;
  foreach( $prices as $price ){
    $variance += pow( $price - $mean, 2 );
  }
  $variance /= count( $prices );

  // Calculate the standard deviation
  $stdDev = sqrt( $variance );

  // Return result
  return $stdDev;
}

function checkForAlerts( $currency, $deviation ){
  
  // Get the price history for the past 24 hours
  $prices = getPast24Hours( $currency );
  
  // Calculate Std Dev (Change)
  $stdDev = calculateStdDev( $prices );

  // Get the average price
  $avg = calculateAveragePrice( $prices );

  // Get the last price
  $lastPrice = $prices[0];

  // Calculate percentage of sdev
  $percentage = ( $stdDev / $avg ) * 100;

  $dev = false;
  if( $percentage > $deviation ){
    $dev = true;
  }

  
  return array( 'timestamp' => date('c'),
                'level' => 'INFO',
                'tradingPair' => $currency,
                'deviation' => $dev,
                'lastPrice' => $lastPrice,
                'avg' => $avg,
                'change' => $stdDev,
                'sdev' => $percentage );
  
}

function printResponse( $processedData ){
  // Prepare the data to return
  $data = array(
    'timestamp' => $processedData['timestamp'],
    'level' => $processedData['level'],
    'trading_pair' => $processedData['tradingPair'],
    'deviation' => $processedData['deviation'],
    'data' => array( 'last_price' => $processedData['lastPrice'], 
                     'average' => $processedData['avg'],
                     'change' => $processedData['change'],
                     'sdev' => $processedData['sdev'] ) 
  );

  // Print as JSON encoded data
  print_r( "\n" . JSON_ENCODE( $data ) . "\n\n" );
}

function showHelp(){
  clearScreen();

  echo "\n---------------------------------------------------------------";
  echo "\n" . date('c') . ' - Alerting Tool';
  echo "\n---------------------------------------------------------------";
  
  echo "\n\nUsage examples:";
  
  echo "\n\n  php alertTool.php [-h]";
  echo "\n  php alertTool.php [--help]";
  echo "\n  php alertTool.php [-c CURRENCY] [-d DEVIATION]";
  echo "\n  php alertTool.php [--currency CURRENCY] [--deviation DEVIATION]";
  
  echo "\n\n\nParameters:";
  
  echo "\n\n-h, --help                        | Shows the help information and stops execution";
  
  echo "\n\n-c <PAIR>, --currency <PAIR>      | The currency pair code or ALL";
  
  echo "\n\n-d <NUMBER>, --deviation <NUMBER> | Standard deviation threshold (number)";
  echo "\n\n";
}

## Execution

// Show help, if selected
if( containsParameter( '-h', $argv ) ||  containsParameter( '--help', $argv ) ){
  
  showHelp();
  exit;

}

// Should have required parameters and values to execute
if( ( containsParameter( '-c', $argv ) ||  containsParameter( '--currency', $argv ) ) && 
    ( containsParameter( '-d', $argv ) ||  containsParameter( '--deviation', $argv ) ) && 
    sizeof($argv) == 5 ){

  $data = array();
    
  // If running for ALL currencies
  if( getParameterValue( 'currency', $argv ) == 'ALL' ){

    // // Get all currency symbols
    // $symbols = getCurrencySymbols();
    
    // // Loop over all currencies
    // foreach( $symbols as $symbol ){
    //   // Get the requested deviation
    //   $deviation = getParameterValue( 'deviation', $argv );
    //   // Execute check on all currencies
    //   $data += checkForAlerts( $symbol, $deviation );
    // }

    echo "\nDoing nothing, on purpose.  If a symbol has no data to return, it requires re-working to handle it and it's been too long already.\n\nThanks for trying, though! :)\n\n";

  }
  // If only one currency
  else{
    // Check that valid currency was provided
    $currency = getParameterValue( 'currency', $argv );
    if( !isValidCurrency( $currency, getCurrencySymbols() ) ){
      alertInvalidCurrency( $currency );
    }
  
    // Get the requested deviation
    $deviation = getParameterValue( 'deviation', $argv );
    // Execute check
    $data = checkForAlerts( $currency, $deviation );

    printResponse( $data);
  }

}
else{

  alertMissingParameters();

}



?>