<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class UnitTest extends TestCase
{
    private $config,$class;

    public function setUp(){
        // Config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // Client
        $this->class    =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);        
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
        $this->assertSame($expected_op, $op);
    }
    public function resource_splitter(){
        return [
            [
                'payload'   =>  'opportunity',
                'eop'       =>  [['opportunity'],'list']
            ],
            [
                'payload'   =>  'opportunity:7111052',
                'eop'       =>  [['opportunity','7111052'],'show']
            ],
            [
                'payload'   =>  'entries:opportunity:7111052',
                'eop'       =>  [['entries','opportunity','7111052'],'list']
            ]
        ];
    }
    /**
     * @test
     * @covers \BlackQuadrant\CapsuleCRM\CapsuleCRM::request_builder
     * @dataProvider request_builder
     */
    public function requestBuilder($resource,$filter,$eop){
        $op     =   $this->class->request_builder($resource,$action);
        $this->assertEquals($eop,$op);
    }

    public function request_builder(){
        $root     =   'https://api.capsulecrm.com/api/v2/';
        return [
            [
                'resource'  =>  ['opportunity'],
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
                ],
                'eop'  =>  [
                    
                ]
            ],
            // [
            //     'resource'  =>  'opportunity:7111052',
            //     'filter'    =>  [],
            //     'e_op'      =>  json_decode('{}',1)
            // ]
        ];
    }
    

}

?>