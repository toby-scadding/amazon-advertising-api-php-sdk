<?php
namespace AmazonAdvertisingApi;

require_once "AmazonAdvertisingApi/Client.php";
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $client = null;
    private $return_value = null;
    private $config = array(
        "clientId" => "",
        "clientSecret" => "",
        "refreshToken" => "",
        "accessToken" => "",
        "region" => "",
        "sandbox" => true
    );


    public function setUp() : void
    {
        $this->return_value = array(
            "code" => "200",
            "success" => true,
            "requestId" => "test",
            "response" => "{\"status\":\"TESTING\"}"
        );

        $this->client = $this->getMockBuilder("AmazonAdvertisingApi\Client")
                             ->setConstructorArgs(array($this->config))
                             ->setMethods(array("_executeRequest"))
                             ->getMock();

        $this->client->expects($this->any())
             ->method("_executeRequest")
             ->will($this->returnValue($this->return_value));
    }


    public function testValidateClientId()
    {
        $testConfig = $this->config;
        $testConfig["clientId"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid parameter value for clientId./", strval($expected));
        }
    }

    public function testValidateClientSecret()
    {
        $testConfig = $this->config;
        $testConfig["clientSecret"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid parameter value for clientSecret./", strval($expected));
        }
    }

    public function testValidateRegion()
    {
        $testConfig = $this->config;
        $testConfig["region"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid region or endpoint type./", strval($expected));
        }
    }

    public function testValidateAccessToken()
    {
        $testConfig = $this->config;
        $testConfig["accessToken"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid parameter value for accessToken./", strval($expected));
        }
    }

    public function testValidateRefreshToken()
    {
        $testConfig = $this->config;
        $testConfig["refreshToken"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid parameter value for refreshToken./", strval($expected));
        }
    }

    public function testValidateSandbox()
    {
        $testConfig = $this->config;
        $testConfig["sandbox"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertMatchesRegularExpression("/Invalid parameter value for sandbox./", strval($expected));
        }
    }

    public function testListProfiles()
    {
        $request = $this->client->listProfiles();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetProfile()
    {
        $request = $this->client->getProfile("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateProfiles()
    {
        $request = $this->client->updateProfiles("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsCampaign()
    {
        $request = $this->client->getSponsoredProductsCampaign("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsCampaignEx()
    {
        $request = $this->client->getSponsoredProductsCampaignEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsCampaigns()
    {
        $request = $this->client->createSponsoredProductsCampaigns("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveSponsoredProductsCampaign()
    {
        $request = $this->client->archiveSponsoredProductsCampaign("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsCampaigns()
    {
        $request = $this->client->listSponsoredProductsCampaigns();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsCampaignsEx()
    {
        $request = $this->client->listSponsoredProductsCampaignsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAdGroup()
    {
        $request = $this->client->getSponsoredProductsAdGroup("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAdGroupEx()
    {
        $request = $this->client->getSponsoredProductsAdGroupEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsAdGroups()
    {
        $request = $this->client->createSponsoredProductsAdGroups("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateSponsoredProductsAdGroups()
    {
        $request = $this->client->updateSponsoredProductsAdGroups("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveSponsoredProductsAdGroup()
    {
        $request = $this->client->archiveSponsoredProductsAdGroup("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsAdGroups()
    {
        $request = $this->client->listSponsoredProductsAdGroups();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsAdGroupsEx()
    {
        $request = $this->client->listSponsoredProductsAdGroupsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsBiddableKeyword()
    {
        $request = $this->client->getSponsoredProductsBiddableKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsBiddableKeywordEx()
    {
        $request = $this->client->getSponsoredProductsBiddableKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsBiddableKeywords()
    {
        $request = $this->client->createSponsoredProductsBiddableKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateSponsoredProductsBiddableKeywords()
    {
        $request = $this->client->updateSponsoredProductsBiddableKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveSponsoredProductsBiddableKeyword()
    {
        $request = $this->client->archiveSponsoredProductsBiddableKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsBiddableKeywords()
    {
        $request = $this->client->listSponsoredProductsBiddableKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsBiddableKeywordsEx()
    {
        $request = $this->client->listSponsoredProductsBiddableKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsNegativeKeyword()
    {
        $request = $this->client->getSponsoredProductsNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsNegativeKeywordEx()
    {
        $request = $this->client->getSponsoredProductsNegativeKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsNegativeKeywords()
    {
        $request = $this->client->createSponsoredProductsNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateSponsoredProductsNegativeKeywords()
    {
        $request = $this->client->updateSponsoredProductsNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveSponsoredProductsNegativeKeyword()
    {
        $request = $this->client->archiveSponsoredProductsNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsNegativeKeywords()
    {
        $request = $this->client->listSponsoredProductsNegativeKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsNegativeKeywordsEx()
    {
        $request = $this->client->listSponsoredProductsNegativeKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsCampaignNegativeKeyword()
    {
        $request = $this->client->getSponsoredProductsCampaignNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsCampaignNegativeKeywordEx()
    {
        $request = $this->client->getSponsoredProductsCampaignNegativeKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsCampaignNegativeKeywords()
    {
        $request = $this->client->createSponsoredProductsCampaignNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateSponsoredProductsCampaignNegativeKeywords()
    {
        $request = $this->client->updateSponsoredProductsCampaignNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testRemoveSponsoredProductsCampaignNegativeKeyword()
    {
        $request = $this->client->removeSponsoredProductsCampaignNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsCampaignNegativeKeywords()
    {
        $request = $this->client->listSponsoredProductsCampaignNegativeKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsCampaignNegativeKeywordsEx()
    {
        $request = $this->client->listSponsoredProductsCampaignNegativeKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsProductAd()
    {
        $request = $this->client->getSponsoredProductsProductAd("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsProductAdEx()
    {
        $request = $this->client->getSponsoredProductsProductAdEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateSponsoredProductsProductAds()
    {
        $request = $this->client->createSponsoredProductsProductAds("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateSponsoredProductsProductAds()
    {
        $request = $this->client->updateSponsoredProductsProductAds("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveSponsoredProductsProductAd()
    {
        $request = $this->client->archiveSponsoredProductsProductAd("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsProductAds()
    {
        $request = $this->client->listSponsoredProductsProductAds();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListSponsoredProductsProductAdsEx()
    {
        $request = $this->client->listSponsoredProductsProductAdsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAdGroupBidRecommendations()
    {
        $request = $this->client->getSponsoredProductsAdGroupBidRecommendations("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsKeywordBidRecommendations()
    {
        $request = $this->client->getSponsoredProductsKeywordBidRecommendations("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testBulkGetSponsoredProductsKeywordBidRecommendations()
    {
        $request = $this->client->bulkGetSponsoredProductsKeywordBidRecommendations(123, "test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAdGroupKeywordSuggestions()
    {
        $request = $this->client->getSponsoredProductsAdGroupKeywordSuggestions(
            array("adGroupId" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAdGroupKeywordSuggestionsEx()
    {
        $request = $this->client->getSponsoredProductsAdGroupKeywordSuggestionsEx(
            array("adGroupId" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsAsinKeywordSuggestions()
    {
        $request = $this->client->getSponsoredProductsAsinKeywordSuggestions(
            array("asin" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testBulkGetSponsoredProductsAsinKeywordSuggestions()
    {
        $request = $this->client->bulkGetSponsoredProductsAsinKeywordSuggestions(
            array("asins" => array("ASIN1", "ASIN2")));
        $this->assertEquals($this->return_value, $request);
    }

    public function testRequestSponsoredProductsSnapshot()
    {
        $request = $this->client->requestSponsoredProductsSnapshot("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSponsoredProductsSnapshot()
    {
        $request = $this->client->getSponsoredProductsSnapshot("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testRequestSponsoredProductsReport()
    {
        $request = $this->client->requestSponsoredProductsReport("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetReport()
    {
        $request = $this->client->getReport("test");
        $this->assertEquals($this->return_value, $request);
    }
}
