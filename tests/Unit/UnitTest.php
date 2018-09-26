<?php 
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class UnitTest extends TestCase
{
    private $config;

    public function setUp(){
        // config
        $this->config   =   require \hiqdev\composer\config\Builder::path('api');
        // client
        $this->class    =   new CapsuleCRM\CapsuleCRM(API_KEY,$this->config);
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
     * @dataProvider payload
     */
    public function testPayloadSplitter($payload, $expected_op){
        $op     =   $this->class->payload_splitter($payload);
        $this->assertSame($op, $expected_op);
    }

    public function payload(){
        return [
            // Resource only
            ['payload' => 'party', 'expected_op'=>['resource'=>'party', 'subresource'=>'', 'q'=>'']],
            // Resource & Sub Resource
            ['payload' => 'party:person', 'expected_op'=>['resource'=>'party', 'subresource'=>'person', 'q'=>'']],
            // Resource, Sub Resource & one Query
            ['payload' => 'party:person?id=140356573', 'expected_op'=>['resource'=>'party', 'subresource'=>'person', 'q'=>['id'=>'140356573']]]
            // Resource, Sub Resource & multiple Query
        ];
    }

}

?>