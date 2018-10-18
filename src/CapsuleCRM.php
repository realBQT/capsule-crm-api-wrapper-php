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

    /**
     * Universal Getter
     */
    public function get($resource,$filter=['filter'=>['conditions'=>[]]]){
        /**
         * 1.   Resource Splitter
         * 2.   Request Builder
         * 3.   Call
         */
        list($resource,$action)             =   $this->resource_splitter($resource);
        list($method,$endpoint,$payload)    =   $this->request_builder($resource,$action,$filter);
        $response   =   [];
        if($action==='list'){
            $page       =   1;
            $continue   =   false;
            do{
                //  Set Page
                $payload['query']['page']   =   $page;
                $data       =   $this->call_api($method,$endpoint,$payload);
                $continue   =   filter_var($data->getHeaders()['X-Pagination-Has-More'][0], FILTER_VALIDATE_BOOLEAN);
                $records    =   json_decode($data->getBody()->getContents(),1);
                //  Incrementing Page                
                if($continue){
                    $page++;
                }
                // Storing response
                foreach($records[$this->config['resources'][$resource[0]]['plural']] as $key=>$record){
                    $response[]     =   $record;               
                }
    
            }while($continue);
        }
        else if($action==='show'){
            $config     =   $this->config['resources'][$resource[0]]['show'];   
            $data       =   $this->call_api($method,$endpoint,$payload);
            $response   =   json_decode($data->getBody()->getContents(),1);
        }
        return $response;
    }
    
    public function resource_splitter($payload){
        $parts  =   explode(":",$payload);
        if(count($parts)%2==0){
            $action     =   'show';
        }
        else{
            $action     =   'list';
        }
        return [$parts,$action];
    }
    
    public function request_builder($resource,$action,$filter){
        $root           =   array_shift($resource);
        // Config
        $config         =   $this->config['resources'][$root][$action];
        // Method
        $response[0]    =   strtoupper($config['method']);
        // Endpoint
        $response[1]    =   $config['endpoint'];            
        foreach($resource as $key=>$value){
            if(strpos($response[1],'{')!==false){     
                //    Converting resource to plural
                if(!is_numeric($value)){
                    $value      =   $this->config['resources'][$value]['plural'];
                }
                $key            =   '{'.$key.'}';
                if(strpos($response[1],$key)!==false){
                    $response[1]    =   str_replace($key,$value,$response[1]);
                }
            }
        }
        // Payload
        $query_from_endpoint    =   [];
        if (strpos($response[1], '?') !== false) {
            list($response[1],$q) = explode('?',$response[1]);
            // Parsing query
            parse_str($q, $query_from_endpoint);
        }
        $response[2]    =   $this->payload_builder($response[0],$query_from_endpoint,$filter);
        return $response;
    }

    public function payload_builder($method,$query_from_endpoint,$filter){
        $response       =   [];
        if($method==='GET'){
            $response       =   [
                'query'         =>  array_merge($this->config['resources']['settings'], $filter, $query_from_endpoint)
            ];
        }
        else if($method==='POST'){
            $response       =   [
                'body'          =>  json_encode($filter),
                'query'         =>  array_merge($this->config['resources']['settings'], $query_from_endpoint)
            ];
        }
        return $response;
    }
    
    
    /**
     * API Caller
     */
    private function call_api($method,$endpoint,$payload){
        $client     =   new Client();     
        // Defaults
        $default_settings   =   [
            'headers' => [
                'Authorization' =>  'Bearer '.$this->personal_access_token,
                'Accept'        =>  'application/json',
                'Content-Type'  =>  'application/json'
            ]
        ];
        // All Settings
        $all_settings   =   array_merge($default_settings, $payload);
        $response       =   $client->request($method,$endpoint,$all_settings);
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