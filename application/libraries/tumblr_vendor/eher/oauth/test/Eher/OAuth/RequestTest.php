<?php

namespace Eher\OAuth;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequestToUrl()
    {
        $consumer = null;
        $signatureMethod = null;
        $request = null;
        $once = "";
        $timestamp = "";
        $signature = "";
        $expectedUrl = "";

        $consumer = new Consumer('ConsumerKey', 'ConsumerSecret');
        $signatureMethod = new HmacSha1();
        $request = Request::from_consumer_and_token(
            $consumer,
            null,
            "GET",
            "http://www.endpoint.url/",
            array()
        );
        $request->sign_request($signatureMethod, $consumer, null);

        $once = $request->get_parameter('oauth_nonce');
        $timestamp = $request->get_parameter('oauth_timestamp');
        $signature = $request->get_parameter('oauth_signature');
        $expectedUrl = "http://www.endpoint.url/?"
            . "oauth_consumer_key=ConsumerKey"
            . "&oauth_nonce=" . $once
            . "&oauth_signature=" . Util::urlencode_rfc3986($signature)
            . "&oauth_signature_method=HMAC-SHA1"
            . "&oauth_timestamp=" . $timestamp
            . "&oauth_version=1.0";
        $this->assertEquals( $expectedUrl, (string) $request);
    }
}
