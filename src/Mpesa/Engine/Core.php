<?php

namespace Kabangi\Mpesa\Engine;

use Kabangi\Mpesa\Validation\Validator;    
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Contracts\CacheStore;
use Kabangi\Mpesa\Exceptions\ConfigurationException;
use Kabangi\Mpesa\Exceptions\MpesaException;
use Kabangi\Mpesa\Contracts\ConfigurationStore;
use Kabangi\Mpesa\Contracts\HttpRequest;

/**
 * Class Core.
 *
 * @category PHP
 *
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
     * @var validation rules
     * 
    */
    public $validationRules;

    /**
     * @var HttpRequest curl
     */
    protected $curl;

    /**
     * Core constructor.
     *
     * @param ConfigurationStore $configStore
     * @param CacheStore         $cacheStore
     */
    public function __construct(
        ConfigurationStore $configStore, 
        CacheStore $cacheStore,
        HttpRequest $curl,
        Authenticator $auth
    ){
        $this->config = $configStore;
        $this->cache  = $cacheStore;
        $this->curl = $curl;
        $this->setBaseUrl();
        $this->validator = new Validator();
        self::$instance = $this;
        $this->auth = $auth;
        $this->auth->setEngine($this);
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

    public function setValidationRules($rules){
        $this->validationRules = $rules;
        foreach($this->validationRules as $key => $value){
            $this->validator->add($key,$value);
        }
    }

    private function validateRequestBodyParams($params){
        if ($this->validator->validate($params) == false) {
            $errors = $this->validator->getMessages();
            $finalErrors = [];
            foreach($errors as $err){
                foreach($err as $er){
                    $finalErrors[] = $er->__toString();
                }
            }
            $this->throwApiConfException(\json_encode($finalErrors));
        }
        return true;
    }

    /**
     * Throw an exception that describes a missing param.
     *
     * @param $reason
     *
     * @return ConfigurationException
     */
    public function throwApiConfException($reason){
        throw new ConfigurationException($reason,422);
    }

    /**
     * Get current request time
     * @return 
     */
    public function getCurrentRequestTime(){
        date_default_timezone_set('UTC');
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
    public function makePostRequest($options = [],$appName = 'default'){
        
        $response = $this->request('POST', $options['endpoint'], [
            'headers' => [
                'Authorization: Bearer ' . $this->auth->authenticate($appName),
                'Content-Type: application/json',
            ],
            'body' => $options['body'],
        ]);

        return $response;
    }

    private function request($method,$endpoint,$params){
        // Validate params
        if (!empty($this->validationRules) && isset($params['body'])) {
            $this->validateRequestBodyParams($params['body']);
        }
        $url = $this->baseUrl.$endpoint;
        $this->curl->reset();
        $this->curl->setOption(CURLOPT_URL, $url);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_HEADER, false);
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOption(CURLOPT_HTTPHEADER, $params['headers']);

        if($method === 'POST'){
            $this->curl->setOption(CURLOPT_POST, true);
            $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($params['body']));
        }

        $result = $this->curl->execute();
        $httpCode = $this->curl->getInfo(CURLINFO_HTTP_CODE);

        if( $result === false){ 
            throw new \Exception($this->curl->error());
        }
        if($httpCode != 200){
            throw new MpesaException($result,$httpCode);
        } 
        return json_decode($result); 
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
                'Authorization: Basic ' . $options['token'],
                'Content-Type: application/json',
            ],
        ]);
    }

    /**
     * Compute security credential
     * 
     */
    public function computeSecurityCredential($initiatorPass){
        $pubKeyFile =  __DIR__ . '/../../config/mpesa_public_cert.cer';
        $pubKey = '';
        if(\is_file($pubKeyFile)){
            $pubKey = file_get_contents($pubKeyFile);
        }else{
            throw new \Exception("Please provide a valid public key file");
        }
        openssl_public_encrypt($initiatorPass, $encrypted, $pubKey, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }
}
