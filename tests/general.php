<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once('../tests/config.php'); 	// personal config not in repository
require_once('../lib/BoxDocument.php'); // autoloads BoxApi by itself


/**
 * 0. Prepare our Box View API key
 *
 */
$config = array('api_key' => $config['api_key']);



/**
 * 1. Upload a document
 *
 */
// $document = new BoxDocument($config);

// $document->setName('Test document');

// $document->upload(__DIR__.'/ppp-test.docx');

// echo $document->getId() . ' ' . $document->getStatus();



/**
 * 2. Get a document ZIP assets
 *
 */
$document = new BoxDocument($config);

$document->setId('7afda5c3dee44665a295e6fc658d12fd');


$zipContents = $document->assets('zip');
	
	// put that somewhere on your server...
	file_put_contents(__DIR__.'/assets.zip', $zipContents);
	
?>