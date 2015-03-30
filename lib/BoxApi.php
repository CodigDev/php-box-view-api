<?php
/**
 * Box View API unofficial PHP wrapper
 *
 * @author Romain Bruckert
 */
class BoxApi
{

	private $api_url;
	private $api_key;


	/**
	 * 
	 *
	 */
	public function __construct($api_key)
	{
		$this->api_key = $api_key;
	}

	
	/**
	 * 
	 *
	 */
	protected function request($params = array())
	{
		$ch = curl_init();
    	// Return the result of the curl_exec().
    	
    	$curlParams[CURLOPT_RETURNTRANSFER] = true;
    	$curlParams[CURLOPT_FOLLOWLOCATION] = true;
    	
    	// Need to set the authorization header.
   		$curlParams[CURLOPT_HTTPHEADER][] = 'Authorization: Token ' . $this->api_key;
   		
   		// Set other CURL_OPT params.
    	foreach($params as $curlOpt => $val)
    	{
      		curl_setopt($ch, $curlOpt, $val);
    	}

    	// Get the response.
   		$response = curl_exec($ch);
    	
    	// Ensure our request didn't have errors.
    	if($error = curl_error($ch)) {
      		throw new Exception($error);
    	}

   		// Close and return the curl response.
    	$result = $this->parseResponse($response);

    	var_dump($result);

    	curl_close($ch);
    	
    	if(is_object($result->response) && property_exists($result->response, 'type') && $result->response->type === 'error') {
      		throw new Box_View_Exception('Error: ' . $result->response->message, $result->headers->code);
    	}

    	return $result;
	}


	/**
	 * Parse CURL response from request
	 *
	 */
	private function parseResponse($response)
	{
		return $response;
	}

}