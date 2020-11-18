<?php
namespace AmazonAdvertisingApi;

require_once "Client.php";

$config = array(
    "clientId" => "amzn1.application-oa2-client.bacaf4daf8e94ff3961a116e20fd5d57",
    "clientSecret" => "50649c45202270b00758edc4edc502994461f6bc7670900c0129f85e3af53c3b",
    "region" => 'eu',
    "accessToken" => null,
    "refreshToken" => "Atzr|IwEBII9mW3hNUBu9M0a19B4jVovVEno9aZHkFjghGALbHZmX0qIhnoKh8rPRqKmxzfAFGsPHg9spjfI_2gKQZpPtUHUGdtO-T2Dh9mqbb8XJeETGSlG-6eEa5yM_dNFRZV-CVxvVEKwyfngah9anTQfBoW8c7p0sYX4PmvJLPorGbV525dlodteQyVsKlwHjoNNhOZe8d1gm_RsKzpRPcLAXp1-cBkZIhC4G_2MJfoBhau4UMEE4reD5bwax_62jgRJgZhmMygrbaieONzfrkpRlOoGVw5EZEf0cpDJ1ll_afFoAuupbGw8BHv4PVVFQccNsfHIo0UFKrbhSfonQJAbX2UdsmeHAfeQkr5iNPJBBMz6G_4YV8xuzSQZPDDiIU7bdLVyJmiKEUhCUNbWfvSTzePV7EI0iLR8S90xYLxiPCT-V5pYRG1CACq7kVPgZWg4xJm6TqvyKoJtB-_JQKLfMuakMcE_DSdYia1X7y1eC4l1doRePU045v337aMxWPKZefFZsCJhhd6Rqy-w3bKXNYS6FOjPSlpLptLu_-1GDl5TGJScnHvElnb3dlQMJy0RpZwTMN5P2P7bUNNTKTBAQsE5z",
    "sandbox" => true
);
$client = new Client($config);