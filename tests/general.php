<?php
ini_set('display_errors', 1);

require_once('../tests/config.php');
require_once('../lib/BoxApi.php');



// 1. Testing upload
	
	$doc = new BoxDocument();

	$doc->setFilePath(__DIR__.'/ppp-test.docx');


	$api = new BoxApi($config['api_key']);
	

	// on success, id (uuid) and status (queued, usually) will be set
	$document = $api->upload($doc);
	

	var_dump( $document );


?>