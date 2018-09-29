<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;
use Adbar\Dot;

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
    public function testOpportunityList($resource, $filter=[], $e_op){        
        // Response
        $response   =   $this->client->list($resource, $filter);   
        if(empty($response)){
            // No results, no error
            fwrite(STDERR, print_r("\nNo results were found for: ".$resource."->".json_encode($filter)."\n", TRUE));
            $this->markTestIncomplete('');
        }
        else{
            // Response has records
            $this->assertTrue( count($response) > 1 );
            if(count($response))
            // Resource Subresource
            $r          =   $this->client->resource_splitter($resource);
            foreach($response as $record){
                // Response has Resource Type
                $this->assertTrue($this->client->filter($record,$r['q']));
                // Response has e_op
                $dot    =   dot($record);
                foreach($e_op as $key=>$value){
                    $this->assertTrue($dot->get($key)==$value);
                }
            }
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
                ],
                'e_op'  =>  [
                    'milestone.name'    =>  'Won'
                ]
            ],
            // Opportunities Lost
            [
                'resource'  => 'opportunity',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'milestone',
                                'operator'  =>  'is',
                                'value'     =>  'Lost'
                            ]
                        ]
                    ]
                ],
                'e_op'  =>  [
                    'milestone.name'    =>  'Lost'
                ]
            ],
            // Opportunities Prospects
            [
                'resource'  => 'opportunity',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'milestone',
                                'operator'  =>  'is',
                                'value'     =>  'Prospect'
                            ]
                        ]
                    ]
                ],
                'e_op'  =>  [
                    'milestone.name'    =>  'Prospect'
                ]
            ],
            // Opportunities Proposal
            [
                'resource'  => 'opportunity',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'milestone',
                                'operator'  =>  'is',
                                'value'     =>  'Proposal'
                            ]
                        ]
                    ]
                ],
                'e_op'  =>  [
                    'milestone.name'    =>  'Proposal'
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