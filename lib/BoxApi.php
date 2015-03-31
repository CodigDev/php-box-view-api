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
	protected $config = array(
		'api_key' => false,
	);


	/**
	 *
	 */
	protected $messages = array();


	/**
	 * 
	 *
	 */
	public function __construct()
	{
		
	}


	/**
	 * Sets up configuration or returns it to be passed to BoxDocument
	 *
	 */
	public function config($config = array())
	{
		if(empty($config)) {
			return $this->config;
		}

		// Box View API key config option (required)
		$this->config['api_key'] = isset($config['api_key']) ? $config['api_key'] : false;
		
		return $this;
	}



	/**
	 * Uses Box API multipart upload to upload a document
	 * Ref https://developers.box.com/view/#post-documents
	 * 
	 */
	public function multipartUpload(BoxDocument $document)
	{
		$curlParams = array();
		$postFields = array();

		if(!is_file($document->file_path))
		{
			throw new Exception("BoxApi::upload() File path for BoxDocument instance is not valid.");
		}


		$fileContents = file_get_contents($document->file_path);

		$postFields  = array(
			'name' 	=> "Test document",
			'file' 	=> "@".$document->file_path,
		);

		// set request parameters
		$curlParams[CURLOPT_URL] 			= 'https://upload.view-api.box.com/1/documents';
		$curlParams[CURLOPT_CUSTOMREQUEST]  = 'POST';
		$curlParams[CURLOPT_HTTPHEADER][]   = 'Content-Type: multipart/form-data';
		$curlParams[CURLOPT_SSL_VERIFYPEER] = false;
		$curlParams[CURLOPT_POSTFIELDS] 	= $postFields;

		return $this->request($curlParams);
	}


	/**
	 * Download documents assets if it is viewable
	 * Ref https://developers.box.com/view/#get-documents-id-content 
	 *
	 */
	public function getAssets(BoxDocument $document, $ext = 'zip')
	{
		if(empty($document->id)) {
			throw new Exception("Document malformated, id is missing");
		}

		// if($document->status !== 'ready') {
		// 	$this->messages[] = "Document status is not yes ready. Cannot download assets.";
		// 	return false;
		// }

		// then get the zip
		$curlParams[CURLOPT_URL] = 'https://view-api.box.com/1/documents/'.$document->id.'/content.'.$ext;
		
		$result = $this->request($curlParams);
		
		return empty($result->response) ? false : $result->response;
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
   		$curlParams[CURLOPT_HTTPHEADER][] = "Authorization: Token {$this->config['api_key']}";


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
    	$result = $this->parseResponse($ch, $response);
    	curl_close($ch);

    	// Test for error on Box API side
    	if(!$this->responseIsValid($result))
    	{
      		$this->messages[] = $result->response->message.': '.$result->headers->code;

      		return false;

    	} else
    	{
    		return $result;
    	}
	}


	/**
	 * Parse CURL response from request
	 *
	 */
	private function parseResponse($ch, $response = null)
	{
		$headers = $this->parseHeaders($ch);

		if($decoded = json_decode($response))
		{
			$body = $decoded;
		} else
		{
			$body = $response;
		}

		return (object) array(
			'response' 	=> $body,
			'headers' 	=> $headers
		);
	}


	/**
	 * Parse CURL headers from request
	 *
	 */
	private function parseHeaders($ch)
	{
		$headers = new stdClass();

    	$headers->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    	return $headers;
	}


	/**
	 * Checks the result/response object is valid and not an error
	 *
	 */
	private function responseIsValid($result)
	{
		if(is_object($result->response) && isset($result->response->type) && $result->response->type === 'error')
		{
			return false;

		} else
		{
			return true;
		}
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