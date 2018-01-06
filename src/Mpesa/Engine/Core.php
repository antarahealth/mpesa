<?php

namespace Kabangi\Mpesa\Engine;

use Sirius\Validation\Validator;    
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Contracts\CacheStore;
use Kabangi\Mpesa\Contracts\ConfigurationStore;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Core.
 *
 * @category PHP
 *
 * @author   David Mjomba <Kabangiprivate@gmail.com>
 */
class Core
{
    /**
     * @var ConfigurationStore
     */
    public $config;

    /**
     * @var CacheStore
     */
    public $cache;

    /**
     * @var Core
     */
    public static $instance;

    /**
     * @var Authenticator
     */
    public $auth;

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var Validator
    */
    public $validator;

    /**
     * Core constructor.
     *
     * @param ConfigurationStore $configStore
     * @param CacheStore         $cacheStore
     */
    public function __construct(ConfigurationStore $configStore, CacheStore $cacheStore)
    {
        $this->config = $configStore;
        $this->cache  = $cacheStore;
        $this->validator = new Validator();

        $this->initialize();

        self::$instance = $this;
    }

    /**
     * Initialize the Core process.
     */
    private function initialize()
    {
        $this->setBaseUrl();
        $this->auth = new Authenticator($this);
    }

    /**
     * Validate the current package state.
     */
    private function setBaseUrl(){
        $apiRoot = $this->config->get('mpesa.apiUrl', '');
        if (substr($apiRoot, strlen($apiRoot) - 1) !== '/') {
            $apiRoot = $apiRoot . '/';
        }
        $this->baseUrl  = $apiRoot;
    }

    public function addValidationRules($rules){
        foreach($rules as $key => $value){
            $this->validator->add($key,$value);
        }
    }

    public function validateParams($params){
        if ($this->validator->validate($params)) {
            return true;
        }else{
            $errors = $this->validator->getMessages();
            $finalErrors = [];
            foreach($errors as $err){
                foreach($err as $er){
                    $finalErrors[] = $er->__toString();
                }
                
            }
            return $finalErrors;
        }
    }

    /**
     * Get current request time
     * @return 
     */
    public function getCurrentRequestTime(){
        $date = new \DateTime();
        return $date->format('YmdHis');
    }

    /**
    * Make a post request
    *
    * @param Array $options
    *
    * @return mixed|\Psr\Http\Message\ResponseInterface
    **/
    public function makePostRequest($options = []){
        $response = $this->request('POST', $options['endpoint'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->auth->authenticate(),
                'Content-Type'  => 'application/json',
            ],
            $options['body'],
        ]);

        return \json_decode($response->getBody());
    }

    private function request($method,$endpoint,$headers,$body){
        $url = $this->baseUrl.$endpoint;
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 200,
            CURLOPT_HTTPHEADER => $headers
        ];

        if($method === 'POST'){
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($body);
        }

        curl_setopt_array($ch, $options);
        if( ! $result = curl_exec($ch)) 
        { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch); 
        return $result; 
    }

    /**
    * Make a GET request
    *
    * @param Array $options
    *
    * @return mixed|\Psr\Http\Message\ResponseInterface
    **/
    public function makeGetRequest($options = []){
        return $this->request('GET', $options['endpoint'], [
            'headers' => [
                'Authorization' => 'Basic ' . $options['token'],
                'Content-Type'  => 'application/json',
            ],
        ]);
    }
}
