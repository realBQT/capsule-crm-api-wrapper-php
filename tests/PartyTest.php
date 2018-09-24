<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class PartyTest extends TestCase
{
    private $config;

    public function setUp(){
        // config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // client
        $this->client   =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);
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
    public function testPartyList(){
        $response   =   $this->client->list('party');
        // Successful Response
        $this->assertTrue( $response->getStatusCode() === 200 );
        // $response   =   $response->getHeaders();
        $response   =   json_decode($response->getBody()->getContents(),1);        
        $this->assertArrayHasKey('parties',$response);
    }

    public function expectedConnectionString(){
        return [
            []
        ];
    }
}

?>