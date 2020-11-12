<?php
namespace AmazonAdvertisingApi;

require_once "Versions.php";


class RequestPrefixes
{
	private $versionStrings = null;

	public function __construct() 
	{
		$versions = new Versions();
		$this->versionStrings = $versions->versionStrings;
	}

    public $requestPrefixStrings = array(
    	"display" => "sd/"
        "products" => "{$this->versionStrings["apiVersion"]}/sp/"
        "brands"   => "sb/"
        "profile" => "{$this->versionStrings["apiVersion"]}/"
    );
}
