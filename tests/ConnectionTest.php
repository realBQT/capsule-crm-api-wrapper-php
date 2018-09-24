<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class ConnectionTest extends TestCase
{
    private $config;

    public function setUp(){
        // Setting config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
    }

    public function testApiKeyIsPresent(){
        // API_KEY constant is set
        $this->assertTrue(defined('API_KEY'));
        // API_KEY is not null
        $this->assertNotNull(API_KEY);
        // API_KEY is not empty
        $this->assertNotEmpty(API_KEY);
    }

    /**
     * @dataProvider expectedConnectionString
     */
    public function testConnectionToCapsuleCrm( $expected_connection_string ){
        $client     =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);
        // Test connection with site settings
        $response   =   $client->connect();
        // Successful Connection
        $this->assertTrue( $response->getStatusCode() === 200 );
        // Check for string response
        $response   =   $response->getBody()->getContents();
        $this->assertJsonStringEqualsJsonString( $response, $expected_connection_string );
    }

    public function expectedConnectionString(){
        return [
            ['{"site":{"url":"https://thecleancrew-co-nz.capsulecrm.com","subdomain":"thecleancrew-co-nz","name":"thecleancrew.co.nz"}}']
        ];
    }
}

?>