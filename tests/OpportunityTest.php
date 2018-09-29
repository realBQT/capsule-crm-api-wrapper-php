<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class OpportunityTest extends TestCase
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
     * @dataProvider resource_subresource
     */
    public function testOpportunityList($resource, $filter=[]){        
        // Response
        $response   =   $this->client->list($resource, $filter); 
        echo json_encode($response);die();
        // Response has records
        $this->assertTrue( count($response) > 1 );
        // Resource Subresource
        $r          =   $this->client->resource_splitter($resource);
        // Response has correct records
        foreach($response as $record){
            $this->assertTrue($this->client->filter($record,$r['q']));
        }
    }

    /**
     * @dataProvider opportunity_id
     */
    public function testOpportunityDetails($opportunity_id){
        // Response
        $response   =   $this->client->show('opportunity',$opportunity_id);
        // Check veracity
        $this->assertArrayHasKey('createdAt',$response);
    }

    public function resource_subresource(){
        return [
            // Opportunities Won
            [
                'resource'  => 'opportunity',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'milestone',
                                'operator'  =>  'is',
                                'value'     =>  'Won'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function opportunity_id(){
        return [
            // Person
            ['id'=>'7120444'],
            // Organisation
            ['id'=>'7124064']
        ];
    }

}

?>