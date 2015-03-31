<?php
/**
 * BoxDocument class
 *
 **/
class BoxDocument
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
		$contents = $this->boxApi->getAssets($this, $ext);

		return $contents;
	}


	/**
	 * Uploads a document to Box View API via multipart upload
	 * Ref https://developers.box.com/view/#post-documents
	 * 
	 * @return (mixed) NULL or a zip contents
	 */
	public function upload()
	{
		if(empty($this->file_path) && empty($this->file_url)) {
			return $this;
		}

		// prefer URL upload which is quicker
		if($this->file_url)
		{
			$response = $this->boxApi->urlUpload($this);

		} elseif($this->file_path)
		{
			$response = $this->boxApi->multipartUpload($this);
		}
		
		if($response)
		{
			$this->id 		= $response->id;
			$this->status 	= $response->status;
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
		$result = $this->boxApi->delete($this);

		if($result)
		{
			$this->id = false;

			return true;

		} else
		{
			return false;
		}
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
	 * Set URL (can be distant)
	 *
	 */
	public function setUrl($file_url)
	{
		$this->file_url = $file_url;

		return $this;
	}


	/**
	 * Set path (file local path on server)
	 *
	 */
	public function setPath($file_path)
	{
		$this->file_path = $file_path;

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
		return $this->boxApi->getMessages();
	}

}
