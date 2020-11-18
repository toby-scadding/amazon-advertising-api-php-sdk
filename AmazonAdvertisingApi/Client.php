<?php
namespace AmazonAdvertisingApi;

require_once "Versions.php";
require_once "Regions.php";
require_once "RequestPrefixes.php";
require_once "CurlRequest.php";

class Client
{    
    private $config = array(
        "clientId" => null,
        "clientSecret" => null,
        "region" => null,
        "accessToken" => null,
        "refreshToken" => null,
        "sandbox" => false);


    private $userAgent = null;
    private $endpoint = null;
    private $tokenUrl = null;
    private $requestId = null;
    public $profileId = null;

    public function __construct($config)
    {
        $this->userAgent = "AdvertisingAPI PHP Client Library v" . Versions::APPLICATION_VERSION;

        $this->_validateConfig($config);
        $this->_validateConfigParameters();
        $this->_setEndpoints();

        if (is_null($this->config["accessToken"]) && !is_null($this->config["refreshToken"])) {
            /* convenience */
            $this->doRefreshToken();
        }
    }

    public function doRefreshToken()
    {
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
            "User-Agent: {$this->userAgent}"
        );

        $refresh_token = rawurldecode($this->config["refreshToken"]);

        $params = array(
            "grant_type" => "refresh_token",
            "refresh_token" => $refresh_token,
            "client_id" => $this->config["clientId"],
            "client_secret" => $this->config["clientSecret"]);

        $data = "";
        foreach ($params as $k => $v) {
            $data .= "{$k}=".rawurlencode($v)."&";
        }

        $url = "https://{$this->tokenUrl}";

        $request = new CurlRequest();
        $request->setOption(CURLOPT_URL, $url);
        $request->setOption(CURLOPT_HTTPHEADER, $headers);
        $request->setOption(CURLOPT_USERAGENT, $this->userAgent);
        $request->setOption(CURLOPT_POST, true);
        $request->setOption(CURLOPT_POSTFIELDS, rtrim($data, "&"));

        $response = $this->_executeRequest($request);
        $response_array = json_decode($response["response"], true);
        if (array_key_exists("access_token", $response_array)) {
            $this->config["accessToken"] = $response_array["access_token"];
        } else {
            $this->_logAndThrow("Unable to refresh token. 'access_token' not found in response. ". print_r($response, true));
        }

        return $response;
    }

    public function listProfiles()
    {
        return $this->_operation(
            "profile", 
            "profiles"
        );
    }

    public function registerProfile($data)
    {
        return $this->_operation(
            "profile",
            "profiles/register", $data, "PUT"
        );
    }

    public function registerProfileStatus($profileId)
    {
        return $this->_operation(
            "profile",
            "profiles/register/{$profileId}/status"
        );
    }

    public function getProfile($profileId)
    {
        return $this->_operation(
            "profile",
            "profiles/{$profileId}"
        );
    }

    public function updateProfiles($data)
    {
        return $this->_operation(
            "profile",
            "profiles", $data, "PUT"
        );
    }

    public function getSponsoredProductsCampaign($campaignId)
    {
        return $this->_operation(
            "products",
            "campaigns/{$campaignId}"
        );
    }

    public function getSponsoredProductsCampaignEx($campaignId)
    {
        return $this->_operation(
            "products",
            "campaigns/extended/{$campaignId}"
        );
    }

    public function createSponsoredProductsCampaigns($data)
    {
        return $this->_operation(
            "products",
            "campaigns", $data, "POST"
        );
    }

    public function updateSponsoredProductsCampaigns($data)
    {
        return $this->_operation(
            "products",
            "campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsCampaign($campaignId)
    {
        return $this->_operation(
            "products",
            "campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsCampaigns($data = null)
    {
        return $this->_operation(
            "products",
            "campaigns", $data
        );
    }

    public function listSponsoredProductsCampaignsEx($data = null)
    {
        return $this->_operation(
            "products",
            "campaigns/extended", $data
        );
    }

    public function getSponsoredDisplayCampaign($campaignId)
    {
        return $this->_operation(
            "display",
            "campaigns/{$campaignId}"
        );
    }

    public function getSponsoredDisplayCampaignEx($campaignId)
    {
        return $this->_operation(
            "display",
            "campaigns/extended/{$campaignId}"
        );
    }

    public function createSponsoredDisplayCampaigns($data)
    {
        return $this->_operation(
            "display",
            "campaigns", $data, "POST"
        );
    }

    public function updateSponsoredDisplayCampaigns($data)
    {
        return $this->_operation(
            "display",
            "campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredDisplayCampaign($campaignId)
    {
        return $this->_operation(
            "display",
            "campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredDisplayCampaigns($data = null)
    {
        return $this->_operation(
            "display",
            "campaigns", $data
        );
    }

    public function listSponsoredDisplayCampaignsEx($data = null)
    {
        return $this->_operation(
            "display",
            "campaigns/extended", $data
        );
    }

    public function getSponsoredBrandsCampaign($campaignId)
    {
        return $this->_operation(
            "brands",
            "campaigns/{$campaignId}"
        );
    }

    public function createSponsoredBrandsCampaigns($data)
    {
        return $this->_operation(
            "brands",
            "campaigns", $data, "POST"
        );
    }

    public function updateSponsoredBrandsCampaigns($data)
    {
        return $this->_operation(
            "brands",
            "campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredBrandsCampaign($campaignId)
    {
        return $this->_operation(
            "brands",
            "campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredBrandsCampaigns($data = null)
    {
        return $this->_operation(
            "brands",
            "campaigns", $data
        );
    }

    public function getSponsoredProductsAdGroup($adGroupId)
    {
        return $this->_operation(
            "products",
            "adGroups/{$adGroupId}"
        );
    }

    public function getSponsoredProductsAdGroupEx($adGroupId)
    {
        return $this->_operation(
            "products",
            "adGroups/extended/{$adGroupId}"
        );
    }

    public function createSponsoredProductsAdGroups($data)
    {
        return $this->_operation(
            "products",
            "adGroups", $data, "POST"
        );
    }

    public function updateSponsoredProductsAdGroups($data)
    {
        return $this->_operation(
            "products",
            "adGroups", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsAdGroup($adGroupId)
    {
        return $this->_operation(
            "products",
            "adGroups/{$adGroupId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsAdGroups($data = null)
    {
        return $this->_operation(
            "products",
            "adGroups", $data
        );
    }

    public function listSponsoredProductsAdGroupsEx($data = null)
    {
        return $this->_operation(
            "products",
            "adGroups/extended", $data
        );
    }

    public function getSponsoredDisplayAdGroup($adGroupId)
    {
        return $this->_operation(
            "display",
            "adGroups/{$adGroupId}"
        );
    }

    public function getSponsoredDisplayAdGroupEx($adGroupId)
    {
        return $this->_operation(
            "display",
            "adGroups/extended/{$adGroupId}"
        );
    }

    public function createSponsoredDisplayAdGroups($data)
    {
        return $this->_operation(
            "display",
            "adGroups", $data, "POST"
        );
    }

    public function updateSponsoredDisplayAdGroups($data)
    {
        return $this->_operation(
            "display",
            "adGroups", $data, "PUT"
        );
    }

    public function archiveSponsoredDisplayAdGroup($adGroupId)
    {
        return $this->_operation(
            "display",
            "adGroups/{$adGroupId}", null, "DELETE"
        );
    }

    public function listSponsoredDisplayAdGroups($data = null)
    {
        return $this->_operation(
            "display",
            "adGroups", $data
        );
    }

    public function listSponsoredDisplayAdGroupsEx($data = null)
    {
        return $this->_operation(
            "display",
            "adGroups/extended", $data
        );
    }

    public function getSponsoredBrandsAdGroup($adGroupId)
    {
        return $this->_operation(
            "brands",
            "adGroups/{$adGroupId}"
        );
    }

    public function listSponsoredBrandsAdGroups($data = null)
    {
        return $this->_operation(
            "brands",
            "adGroups", $data
        );
    }

    public function getSponsoredProductsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "keywords/{$keywordId}"
        );
    }

    public function getSponsoredProductsBiddableKeywordEx($keywordId)
    {
        return $this->_operation(
            "products",
            "keywords/extended/{$keywordId}"
        );
    }

    public function createSponsoredProductsBiddableKeywords($data)
    {
        return $this->_operation(
            "products",
            "keywords", $data, "POST"
        );
    }

    public function updateSponsoredProductsBiddableKeywords($data)
    {
        return $this->_operation(
            "products",
            "keywords", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "keywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsBiddableKeywords($data = null)
    {
        return $this->_operation(
            "products",
            "keywords", $data
        );
    }

    public function listSponsoredProductsBiddableKeywordsEx($data = null)
    {
        return $this->_operation(
            "products",
            "keywords/extended", $data
        );
    }

    public function getSponsoredBrandsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "brands",
            "keywords/{$keywordId}"
        );
    }

    public function createSponsoredBrandsBiddableKeywords($data)
    {
        return $this->_operation(
            "brands",
            "keywords", $data, "POST"
        );
    }

    public function updateSponsoredBrandsBiddableKeywords($data)
    {
        return $this->_operation(
            "brands",
            "keywords", $data, "PUT"
        );
    }

    public function archiveSponsoredBrandsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "brands",
            "keywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredBrandsBiddableKeywords($data = null)
    {
        return $this->_operation(
            "brands",
            "keywords", $data
        );
    }

    public function getSponsoredProductsNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "negativeKeywords/{$keywordId}"
        );
    }

    public function getSponsoredProductsNegativeKeywordEx($keywordId)
    {
        return $this->_operation(
            "products",
            "negativeKeywords/extended/{$keywordId}"
        );
    }

    public function createSponsoredProductsNegativeKeywords($data)
    {
        return $this->_operation(
            "products",
            "negativeKeywords", $data, "POST"
        );
    }

    public function updateSponsoredProductsNegativeKeywords($data)
    {
        return $this->_operation(
            "products",
            "negativeKeywords", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "negativeKeywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsNegativeKeywords($data = null)
    {
        return $this->_operation(
            "products",
            "negativeKeywords", $data
        );
    }

    public function listSponsoredProductsNegativeKeywordsEx($data = null)
    {
        return $this->_operation(
            "products",
            "negativeKeywords/extended", $data
        );
    }

    public function getSponsoredBrandsNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "brands",
            "negativeKeywords/{$keywordId}"
        );
    }

    public function createSponsoredBrandsNegativeKeywords($data)
    {
        return $this->_operation(
            "brands",
            "negativeKeywords", $data, "POST"
        );
    }

    public function updateSponsoredBrandsNegativeKeywords($data)
    {
        return $this->_operation(
            "brands",
            "negativeKeywords", $data, "PUT"
        );
    }

    public function archiveSponsoredBrandsNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "brands",
            "negativeKeywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredBrandsNegativeKeywords($data = null)
    {
        return $this->_operation(
            "brands",
            "negativeKeywords", $data
        );
    }

    public function getSponsoredProductsCampaignNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords/{$keywordId}"
        );
    }

    public function getSponsoredProductsCampaignNegativeKeywordEx($keywordId)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords/extended/{$keywordId}"
        );
    }

    public function createSponsoredProductsCampaignNegativeKeywords($data)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords", $data, "POST"
        );
    }

    public function updateSponsoredProductsCampaignNegativeKeywords($data)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords", $data, "PUT"
        );
    }

    public function removeSponsoredProductsCampaignNegativeKeyword($keywordId)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsCampaignNegativeKeywords($data = null)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords", $data
        );
    }

    public function listSponsoredProductsCampaignNegativeKeywordsEx($data = null)
    {
        return $this->_operation(
            "products",
            "campaignNegativeKeywords/extended", $data
        );
    }

    public function getSponsoredProductsProductAd($productAdId)
    {
        return $this->_operation(
            "products",
            "productAds/{$productAdId}"
        );
    }

    public function getSponsoredProductsProductAdEx($productAdId)
    {
        return $this->_operation(
            "products",
            "productAds/extended/{$productAdId}"
        );
    }

    public function createSponsoredProductsProductAds($data)
    {
        return $this->_operation(
            "products",
            "productAds", $data, "POST"
        );
    }

    public function updateSponsoredProductsProductAds($data)
    {
        return $this->_operation(
            "products",
            "productAds", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsProductAd($productAdId)
    {
        return $this->_operation(
            "products",
            "productAds/{$productAdId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsProductAds($data = null)
    {
        return $this->_operation(
            "products",
            "productAds", $data
        );
    }

    public function listSponsoredProductsProductAdsEx($data = null)
    {
        return $this->_operation(
            "products",
            "productAds/extended", $data
        );
    }

    public function getSponsoredDisplayProductAd($productAdId)
    {
        return $this->_operation(
            "display",
            "productAds/{$productAdId}"
        );
    }

    public function getSponsoredDisplayProductAdEx($productAdId)
    {
        return $this->_operation(
            "display",
            "productAds/extended/{$productAdId}"
        );
    }

    public function createSponsoredDisplayProductAds($data)
    {
        return $this->_operation(
            "display",
            "productAds", $data, "POST"
        );
    }

    public function updateSponsoredDisplayProductAds($data)
    {
        return $this->_operation(
            "display",
            "productAds", $data, "PUT"
        );
    }

    public function archiveSponsoredDisplayProductAd($productAdId)
    {
        return $this->_operation(
            "display",
            "productAds/{$productAdId}", null, "DELETE"
        );
    }

    public function listSponsoredDisplayProductAds($data = null)
    {
        return $this->_operation(
            "display",
            "productAds", $data
        );
    }

    public function listSponsoredDisplayProductAdsEx($data = null)
    {
        return $this->_operation(
            "display",
            "productAds/extended", $data
        );
    }

    public function getSponsoredProductsAdGroupBidRecommendations($adGroupId)
    {
        return $this->_operation(
            "products",
            "adGroups/{$adGroupId}/bidRecommendations"
        );
    }

    public function getSponsoredProductsKeywordBidRecommendations($keywordId)
    {
        return $this->_operation(
            "products",
            "keywords/{$keywordId}/bidRecommendations"
        );
    }

    public function bulkGetSponsoredProductsKeywordBidRecommendations($adGroupId, $data)
    {
        $data = array(
            "adGroupId" => $adGroupId,
            "keywords" => $data);
        return $this->_operation(
            "products",
            "keywords/bidRecommendations", $data, "POST"
        );
    }

    public function getSponsoredProductsAdGroupKeywordSuggestions($data)
    {
        $adGroupId = $data["adGroupId"];
        unset($data["adGroupId"]);
        return $this->_operation(
            "products",
            "adGroups/{$adGroupId}/suggested/keywords", $data
        );
    }

    public function getSponsoredProductsAdGroupKeywordSuggestionsEx($data)
    {
        $adGroupId = $data["adGroupId"];
        unset($data["adGroupId"]);
        return $this->_operation(
            "products",
            "adGroups/{$adGroupId}/suggested/keywords/extended", $data
        );
    }

    public function getSponsoredProductsAsinKeywordSuggestions($data)
    {
        $asin = $data["asin"];
        unset($data["asin"]);
        return $this->_operation(
            "products",
            "asins/{$asin}/suggested/keywords", $data
        );
    }

    public function bulkGetSponsoredProductsAsinKeywordSuggestions($data)
    {
        return $this->_operation(   
            "products",
            "asins/suggested/keywords", $data, "POST"
        );
    }

    public function requestSponsoredProductsSnapshot($recordType, $data = null)
    {
        return $this->_operation(
            "products",
            "{$recordType}/snapshot", $data, "POST"
        );
    }

    public function getSponsoredProductsSnapshot($snapshotId)
    {
        $req = $this->_operation(
            "products",
            "snapshots/{$snapshotId}"
        );
        if ($req["success"]) {
            $json = json_decode($req["response"], true);
            if ($json["status"] == "SUCCESS") {
                return $this->_download($json["location"]);
            }
        }
        return $req;
    }

    public function requestSponsoredProductsReport($recordType, $data = null)
    {
        return $this->_operation(
            "products",
            "{$recordType}/report", $data, "POST"
        );
    }

    public function getReport($reportId)
    {
        $req = $this->_operation(
            "report",
            "reports/{$reportId}"
        );
        if ($req["success"]) {
            $json = json_decode($req["response"], true);
            if ($json["status"] == "SUCCESS") {
                return $this->_download($json["location"]);
            }
        }
        return $req;
    }

    private function _download($location, $gunzip = false)
    {
        $headers = array();

        if (!$gunzip) {
            /* only send authorization header when not downloading actual file */
            array_push($headers, "Authorization: bearer {$this->config["accessToken"]}");
        }

        if (!is_null($this->profileId)) {
            array_push($headers, "Amazon-Advertising-API-Scope: {$this->profileId}");
        }

        $request = new CurlRequest();
        $request->setOption(CURLOPT_URL, $location);
        $request->setOption(CURLOPT_HTTPHEADER, $headers);
        $request->setOption(CURLOPT_USERAGENT, $this->userAgent);

        if ($gunzip) {
            $response = $this->_executeRequest($request);
            $response["response"] = gzdecode($response["response"]);
            return $response;
        }

        return $this->_executeRequest($request);
    }

    private function _operation($requestType, $interface, $params = array(), $method = "GET")
    {

        $headers = array(
            "Authorization: bearer {$this->config["accessToken"]}",
            "Amazon-Advertising-API-ClientId: {$this->config["clientId"]}",
            "Content-Type: application/json",
            "User-Agent: {$this->userAgent}"
        );

        if (!is_null($this->profileId)) {
            array_push($headers, "Amazon-Advertising-API-Scope: {$this->profileId}");
        }

        $request = new CurlRequest();
        $url = $this->endpoint . "/" 
            . RequestPrefixes::getPrefix($requestType) 
            . $interface;

        $this->requestId = null;
        $data = "";

        switch (strtolower($method)) {
            case "get":
                if (!empty($params)) {
                    $url .= "?";
                    foreach ($params as $k => $v) {
                        $url .= "{$k}=".rawurlencode($v)."&";
                    }
                    $url = rtrim($url, "&");
                }
                break;
            case "put":
            case "post":
            case "delete":
                if (!empty($params)) {
                    $data = json_encode($params);
                    $request->setOption(CURLOPT_POST, true);
                    $request->setOption(CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                $this->_logAndThrow("Unknown verb {$method}.");
        }

        $request->setOption(CURLOPT_URL, $url);
        $request->setOption(CURLOPT_HTTPHEADER, $headers);
        $request->setOption(CURLOPT_USERAGENT, $this->userAgent);
        $request->setOption(CURLOPT_CUSTOMREQUEST, strtoupper($method));
        return $this->_executeRequest($request);
    }

    protected function _executeRequest($request)
    {
        $response = $request->execute();
        $this->requestId = $request->requestId;
        $response_info = $request->getInfo();
        $request->close();

        if ($response_info["http_code"] == 307) {
            /* application/octet-stream */
            return $this->_download($response_info["redirect_url"], true);
        }

        if (!preg_match("/^(2|3)\d{2}$/", $response_info["http_code"])) {
            $requestId = 0;
            $json = json_decode($response, true);
            if (!is_null($json)) {
                if (array_key_exists("requestId", $json)) {
                    $requestId = json_decode($response, true)["requestId"];
                }
            }
            return array("success" => false,
                    "code" => $response_info["http_code"],
                    "response" => $response,
                    "requestId" => $requestId);
        } else {
            return array("success" => true,
                    "code" => $response_info["http_code"],
                    "response" => $response,
                    "requestId" => $this->requestId);
        }
    }

    private function _validateConfig($config)
    {
        if (is_null($config)) {
            $this->_logAndThrow("'config' cannot be null.");
        }

        foreach ($config as $k => $v) {
            if (array_key_exists($k, $this->config)) {
                $this->config[$k] = $v;
            } else {
                $this->_logAndThrow("Unknown parameter '{$k}' in config.");
            }
        }
        return true;
    }

    private function _validateConfigParameters()
    {
        foreach ($this->config as $k => $v) {
            if (is_null($v) && $k !== "accessToken" && $k !== "refreshToken") {
                $this->_logAndThrow("Missing required parameter '{$k}'.");
            }
            switch ($k) {
                case "clientId":
                    if (!preg_match("/^amzn1\.application-oa2-client\.[a-z0-9]{32}$/i", $v)) {
                        $this->_logAndThrow("Invalid parameter value for clientId.");
                    }
                    break;
                case "clientSecret":
                    if (!preg_match("/^[a-z0-9]{64}$/i", $v)) {
                        $this->_logAndThrow("Invalid parameter value for clientSecret.");
                    }
                    break;
                case "accessToken":
                    if (!is_null($v)) {
                        if (!preg_match("/^Atza(\||%7C|%7c).*$/", $v)) {
                            $this->_logAndThrow("Invalid parameter value for accessToken.");
                        }
                    }
                    break;
                case "refreshToken":
                    if (!is_null($v)) {
                        if (!preg_match("/^Atzr(\||%7C|%7c).*$/", $v)) {
                            $this->_logAndThrow("Invalid parameter value for refreshToken.");
                        }
                    }
                    break;
                case "sandbox":
                    if (!is_bool($v)) {
                        $this->_logAndThrow("Invalid parameter value for sandbox.");
                    }
                    break;
            }
        }
        return true;
    }

    private function _setEndpoints()
    {
        try {
            if ($this->config["sandbox"]) { 
                $endpointType = "sandbox";
            } else {
                $endpointType = "prod";
            }
            $this->endpoint = Regions::getEndpoint($this->config["region"], $endpointType);
            $this->tokenUrl = Regions::getEndpoint($this->config["region"], "tokenUrl");
        } catch (Exception $e) {
            $this->_logAndThrow("Invalid region or endpoint type");
        }
        return true;
    }

    private function _logAndThrow($message)
    {
        error_log($message, 0);
        throw new \Exception($message);
    }
}
