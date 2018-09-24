<?php 

namespace BlackQuadrant\CapsuleCRM;

use GuzzleHttp\Client;

class CapsuleCRM{
    private $personal_access_token = null;
    CONST API_ROOT = 'https://api.capsulecrm.com/api/v2/';

    /**
     * Personal Access Token :: SET
     */
    public function set_personal_access_token($personal_access_token){
        $this->personal_access_token = $personal_access_token;
        return $this;
    }

    /**
     * Check if connection to CapsuleCRM is successful
     */
    public function connect(){
        return $this->call('GET','site',null);
    }

    /**
     * API Caller
     */
    private function call( $method, $api_endpoint, $payload ){
        $client     =   new Client();
        // Test connection with site settings
        $response   =   $client->request(strtoupper($method),CapsuleCRM::API_ROOT.$api_endpoint, [
            'headers' => [
                'Authorization'=>'Bearer '.$this->personal_access_token   
            ]
        ]);
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