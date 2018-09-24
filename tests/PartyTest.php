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
    public function testPartyList($resource){        
        // Response
        $response   =   $this->client->list($resource);
        // Response has records
        $this->assertTrue( count($response) > 1 );
        // Resource:Subresource split
        if (strpos($resource, ':') !== false) {
            list($resource,$sub_resource) = explode(':',$resource);
        }
        else{
            $sub_resource = false;
        }
        // Response has correct records
        if($sub_resource){
            foreach($response as $record){
                $this->assertTrue($record['type'] == $sub_resource);
            }
        }
        
    }

    public function expectedConnectionString(){
        return [
            ['party'],
            ['party:person'],
            ['party:organisation']
        ];
    }
}

?>