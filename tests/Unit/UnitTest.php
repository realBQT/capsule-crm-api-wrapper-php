<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;
use Adbar\Dot;

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
        list($resource,$action) =   $resource;
        $op     =   $this->class->request_builder($resource,$action,$filter);
        $this->assertEquals($eop,$op);
    }
    public function request_builder(){
        $root     =   'https://api.capsulecrm.com/api/v2/';
        return [
            [
                'resource'  =>  [['opportunity'],'list'],
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
                    'POST',
                    'https://api.capsulecrm.com/api/v2/opportunities/filters/results',
                    [
                        'body'  =>  '{"filter":{"conditions":[{"field":"milestone","operator":"is","value":"Won"}]}}',
                        'query' =>  [
                            'perPage'   =>  100
                        ]
                    ]
                ]
            ],
            [
                'resource'  =>  [['opportunity','7111052'],'show'],
                'filter'    =>  [],
                'e_op'      =>  [
                    'GET',
                    'https://api.capsulecrm.com/api/v2/opportunities/7111052',
                    [
                        'query' =>  [
                            'perPage'   =>  100,
                            'embed'     =>  'tags,fields,party,milestone'
                        ]
                    ]
                ]
            ]
        ];
    }
    /**
     * @test
     * @covers \BlackQuadrant\CapsuleCRM\CapsuleCRM::get
     * @dataProvider get_data
     */
    public function get($resource,$filter,$success){
        if(empty($filter)){
            $response   =   $this->class->get($resource);
        }
        else{
            $response   =   $this->class->get($resource,$filter);
        }
        $this->assertTrue($this->check_success($response,$success));
    }
    public function get_data(){
        return [
            [
                'resource'  =>  'opportunity',
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
                'success'   =>  [
                    'milestone.name' => 'Won'
                ]
            ],
            [
                'resource'  =>  'opportunity',
                'filter'    =>  [],
                'success'   =>  []
            ],
            [
                'resource'  =>  'opportunity:7111052',
                'filter'    =>  [],
                'success'   =>  [
                    'id'        =>  '7111052',
                    'value.currency'    =>  'NZD'
                ]
            ],
            [
                'resource'  =>  'track:opportunity:7111052',
                'filter'    =>  [],
                'success'   =>  [
                    'direction' =>  'START_DATE'
                ]
            ],
            [
                'resource'  =>  'entry:opportunity:7111052',
                'filter'    =>  [],
                'success'   =>  [
                    'entryAt' =>  '__EXISTS__'
                ]
            ]            
        ];
    }
    
    private function check_success($response,$success){
        foreach($response as $record){
            $record     =   dot($record);
            foreach($success as $key=>$value){
                if(strpos($value,'__')!==false){
                    if($value==='__EXISTS__'){
                        if(!in_array($key, $record)){
                            return false;
                        }
                    }
                }
                else{
                    if($record->get($key)!=$value){
                        return false;
                    } 
                }                
            }
        }
        return true;
    }
}

?>