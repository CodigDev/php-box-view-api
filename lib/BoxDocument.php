<?php
/**
 *
 *
 **/
class BoxDocument extends BoxApi
{

	/**
	 * Id returned by Box View API on upload
	 *
	 */
	private $id;


	/**
	 * Document status at Box View API
	 * 
	 *
	 */
	private $status;
	

	/**
	 * File path on any distant accessible server
	 * 
	 */
	private $file_url = false;


	/**
	 * File path on the local server
	 * 
	 */
	private $file_path = false;



	/**
	 *
	 *
	 */
	public function __construct()
	{

	}


	/**
	 * Set id
	 * 
	 **/
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}


	/**
	 * Set status
	 * 
	 **/
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}


	/**
	 * Set file server path
	 *
	 **/
	public function setFilePath($filePath)
	{
		if(is_file($filePath))
		{
			$this->file_path = $filePath;
		}

		return $this;
	}


	/**
	 * Get id
	 * 
	 **/
	public function getId()
	{
		$this->id;
	}


	/**
	 * Get status
	 * 
	 **/
	public function getStatus()
	{
		$this->status;
	}


	/**
	 * Get file server path
	 *
	 **/
	public function getFilePath()
	{
		return $this->file_path;
	}

}
