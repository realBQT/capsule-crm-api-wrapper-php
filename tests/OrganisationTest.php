<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class OrganisationTest extends TestCase
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

    public function testOrganisationList(){
        // Response
        $response   =   $this->client->list('organisation',['filter'=>['conditions'=>[['field'=>'type','operator'=>'is','value'=>'organisation']]]]);
        // var_dump($response);die();
        // Response has records
        // fwrite(STDERR, print_r(count($response)." Cases found\n", TRUE));
        $this->assertTrue( count($response) > 1 );
        // Resource Subresource
        $r          =   $this->client->resource_splitter('party:organisation');
        // Response has correct records
        foreach($response as $record){
            $this->assertTrue($this->client->filter($record,$r['q']));
        }
        
    }
}

?>