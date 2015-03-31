<?php
require_once(__DIR__.'/BoxApi.php');

/**
 * BoxDocument class
 *
 **/
class BoxDocument extends BoxApi
{

	/**
	 * Id returned by Box View API on upload
	 *
	 */
	public $id;


	/**
	 * Document name at Box View API
	 * 
	 */
	public $name;


	/**
	 * Document status at Box View API
	 * 
	 */
	public $status;
	

	/**
	 * File path on any distant accessible server
	 * 
	 */
	public $file_url = false;


	/**
	 * File path on the local server
	 * 
	 */
	public $file_path = false;


	/**
	 * File path on the local server
	 * 
	 */
	public $zip_contents = false;


	/**
	 * Private box API instance
	 * 
	 */
	private $boxApi = false;


	/**
	 *
	 *
	 */
	public function __construct($config = array())
	{
		$this->boxApi = new BoxApi($config);

		$this->boxApi->config($config);
	}


	
	/**
	 * Fetches documents asset as Zip and returns that content
	 * Ref https://developers.box.com/view/#get-documents-id-content
	 *
	 */
	public function assets($ext = 'zip')
	{
		return $this->boxApi->getAssets($this, $ext);
	}


	/**
	 * Uploads a document to Box View API via multipart upload
	 * Ref https://developers.box.com/view/#post-documents
	 * 
	 * @return (mixed) NULL or a zip contents
	 */
	public function upload($file_path)
	{
		$this->file_path = $file_path;

		$result = $this->boxApi->multipartUpload($this);

		if($result)
		{
			$this->id 		= $result->response->id;
			$this->status 	= $result->response->status;
		}

		return $this;
	}


	/**
	 * Deletes a document from the Box View API
	 * Ref https://developers.box.com/view/#delete-documents-id
	 *
	 */
	public function delete()
	{

	}


	/**
	 * Set id
	 *
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}


	/**
	 * Set name
	 *
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}


	/**
	 * Set status
	 *
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}


	/**
	 * Get id
	 *
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Get name
	 *
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * Get status
	 *
	 */
	public function getStatus()
	{
		return $this->status;
	}


	/**
	 * Get error messages from BoxApi class
	 *
	 */
	public function getMessages()
	{
		return $this->messages;
	}

}
