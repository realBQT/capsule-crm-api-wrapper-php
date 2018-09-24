<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class ConnectionTest extends TestCase
{
    private $test;

    public function testApiKeyIsPresent(){        
        // API_KEY constant is set
        $this->assertTrue(defined('API_KEY'));
        // API_KEY is not null
        $this->assertNotNull(API_KEY);
        // API_KEY is not empty
        $this->assertNotEmpty(API_KEY);
    }

    public function testConnectionToCapsuleCrm(){
        $client     =   new CapsuleCRM\CapsuleCRM();
        // Test connection with site settings
        $response   =   $client->set_personal_access_token(API_KEY)->connect();
        // Successful Connection
        $this->assertTrue( $response->getStatusCode() === 200 );
        printf($response->getBody()->getContents());
    }
}

?>