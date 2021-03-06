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
     * @dataProvider resource_subresource
     */
    public function testPartyList($resource){        
        // Response
        $response   =   $this->client->list($resource);        
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
     * @dataProvider party_id
     */
    public function testPartyDetails($party_id, $expected_output){
        // Response
        $response   =   $this->client->show('party',$party_id);
        // Check veracity
        $this->assertTrue($response==json_decode($expected_output, 1), json_encode($response));
    }

    /**
     * @dataProvider filter_organisation
     */
    public function testOrganisationList($resource,$filter){
        // Response
        $response   =   $this->client->list($resource,$filter);
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

    /**
     * @dataProvider filter_people
     */
    public function testPersonList($resource,$filter){
        // Response
        $response   =   $this->client->list($resource,$filter);
        // var_dump($response);die();
        // Response has records
        // fwrite(STDERR, print_r(count($response)." Cases found\n", TRUE));
        $this->assertTrue( count($response) > 1 );
        // Resource Subresource
        $r          =   $this->client->resource_splitter('party:person');
        // Response has correct records
        foreach($response as $record){
            $this->assertTrue($this->client->filter($record,$r['q']));
        }
        
    }
    
    
    
    
    
    /**
     * Data Providers
     */
    public function filter_people(){
        return [
            // Filter people by type
            [
                'resource'  =>  'people',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'         =>  'type',
                                'operator'      =>  'is',
                                'value'         =>  'person'
                            ]
                        ]
                    ]
                ]
            ],  
            // Filter people by tag
            [
                'resource'  =>  'people',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'     =>  'type',
                                'operator'  =>  'is',
                                'value'     =>  'person'
                            ],
                            [
                                'field'     =>  'tag',
                                'operator'  =>  'is',
                                'value'     =>  'Employee'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    public function filter_organisation(){
        return [
            [
                'resource'  =>  'organisation',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'         =>  'type',
                                'operator'      =>  'is',
                                'value'         =>  'organisation'
                            ]
                        ]
                    ]
                ]
            ],
            // Filter organisations by tag
            [
                'resource'  =>  'organisation',
                'filter'    =>  [
                    'filter'    =>  [
                        'conditions'    =>  [
                            [
                                'field'         =>  'type',
                                'operator'      =>  'is',
                                'value'         =>  'organisation'
                            ],
                            [
                                'field'     =>  'tag',
                                'operator'  =>  'is',
                                'value'     =>  'Existing customer'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public function resource_subresource(){
        return [
            ['party'],
            ['party:person'],
            ['party:organisation']
        ];
    }

    public function party_id(){
        return [
            // Person
            ['id'=>'140356573', 'op'=>'{"id":140356573,"owner":{"id":342008,"username":"gary","name":"Gary Singh","pictureURL":"https://facehub.appspot.com/default/person?text=GS&size=100"},"type":"person","about":null,"title":null,"firstName":"Gary","lastName":"Singh","jobTitle":null,"createdAt":"2017-05-16T00:08:08Z","updatedAt":"2018-08-29T04:11:12Z","organisation":null,"lastContactedAt":"2018-08-29T04:11:12Z","pictureURL":"https://facehub.appspot.com/default/person?text=GS&size=100","phoneNumbers":[],"addresses":[],"emailAddresses":[{"id":284671008,"type":null,"address":"gary@thecleancrew.co.nz"}],"websites":[]}'],
            // Organisation
            ['id'=>'140359963', 'op'=>'{"id":140359963,"owner":null,"type":"organisation","about":null,"name":"Impact Alarms Limited","createdAt":"2017-05-16T01:25:18Z","updatedAt":"2018-09-02T23:24:48Z","lastContactedAt":null,"pictureURL":"https://d365sd3k9yw37.cloudfront.net/a/1537517875/theme/default/images/org_avatar.svg","phoneNumbers":[],"addresses":[],"emailAddresses":[],"websites":[]}']
        ];
    }
}

?>