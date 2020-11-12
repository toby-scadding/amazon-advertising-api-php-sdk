<?php
namespace AmazonAdvertisingApi;

require_once "Versions.php";


class RequestPrefixes
{
	private $apiVersion = null;

	public function __construct() 
	{
		$versions = new Versions();
		$this->apiVersion = $versions->versionStrings["apiVersion"];
	}

    public $requestPrefixStrings = array(
		"display" => "sd/",
		"products" => "{$this->apiVersion}/sp/",
		"brands"   => "sb/",
		"profile" => "{$this->apiVersion}/",
		"report" => "{$this->apiVersion}/"
    );
}
