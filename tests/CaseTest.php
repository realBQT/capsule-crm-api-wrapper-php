<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class CaseTest extends TestCase
{
    private $config;

    public function setUp(){
        // config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // client
        $this->client   =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);
    }

    /**
     * @test
     */
    public function apiKeyIsPresent(){
        // API_KEY constant is set
        $this->assertTrue(defined('API_KEY'));
        // API_KEY is not null
        $this->assertNotNull(API_KEY);
        // API_KEY is not empty
        $this->assertNotEmpty(API_KEY);
    }

    /**
     * @test
     * @covers \BlackQuadrant\CapsuleCRM\CapsuleCRM::list
     * @dataProvider list
     */
    public function caseList($resource,$filter,$eop){    
        // Response
        $response   =   $this->client->list($resource);  
        // Response has records
        // fwrite(STDERR, print_r(count($response)." Cases found\n", TRUE));
        $this->assertTrue( count($response) > 1 );
        // Resource Subresource
        $r          =   $this->client->resource_splitter($resource);
        // Response has correct records
        foreach($response as $record){
            $this->assertTrue($this->client->filter($record,$r['q']));
        }
        
    }
    /**
     * Data Provider to list
     */
    public function list(){
        return [
            // All Cases
            [
                'resource'  =>  'kase',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  []
                    ]
                ],
                'eop'   =>  ''
            ],
            // Closed Cases
            [
                'resource'  =>  'kase',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'status',
                                'operator'  =>  'is',
                                'value'     =>  'CLOSED'
                            ]
                        ]
                    ]
                ],
                'eop'   =>  ''
            ]
        ];
    }

    /**
     * @dataProvider case_id
     */
    public function testCaseDetails($case_id){
        // Response
        $response   =   $this->client->show('kase',$case_id);
        // Check veracity
        $this->assertArrayHasKey('status',$response);
    }

    

    public function case_id(){
        return [
            ['id'=>'2326212'],
            ['id'=>'2349921']
        ];
    }
}

?>