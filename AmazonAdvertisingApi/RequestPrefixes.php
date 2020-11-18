<?php
namespace AmazonAdvertisingApi;

require_once "Versions.php";


class RequestPrefixes
{
    public static function getPrefix($requestType) { 
    	switch ($requestType) { 
    		case 'profile': 
    		case 'report': 
    			$prefix = Versions::API_VERSION . "/"; 
    			break;
    		case 'products': 
    			$prefix = Versions::API_VERSION . "/sp/";
    			break;
    		case 'display':
    			$prefix = "sd/";
    			break;
    		case 'brands':
    			$prefix = "sb/";
    			break;
    		default:
    			throw new \Exception("Request type unsupported: " . $requestType);
    	}
    	return $prefix
    }
}
