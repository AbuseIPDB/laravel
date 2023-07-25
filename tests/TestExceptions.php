<?php 

namespace AbuseipdbLaravel\Tests; 

use AbuseipdbLaravel\Tests\TestCase;
use AbuseipdbLaravel\Exceptions;
use AbuseipdbLaravel\Facades\AbuseIPDB;

class TestExceptions extends TestCase {


    /* accept type text/html will throw errors */
     public function testInvalidAcceptType(){
        $this->expectException(Exceptions\InvalidAcceptTypeException::class);
        AbuseIPDB::makeRequest('check', [], acceptType: 'text/html');
    }  

    /* endpoint name 'reporting'will throw errors */
     public function testInvalidEndpoint(){
        $this->expectException(Exceptions\InvalidEndpointException::class);
        AbuseIPDB::makeRequest('reporting', []);
    }   

    /* max age in days of 367 will throw error */
     public function testInvalidParameter(){
        $this->expectException(Exceptions\InvalidParameterException::class);
        AbuseIPDB::check('127.0.0.1', 367);
    }  

    public function testTooManyRequests(){

        $this->expectException(Exceptions\TooManyRequestsException::class);
        //double reporting 127.0.0.1 within 15 minutes should not be allowed, will throw error
        AbuseIPDB::report('127.0.0.1', 21);
        AbuseIPDB::report('127.0.0.1', 21);
    }   
        
    public function testUnprocessableContent(){
        $this->expectException(Exceptions\UnprocessableContentException::class);
        //category of 31 will throw an unprocessable error
        AbuseIPDB::report('127.0.0.1', 31);
    }      


    //not sure how to test this issue
   /*  public function testMissingAPIKey(){
        
    } */  

    /* Test will need to be rewritten */
    /* public function testMissingParameter(){
        $this->expectException(Exceptions\MissingParameterException::class);
       
    }    */

    /* This test will need to be written */
    /* public function testPaymentRequired(){
        $this->expectException(Exceptions\PaymentRequiredException::class);
        AbuseIPDB::makeRequest('check-block', ['network' => '127.0.0.1/16', 'maxAgeInDays' => 15]);
    } */

     
}
?>