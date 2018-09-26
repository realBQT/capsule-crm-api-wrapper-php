<?php 
require_once __DIR__ . '/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use BlackQuadrant\CapsuleCRM;

class TaskTest extends TestCase
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
    public function testTaskList($resource, $status=[]){    
        // Response
        $response   =   $this->client->list($resource, $status);  
        // Response has records
        // fwrite(STDERR, print_r(count($response)." Tasks found\n", TRUE));
        $this->assertTrue( count($response) > 1 );
        // Resource Subresource
        $r          =   $this->client->resource_splitter($resource);
        // Response has correct records
        foreach($response as $record){
            $this->assertTrue($this->client->filter($record,$r['q']));
        }
        
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

    public function resource_subresource(){
        return [
            // All Tasks
            ['resource'=>'task', 'status'=>['status'=>'OPEN,COMPLETED,PENDING']],
            // Open Tasks
            ['resource'=>'task','status'=>['status'=>'OPEN']],
            // Closed Tasks
            ['resource'=>'task','status'=>['status'=>'COMPLETED']],
            // Pending Tasks
            ['resource'=>'task','status'=>['status'=>'PENDING']]
            // Closed Cases
            // ['kase?status=CLOSED'],
        ];
    }

    public function case_id(){
        return [
            ['id'=>'2326212'],
            ['id'=>'2349921']
        ];
    }
}

?>