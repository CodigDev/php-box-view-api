<?php
ini_set('display_errors', 1);

require_once('../tests/config.php');
require_once('../lib/BoxApi.php');



// 1. Testing upload
	
	/*
	$doc = new BoxDocument();
	$api = new BoxApi($config['api_key']);


	// set a file path for the document
	$doc->setFilePath(__DIR__.'/ppp-test.docx');

	// on success, id and status will be hydrated automatically
	$doc = $api->upload($doc);


		// possible errors ? BoxApi class logs them all...
		if(!empty($api->getMessages()))
		{
			echo '<pre>';
			print_r( $api->getMessages() );
			echo '</pre>';
		}


	// see how your document object was hydrated that
	echo '<pre>';
	print_r( $doc );
	echo '</pre>';
	*/
	
	$api = new BoxApi($config['api_key']);
	
	$doc = new BoxDocument();
	$doc->setId('02c02b27e6244ad0a162ea0df341ecb6');
	$doc->setStatus('ready');
	
	$assets = $api->getAssets($doc, 'zip');
	
	if($assets){
		
		$localFile = __DIR__.'/content.zip';
		file_put_contents($localFile, $assets); // watch you folder permissions here. Another subject anyway...

	} else
	{
		// whaaaat ? how did taht fail ? ;-)
	}
	
?>