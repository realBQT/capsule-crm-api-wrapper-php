<?php 

namespace BlackQuadrant\CapsuleCRM;

use GuzzleHttp\Client;

class CapsuleCRM{
    private $personal_access_token = null, $config;

    // Constructor
    public function __construct($personal_access_token=null, $config=null){
        // Personal Access Token
        if(!is_null($personal_access_token)){
            $this->set_personal_access_token($personal_access_token);
        }
        if(!is_null($config)){
            $this->set_config($config);
        }
        // Default Config for Laravel
        else{
            $this->set_config(config('blackquadrant.capsule'));
        }
        return $this;
    }

    /**
     * Personal Access Token :: SET
     */
    public function set_personal_access_token($personal_access_token){
        $this->personal_access_token = $personal_access_token;
        return $this;
    }

    /**
     * Config :: SET
     */
    public function set_config($config){
        $this->config   =   $config;
        return $this;
    }

    /**
     * Check if connection to CapsuleCRM is successful
     */
    public function connect(){
        $config     =   $this->config['settings']['site'];
        return $this->call($config['method'],$config['endpoint']);
    }

    public function show($resource,$id){        
        $config     =   $this->config['resources'][$resource]['show'];   
        $data       =   $this->call($config['method'],str_replace('{id}',$id,$config['endpoint']));

        $response   =   json_decode($data->getBody()->getContents(),1);
        return $response[$resource];
    }
    
    /**
     * Read resources
     */
    public function list($resource,$payload=[]){
        
        $payload    =   array_merge($this->config['resources']['settings'], $payload);
        if (strpos($resource, ':') !== false) {
            list($resource,$sub_resource) = explode(':',$resource);
        }
        else{
            $sub_resource = false;
        }
        
        $config     =   $this->config['resources'][$resource]['list'];
        
        $continue   =   false;
        $page       =   1;        
        $response   =   [];
        do{
            $payload['page']    =   $page;
            $data       =   $this->call($config['method'],$config['endpoint'],$payload);
            $continue   =   filter_var($data->getHeaders()['X-Pagination-Has-More'][0], FILTER_VALIDATE_BOOLEAN);
            $records    =   json_decode($data->getBody()->getContents(),1);
            
            foreach($records[$this->config['resources'][$resource]['plural']] as $record){
                // Filter based on sub_resource
                // Currently used on 
                if($sub_resource && $record['type'] != $sub_resource){
                    continue;
                }
                
                $response[]     =   $record;
            }
            
            if($continue){
                $page++;
            }

        }while($continue);
                
        return $response;
    }

    /**
     * API Caller
     */
    private function call( $method, $api_endpoint, $payload=[] ){
        $client     =   new Client();                
        $method     =   strtoupper($method);
        // Payload and other settings
        $settings   =   [];
        if( $method === 'GET' ){
            $settings   =   [
                'query'     =>  $payload
            ];
        }

        $default_settings   =   [
            'headers' => [
                'Authorization' =>  'Bearer '.$this->personal_access_token,
                'Accept'        =>  'application/json'
            ]
        ];
        $all_settings = array_merge($default_settings, $settings);        
        $response   =   $client->request(strtoupper($method),$api_endpoint, $all_settings);
        return $response;
    }

    /**
     * Personal Access Token :: GET
     */
    public function get_personal_access_token(){
        if(is_null($this->personal_access_token)){
            return "Personal Access Token is not set";
        }
        else{
            return $this->personal_access_token;
        }
    }
}

?>