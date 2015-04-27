<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('../test/config.php'); 	// personal config not in repository
require_once(__DIR__.'/../lib/BoxDocument.php'); // autoloads BoxApi by itself

use RomainBruckert\BoxViewApi\BoxApi;
use RomainBruckert\BoxViewApi\BoxDocument;

/**
 * 0. Prepare our Box View API key
 *
 */
$config = array('api_key' => $config['api_key']);


//$boxApi = new BoxApi($config);
// $response = $boxApi->setWebhook('http://v3medical.ngrok.io/document/hook/viewable');

// $response = $boxApi->deleteWebhook();

// $response = $boxApi->getWebhook();

// var_dump($response);
// exit();

$document = new BoxDocument($config);


/**
 * 1. Upload a document (multipart upload)
 *
 */
// $document->setName('Test document');
// $document->setPath(__DIR__.'/ppp-test.docx');

// $document->upload();

// echo $document->getId() . ' ' . $document->getStatus();


/**
 * 1. Upload a document (URL upload)
 *
 */
// $document->setName('Test document');
// // $document->setUrl('http://www.scala-lang.org/docu/files/ScalaByExample.pdf');
// $document->setPath(__DIR__.'/ppa-test.pdf');

// $document->upload(array('non_svg' => true));

// echo $document->getId() . ' ' . $document->getStatus();



/**
 * 2. Get a document ZIP assets
 * 
 */
// $document->setId('ea65b84f6794489f8c344aceff32a097');

// $zipContents = $document->assets('zip');


// 	if($zipContents)
// 	{
// 		// put that somewhere on your server...
// 		file_put_contents(__DIR__.'/assets.zip', $zipContents);

// 	}

/**
 * Loads/retrieve document meta data or thumbnail
 *
 */
// $document->setId('48dcf7437e3e4c688abff617f4fefaaaa');

// $document->load();
// var_dump($document);

// $thumbnail = $document->thumbnail(64,64); // width x height
	
// 	// put thumnbail somewhere
// 	if($thumbnail)
// 	{
// 		file_put_contents(__DIR__.'/thumb.png', $thumbnail);
// 	} else
// 	{
// 		echo "Could not get thumbnail";
// 		var_dump($document->getMessages());
// 	}
	
	
/**
 * 2. Delete the document from Box
 *
 */
// $document->setId('48dcf7437e3e4c688abff617f4fefaaa');

// $deleted = $document->delete();
// var_dump($deleted);



// Print all error messages from tests above for debug
if(!empty($document->getMessages()))
{
	echo '<pre>';
	print_r($document->getMessages());
	echo '</pre>';
}
?>