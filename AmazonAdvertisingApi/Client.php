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

    private $applicationVersion = null;
    private $userAgent = null;
    private $endpoint = null;
    private $tokenUrl = null;
    private $requestId = null;
    private $endpoints = null;
    private $versionStrings = null;

    public $profileId = null;

    public function __construct($config)
    {
        $regions = new Regions();
        $this->endpoints = $regions->endpoints;

        $versions = new Versions();
        $this->versionStrings = $versions->versionStrings;

        $requestPrefixes = new RequestPrefixes();
        $this->prefixes = $requestPrefixes->requestPrefixStrings

        $this->applicationVersion = $this->versionStrings["applicationVersion"];
        $this->userAgent = "AdvertisingAPI PHP Client Library v{$this->applicationVersion}";

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
        return $this->_operation("profiles");
    }

    public function registerProfile($data)
    {
        return $this->_operation(
            "{$this->prefixes["profile"]}profiles/register", $data, "PUT"
        );
    }

    public function registerProfileStatus($profileId)
    {
        return $this->_operation(
            "{$this->prefixes["profile"]}profiles/register/{$profileId}/status"
        );
    }

    public function getProfile($profileId)
    {
        return $this->_operation(
            "{$this->prefixes["profile"]}profiles/{$profileId}"
        );
    }

    public function updateProfiles($data)
    {
        return $this->_operation(
            "{$this->prefixes["profile"]}profiles", $data, "PUT"
        );
    }

    public function getSponsoredProductsCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns/{$campaignId}"
        );
    }

    public function getSponsoredProductsCampaignEx($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns/extended/{$campaignId}"
        );
    }

    public function createSponsoredProductsCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns", $data, "POST"
        );
    }

    public function updateSponsoredProductsCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsCampaigns($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns", $data
        );
    }

    public function listSponsoredProductsCampaignsEx($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}campaigns/extended", $data
        );
    }

    public function getSponsoredDisplayCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns/{$campaignId}"
        );
    }

    public function getSponsoredDisplayCampaignEx($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns/extended/{$campaignId}"
        );
    }

    public function createSponsoredDisplayCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns", $data, "POST"
        );
    }

    public function updateSponsoredDisplayCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredDisplayCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredDisplayCampaigns($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns", $data
        );
    }

    public function listSponsoredDisplayCampaignsEx($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}campaigns/extended", $data
        );
    }

    public function getSponsoredBrandsCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}campaigns/{$campaignId}"
        );
    }

    public function createSponsoredBrandsCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}campaigns", $data, "POST"
        );
    }

    public function updateSponsoredBrandsCampaigns($data)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}campaigns", $data, "PUT"
        );
    }

    public function archiveSponsoredBrandsCampaign($campaignId)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}campaigns/{$campaignId}", null, "DELETE"
        );
    }

    public function listSponsoredBrandsCampaigns($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}campaigns", $data
        );
    }

    public function getSponsoredProdcutsAdGroup($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups/{$adGroupId}"
        );
    }

    public function getSponsoredProdcutsAdGroupEx($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups/extended/{$adGroupId}"
        );
    }

    public function createSponsoredProdcutsAdGroups($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups", $data, "POST"
        );
    }

    public function updateSponsoredProdcutsAdGroups($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups", $data, "PUT"
        );
    }

    public function archiveSponsoredProdcutsAdGroup($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups/{$adGroupId}", null, "DELETE"
        );
    }

    public function listSponsoredProdcutsAdGroups($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups", $data
        );
    }

    public function listSponsoredProdcutsAdGroupsEx($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}adGroups/extended", $data
        );
    }

    public function getSponsoredDisplayAdGroup($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups/{$adGroupId}"
        );
    }

    public function getSponsoredDisplayAdGroupEx($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups/extended/{$adGroupId}"
        );
    }

    public function createSponsoredDisplayAdGroups($data)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups", $data, "POST"
        );
    }

    public function updateSponsoredDisplayAdGroups($data)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups", $data, "PUT"
        );
    }

    public function archiveSponsoredDisplayAdGroup($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups/{$adGroupId}", null, "DELETE"
        );
    }

    public function listSponsoredDisplayAdGroups($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups", $data
        );
    }

    public function listSponsoredDisplayAdGroupsEx($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["display"]}adGroups/extended", $data
        );
    }

    public function getSponsoredBrandsAdGroup($adGroupId)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}adGroups/{$adGroupId}"
        );
    }

    public function listSponsoredBrandsAdGroups($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}adGroups", $data
        );
    }

    public function getSponsoredProductsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords/{$keywordId}"
        );
    }

    public function getSponsoredProductsBiddableKeywordEx($keywordId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords/extended/{$keywordId}"
        );
    }

    public function createSponsoredProductsBiddableKeywords($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords", $data, "POST"
        );
    }

    public function updateSponsoredProductsBiddableKeywords($data)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords", $data, "PUT"
        );
    }

    public function archiveSponsoredProductsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredProductsBiddableKeywords($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords", $data
        );
    }

    public function listSponsoredProductsBiddableKeywordsEx($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["products"]}keywords/extended", $data
        );
    }

    public function getSponsoredBrandsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}keywords/{$keywordId}"
        );
    }

    public function createSponsoredBrandsBiddableKeywords($data)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}keywords", $data, "POST"
        );
    }

    public function updateSponsoredBrandsBiddableKeywords($data)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}keywords", $data, "PUT"
        );
    }

    public function archiveSponsoredBrandsBiddableKeyword($keywordId)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}keywords/{$keywordId}", null, "DELETE"
        );
    }

    public function listSponsoredBrandsBiddableKeywords($data = null)
    {
        return $this->_operation(
            "{$this->prefixes["brands"]}keywords", $data
        );
    }

    public function getNegativeKeyword($keywordId)
    {
        return $this->_operation("negativeKeywords/{$keywordId}");
    }

    public function getNegativeKeywordEx($keywordId)
    {
        return $this->_operation("negativeKeywords/extended/{$keywordId}");
    }

    public function createNegativeKeywords($data)
    {
        return $this->_operation("negativeKeywords", $data, "POST");
    }

    public function updateNegativeKeywords($data)
    {
        return $this->_operation("negativeKeywords", $data, "PUT");
    }

    public function archiveNegativeKeyword($keywordId)
    {
        return $this->_operation("negativeKeywords/{$keywordId}", null, "DELETE");
    }

    public function listNegativeKeywords($data = null)
    {
        return $this->_operation("negativeKeywords", $data);
    }

    public function listNegativeKeywordsEx($data = null)
    {
        return $this->_operation("negativeKeywords/extended", $data);
    }

    public function getCampaignNegativeKeyword($keywordId)
    {
        return $this->_operation("campaignNegativeKeywords/{$keywordId}");
    }

    public function getCampaignNegativeKeywordEx($keywordId)
    {
        return $this->_operation("campaignNegativeKeywords/extended/{$keywordId}");
    }

    public function createCampaignNegativeKeywords($data)
    {
        return $this->_operation("campaignNegativeKeywords", $data, "POST");
    }

    public function updateCampaignNegativeKeywords($data)
    {
        return $this->_operation("campaignNegativeKeywords", $data, "PUT");
    }

    public function removeCampaignNegativeKeyword($keywordId)
    {
        return $this->_operation("campaignNegativeKeywords/{$keywordId}", null, "DELETE");
    }

    public function listCampaignNegativeKeywords($data = null)
    {
        return $this->_operation("campaignNegativeKeywords", $data);
    }

    public function listCampaignNegativeKeywordsEx($data = null)
    {
        return $this->_operation("campaignNegativeKeywords/extended", $data);
    }

    public function getProductAd($productAdId)
    {
        return $this->_operation("productAds/{$productAdId}");
    }

    public function getProductAdEx($productAdId)
    {
        return $this->_operation("productAds/extended/{$productAdId}");
    }

    public function createProductAds($data)
    {
        return $this->_operation("productAds", $data, "POST");
    }

    public function updateProductAds($data)
    {
        return $this->_operation("productAds", $data, "PUT");
    }

    public function archiveProductAd($productAdId)
    {
        return $this->_operation("productAds/{$productAdId}", null, "DELETE");
    }

    public function listProductAds($data = null)
    {
        return $this->_operation("productAds", $data);
    }

    public function listProductAdsEx($data = null)
    {
        return $this->_operation("productAds/extended", $data);
    }

    public function getAdGroupBidRecommendations($adGroupId)
    {
        return $this->_operation("adGroups/{$adGroupId}/bidRecommendations");
    }

    public function getKeywordBidRecommendations($keywordId)
    {
        return $this->_operation("keywords/{$keywordId}/bidRecommendations");
    }

    public function bulkGetKeywordBidRecommendations($adGroupId, $data)
    {
        $data = array(
            "adGroupId" => $adGroupId,
            "keywords" => $data);
        return $this->_operation("keywords/bidRecommendations", $data, "POST");
    }

    public function getAdGroupKeywordSuggestions($data)
    {
        $adGroupId = $data["adGroupId"];
        unset($data["adGroupId"]);
        return $this->_operation("adGroups/{$adGroupId}/suggested/keywords", $data);
    }

    public function getAdGroupKeywordSuggestionsEx($data)
    {
        $adGroupId = $data["adGroupId"];
        unset($data["adGroupId"]);
        return $this->_operation("adGroups/{$adGroupId}/suggested/keywords/extended", $data);
    }

    public function getAsinKeywordSuggestions($data)
    {
        $asin = $data["asin"];
        unset($data["asin"]);
        return $this->_operation("asins/{$asin}/suggested/keywords", $data);
    }

    public function bulkGetAsinKeywordSuggestions($data)
    {
        return $this->_operation("asins/suggested/keywords", $data, "POST");
    }

    public function requestSnapshot($recordType, $data = null)
    {
        return $this->_operation("{$recordType}/snapshot", $data, "POST");
    }

    public function getSnapshot($snapshotId)
    {
        $req = $this->_operation("snapshots/{$snapshotId}");
        if ($req["success"]) {
            $json = json_decode($req["response"], true);
            if ($json["status"] == "SUCCESS") {
                return $this->_download($json["location"]);
            }
        }
        return $req;
    }

    public function requestReport($recordType, $data = null)
    {
        return $this->_operation("{$recordType}/report", $data, "POST");
    }

    public function getReport($reportId)
    {
        $req = $this->_operation("reports/{$reportId}");
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

    private function _operation($interface, $params = array(), $method = "GET")
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
        $url = "{$this->endpoint}/{$interface}";
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
        /* check if region exists and set api/token endpoints */
        if (array_key_exists(strtolower($this->config["region"]), $this->endpoints)) {
            $region_code = strtolower($this->config["region"]);
            if ($this->config["sandbox"]) {
                $this->endpoint = "https://{$this->endpoints[$region_code]["sandbox"]}/";
            } else {
                $this->endpoint = "https://{$this->endpoints[$region_code]["prod"]}/";
            }
            $this->tokenUrl = $this->endpoints[$region_code]["tokenUrl"];
        } else {
            $this->_logAndThrow("Invalid region.");
        }
        return true;
    }

    private function _logAndThrow($message)
    {
        error_log($message, 0);
        throw new \Exception($message);
    }
}
