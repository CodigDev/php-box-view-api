<?php

namespace RomainBruckert\BoxViewApi;

use Exception;

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
	public function __construct($config = array())
	{
		if(isset($config['api_key'])) {
			$this->config['api_key'] = $config['api_key'];
		}
	}


	/**
	 * Sets up configuration or returns it to be passed to BoxDocument
	 *
	 * @param array Config array containing at leats the Box view API key.
	 *
	 * @return object RomainBruckert\BoxViewApi\BoxApi. 
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
	 * Deletes a document from your Box View API account.
	 * 
	 * Doc {@link https://box-view.readme.io/}
	 * Doc {@link https://developers.box.com/view/#delete-documents-id}
	 * 
	 * @param object * @return object RomainBruckert\BoxViewApi\BoxDocument.
	 *
	  * @return object Box API response.
	 */
	public function delete(BoxDocument $document)
	{
		if(empty($document->id)) {
			throw new Exception("BoxApiException.delete Document id is invalid.");
		}

		$curlParams[CURLOPT_URL] 			= 'https://view-api.box.com/1/documents/'.$document->id;
		$curlParams[CURLOPT_CUSTOMREQUEST]  = 'DELETE';

		// get query results
		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result))
    	{
    		$this->messages['BoxApiException.delete'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;
    	}
    	
    	return empty($result->response) ? false : $result->response;
	}


	/**
	 * Retrieves document meta data.
	 * 
	 * Doc {@link https://box-view.readme.io/}
	 * Doc {@link https://developers.box.com/view/#post-documents}
	 *
	 * @param object RomainBruckert\BoxViewApi\BoxDocument.
	 * 
	 * @return object Box API response.
	 */
	public function getMetadata(BoxDocument $document)
	{
		if(empty($document->id)) {
			throw new Exception("BoxApiException.getMetadata Document id is invalid.");
		}
		
		$curlParams[CURLOPT_URL] = 'https://view-api.box.com/1/documents/'.$document->id;
		
		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result))
    	{
    		$this->messages['BoxApiException.getMetadata'] = $result->response->message.' ['.$result->headers->code.']';
    		
    		return false;
    	}
    	
    	return $result->response;
	}


	/**
	 * Retrieves a thumbnail image according to the requested format.
	 * 
	 * Doc {@link https://box-view.readme.io/}
	 * Doc {@link https://developers.box.com/view/#get-documents-id-thumbnail}
	 *
	 * @param object RomainBruckert\BoxViewApi\BoxDocument.
	 * 
	 * @return object Box API response.
	 */
	public function getThumbnail(BoxDocument $document, $width = 32, $height = 32)
	{
		if(empty($document->id)) {
			throw new Exception("BoxApiException.getThumbnail Document id is invalid.");
		}

		$width 	= (int) $width;
		$height = (int) $height;

		$curlParams[CURLOPT_URL] = 'https://view-api.box.com/1/documents/'.$document->id.'/thumbnail?width='.$width.'&height='.$height;

		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result))
    	{
    		$this->messages['BoxApiException.getThumbnail'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;
    	}
    	
    	return $result->response;
	}


	/**
	 * Uploads a document using an accessible URL.
	 *
	 * Doc {@link https://box-view.readme.io/#page-post-documents}
	 * 
	 * @param array  RomainBruckert\BoxViewApi\BoxDocument.
	 * 
	 * @param object Transformation options such as SVG format request, thumbnails, etc.
	 *
	 * @return object Box API response.
	 */
	public function urlUpload(BoxDocument $document, $options = array())
	{
		$postFields  = array(
			'name' 	=> $document->name,
			'url' 	=> $document->file_url,
		);

		// set request parameters
		$curlParams[CURLOPT_URL] 			= 'https://view-api.box.com/1/documents';
		$curlParams[CURLOPT_CUSTOMREQUEST]  = 'POST';
		$curlParams[CURLOPT_HTTPHEADER][]   = 'Content-Type: application/json';
		$curlParams[CURLOPT_POSTFIELDS] 	= json_encode($postFields);
			
			// options by Box API
			if(isset($options['non_svg']) && $options['non_svg'] === true) {
				$postFields['non_svg'] = true;
			}
			if(isset($options['thumbnails']) && !empty($options['thumbnails'])) {
				$postFields['thumbnails'] = $options['thumbnails'];
			}

		// get query results
		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result))
    	{
    		$this->messages['BoxApiException.urlUpload'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;
    	}
    	
    	return $result->response;
	}


	/**
	 * Uploads a document using multipart form data transfer. Better for big files.
	 * 
	 * Doc {@link https://box-view.readme.io/#page-documents-2}
	 * 
	 * @param array  RomainBruckert\BoxViewApi\BoxDocument.
	 * 
	 * @param object Transformation options such as SVG format request, thumbnails, etc.
	 *
	 * @return object Box API response.
	 */
	public function multipartUpload(BoxDocument $document, $options = array())
	{
		$curlParams = array();
		$postFields = array();

		if(!is_file($document->file_path))
		{
			throw new Exception("BoxApi::multipartUpload() File path for BoxDocument instance is not valid.");
		}

		$postFields  = array(
			'name' 	=> $document->name,
			'file' 	=> "@".$document->file_path,
		);

			// options by Box API
			if(isset($options['non_svg']) && $options['non_svg'] === true) {
				$postFields['non_svg'] = true;
			}
			if(isset($options['thumbnails']) && !empty($options['thumbnails'])) {
				$postFields['thumbnails'] = $options['thumbnails'];
			}

		
		// set request parameters
		$curlParams[CURLOPT_URL] 			= 'https://upload.view-api.box.com/1/documents';
		$curlParams[CURLOPT_CUSTOMREQUEST]  = 'POST';
		$curlParams[CURLOPT_HTTPHEADER][]   = 'Content-Type: multipart/form-data';
		$curlParams[CURLOPT_SSL_VERIFYPEER] = false;
		$curlParams[CURLOPT_POSTFIELDS] 	= $postFields;

		// get query results
		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result))
    	{
    		$this->messages['BoxApiException.multipartUpload'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;
    	}
    	
    	return empty($result->response) ? false : $result->response;
	}


	/**
	 * Download documents assets if document is viewable.
	 * 
	 * Doc {@link https://box-view.readme.io/}
	 * Doc {@link https://developers.box.com/view/#get-documents-id-content }
	 * 
	 * @param array  RomainBruckert\BoxViewApi\BoxDocument.
	 * 
	 * @param string Extension/format to return (.zip or .pdf).
	 *
	 * @return object Box API response.
	 */
	public function getAssets(BoxDocument $document, $ext = 'zip')
	{
		if(empty($document->id)) {
			throw new Exception("Document malformated, id is missing");
		}
		
		// then get the zip
		$curlParams[CURLOPT_URL] = 'https://view-api.box.com/1/documents/'.$document->id.'/content.'.$ext;
		
		// get query results
		$result = $this->request($curlParams);

		// when results throwed an error
		if(!$this->responseIsValid($result)) {

    		$this->messages['BoxApiException.getAssets'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;

    	} else {

    		return empty($result->response) ? false : $result->response;
    	}
	}


	/**
	 * Sets up a webhook url to receive notifications from the Box View API service.
	 * Only one webhook per account can be set at this time.
	 *
	 * Docs {@link https://box-view.readme.io/}
	 * 
	 * @param string Your webhook callback URL
	 *
	 * @return JSON response
	 */
	public function setWebhook($webhookUrl)
	{
		$postFields['url'] = $webhookUrl;

		$curlParams = array(
			CURLOPT_URL 			=> "https://view-api.box.com/1/settings/webhook",
			CURLOPT_CUSTOMREQUEST 	=> "POST",
			CURLOPT_POSTFIELDS 		=> json_encode($postFields),
			CURLOPT_HTTPHEADER   	=> array("Content-Type: application/json"),
		);

		$result = $this->request($curlParams);

		if(!$this->responseIsValid($result)) {

			$this->messages['BoxApiException.setWebhook'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;

    	} else {
    		
    		return empty($result->response) ? false : $result->response;
    	}
	}


	/**
	 * Deletes the webhook url.
	 *
	 * Docs {@link https://box-view.readme.io/}
	 *
	 * @return JSON response
	 */
	public function getWebhook()
	{
		$curlParams = array(
			CURLOPT_URL => "https://view-api.box.com/1/settings/webhook"
		);
		
		$result = $this->request($curlParams);
		
		if(!$this->responseIsValid($result)) {

			$this->messages['BoxApiException.setWebhook'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;

    	} else {
    		
    		return empty($result->response) ? false : $result->response;
    	}
	}


	/**
	 * Deletes the webhook url.
	 *
	 * Docs {@link https://box-view.readme.io/}
	 *
	 * @return JSON response
	 */
	public function deleteWebhook()
	{
		$curlParams = array(
			CURLOPT_URL 			=> "https://view-api.box.com/1/settings/webhook",
			CURLOPT_CUSTOMREQUEST 	=> "DELETE",
		);
		
		$result = $this->request($curlParams);

		if(!$this->responseIsValid($result)) {

			$this->messages['BoxApiException.setWebhook'] = $result->response->message.' ['.$result->headers->code.']';

    		return false;

    	} else {
    		
    		return empty($result->response) ? false : $result->response;
    	}
	}


	/**
	 * Makes a completely customizable CURL request.
	 *
	 * Docs {@link https://box-view.readme.io/}
	 *
	 * @param array CURL params as classic curl options.
	 *
	 * @return array JSON decoded response. 
	 */
	protected function request($curlParams = array())
	{
		$ch = curl_init();
    	// Return the result of the curl_exec().
    	
    	$curlParams[CURLOPT_RETURNTRANSFER] = 1;
    	$curlParams[CURLOPT_FOLLOWLOCATION] = 1;

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

    	// return response
    	return $result;
	}


	/**
	 * Parse CURL response and headers from raw request result.
	 *
	 * @param string Raw Curl response.
	 *
	 * @return object Decoded headers and response body.
	 */
	private function parseResponse($curl, $response = null)
	{
		$headers = $this->parseHeaders($curl);

		if($decoded = json_decode($response)) {
			$body = $decoded;
		} else {
			$body = $response;
		}

		return (object) array(
			'response' 	=> $body,
			'headers' 	=> $headers
		);
	}


	/**
	 * Parse CURL headers from a request.
	 *
	 * @param object Curl object.
	 * 
	 * @return object Decoded headers.
	 */
	private function parseHeaders($curl)
	{
		$headers = (object) array();

    	$headers->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    	return $headers;
	}


	/**
	 * Checks that the result/response object is valid and not 
	 * an error according to the Box View API response format.
	 * 
	 * @param object Decoded Box API reponse.
	 *
	 * @return boolean True or false.
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
	 * Returns error messages that were stacked during runtime.
	 *
	 * @return array Array of error messages.
	 */
	public function getMessages()
	{
		return $this->messages;
	}

}
