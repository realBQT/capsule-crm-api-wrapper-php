<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class UnitTest extends TestCase
{
    private $config,$class;

    public function setUp(){
        // config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // client
        $this->class    =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);        
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
     * @covers \BlackQuadrant\CapsuleCRM\CapsuleCRM::resource_splitter
     * @dataProvider resource_splitter
     */
    public function resourceSplitter($payload, $expected_op){
        $op     =   $this->class->resource_splitter($payload);
        $this->assertSame($op, $expected_op);
    }

    /**
     * @test
     * @covers \BlackQuadrant\CapsuleCRM\CapsuleCRM::request_builder
     * @dataProvider request_builder
     */
    public function requestBuilder($resource,$actions,$eop){
        foreach($actions as $key=>$action){
            $op     =   $this->class->request_builder($resource,$action);
            $this->assertEquals($eop[$key],$op);
        }
    }

    public function request_builder(){
        $root     =   'https://api.capsulecrm.com/api/v2/';
        return [
            [
                'resource'  =>  'opportunity',
                'actions'   =>  [
                    'list',
                    'show'
                ],
                'eop'       =>  [
                    [
                        0       =>  'POST',
                        1       =>  $root.'opportunities/filters/results',
                        2       =>  []
                    ],
                    [
                        0       =>  'GET',
                        1       =>  $root.'opportunities/{id}?embed=tags,fields,party,milestone',
                        2       =>  []
                    ]
                ]
            ]
        ];
    }
    public function resource_splitter(){
        return [
            // Resource only
            ['payload' => 'party', 'expected_op'=>['resource'=>'party', 'q'=>['type'=>'']]],
            // Resource & Sub Resource
            ['payload' => 'party:person', 'expected_op'=>['resource'=>'party', 'q'=>['type'=>'person']]],
            // Resource, Sub Resource & one Query
            ['payload' => 'party:person?id=140356573', 'expected_op'=>['resource'=>'party', 'q'=>['id'=>'140356573', 'type'=>'person']]],
            // Resource, Sub Resource & multiple Query
            ['payload' => 'party:person?id=140356573&embed=tags', 'expected_op'=>['resource'=>'party', 'q'=>['id'=>'140356573', 'embed'=>'tags', 'type'=>'person']]],
            // Multi layer resource
            ['payload'=>'entries:7111052:opportunities', 'expected_op'=>['resource'=>'entries','q'=>'opportunities','id'=>'7111052']],
            ['payload'=>'tracks:7111052:opportunities', 'expected_op'=>['resource'=>'tracks','q'=>'opportunities','id'=>'7111052']]
        ];
    }

}

?>