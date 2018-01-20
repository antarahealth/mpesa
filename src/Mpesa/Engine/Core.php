<?php

namespace Kabangi\Mpesa\Engine;

use Kabangi\Mpesa\Validation\Validator;    
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Contracts\CacheStore;
use Kabangi\Mpesa\Contracts\ConfigurationStore;

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
                'Authorization: Bearer ' . $this->auth->authenticate(),
                'Content-Type: application/json',
            ],
            'body' => $options['body'],
        ]);

        return $response;
    }

    private function request($method,$endpoint,$params){
        $url = $this->baseUrl.$endpoint;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);

        if($method === 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params['body']));
        }

        $result = curl_exec($ch);
        if( $result == false) 
        { 
            trigger_error(curl_error($ch)); 
        } 
        curl_close($ch); 
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
        $key = '';
        if(\is_file($pubKeyFile)){
            $pubKey = file_get_contents($pubKeyFile);
        }else{
            throw new \Exception("Please provide a valid public key file");
        }
        openssl_public_encrypt($initiatorPass, $encrypted, $key, OPENSSL_PKCS1_PADDING);
        return base64_encode($encrypted);
    }
}
