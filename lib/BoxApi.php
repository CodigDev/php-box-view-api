<?php
// lib/BoxApi.php

require_once(__DIR__.'/BoxDocument.php');

/**
 * Box View API unofficial PHP wrapper
 *
 * @author Romain Bruckert
 */
class BoxApi
{
	/**
	 *
	 */
	private $api_url;

	/**
	 *
	 */
	private $api_key;

	/**
	 *
	 */
	private $messages = array();


	/**
	 * 
	 *
	 */
	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}



	/**
	 * Uses Box API multipart upload to upload a document
	 * 
	 */
	public function upload(BoxDocument $document)
	{
		$curlParams = array();
		$postFields = array();

		if(!is_file($document->getFilepath()))
		{
			throw new Exception("BoxApi::upload() File path for BoxDocument instance is not valid.");
		}


		$fileContents = file_get_contents($document->getFilePath());

		$postFields  = array(
			'name' 	=> "Test document",
			'file' 	=> "@".$document->getFilePath(),
		);

		// set request parameters
		$curlParams[CURLOPT_URL] 			= 'https://upload.view-api.box.com/1/documents';
		$curlParams[CURLOPT_CUSTOMREQUEST]  = 'POST';
		$curlParams[CURLOPT_HTTPHEADER][]   = 'Content-Type: multipart/form-data';
		$curlParams[CURLOPT_SSL_VERIFYPEER] = false;
		$curlParams[CURLOPT_POSTFIELDS] 	= $postFields;

		$result = $this->request($curlParams);

		if($result)
		{
			$document->id 		= $result->id;
			$document->status 	= $result->status;
		}

		return $document;
	}


	/**
	 * Download documents assets if it is viewable
	 * 
	 */
	public function getAssets(BoxDocument $document, $ext = 'zip')
	{
		if( empty($document->getId()) ) {
			throw new Exception("Document malformated, id is missing");
		}

		if($document->getStatus() !== 'ready') {
			$this->messages[] = "Document status is not yes ready. Cannot download assets.";
			return $this;
		}

		// then get the zip
		$curlParams[CURLOPT_URL] = 'https://view-api.box.com/1/documents/'.$document->getId().'/content.'.$ext;
		
		$contents = $this->request($curlParams);
		
		$contents = (empty($contents)) ? false : $contents;
		return $contents;
	}


	/**
	 * 
	 *
	 */
	protected function request($curlParams = array())
	{
		$ch = curl_init();
    	// Return the result of the curl_exec().
    	
    	$curlParams[CURLOPT_RETURNTRANSFER] = true;
    	$curlParams[CURLOPT_FOLLOWLOCATION] = true;
    	
    	// Need to set the authorization header
   		$curlParams[CURLOPT_HTTPHEADER][] = "Authorization: Token {$this->api_key}";


   		// Set other CURL_OPT params
    	foreach($curlParams as $option => $value) {
      		curl_setopt($ch, $option, $value);
    	}

    	// Get the response
   		$response = curl_exec($ch);
    	
    	// Ensure our request didn't have errors.
    	if($error = curl_error($ch)) {
    		throw new Exception($error);
    	}

   		// Close and return the curl response
    	$result = $this->parseResponse($response);

    	// Test for error on Box API side
    	if(is_object($result) && property_exists($result->type, 'type') && $result->type === 'error')
    	{
      		$this->messages[] = $result->response->message.': '.$result->response->code;
      		return false;
    	}

    	return $result;
	}


	/**
	 * Parse CURL response from request
	 *
	 */
	private function parseResponse($response = null)
	{
		if($decoded = json_decode($response)) {
			$body = $decoded;
		} else
		{
			$body = $response;
		}

		return $body;
	}


	/**
	 * Return error messages that were stacked
	 *
	 */
	public function getMessages()
	{
		return $this->messages;
	}


}