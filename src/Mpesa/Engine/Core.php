<?php

namespace Kabangi\Mpesa\Engine;

use GuzzleHttp\ClientInterface;
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
     * @var ClientInterface
     */
    public $client;

    /**
     * @var Authenticator
     */
    public $auth;

    /**
     * @var Validator
    */
    public $validator;

    /**
     * Core constructor.
     *
     * @param ClientInterface    $client
     * @param ConfigurationStore $configStore
     * @param CacheStore         $cacheStore
     */
    public function __construct(ClientInterface $client, ConfigurationStore $configStore, CacheStore $cacheStore)
    {
        $this->config = $configStore;
        $this->cache  = $cacheStore;
        $this->validator = new Validator();
        $this->setClient($client);

        $this->initialize();

        self::$instance = $this;
    }

    /**
     * Initialize the Core process.
     */
    private function initialize()
    {
        new EndpointsRepository($this->config);
        $this->auth = new Authenticator($this);
    }

    /**
     * Set http client.
     *
     * @param ClientInterface $client
     **/
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
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
    * Make a post request
    *
    * @param Array $options
    *
    * @return mixed|\Psr\Http\Message\ResponseInterface
    **/
    public function makePostRequest($options = []){
        $response = $this->client->request('POST', $options['endpoint'], [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->auth->authenticate(),
                'Content-Type'  => 'application/json',
            ],
            'json' => $options['body'],
        ]);

        return \json_decode($response->getBody());
    }

    /**
    * Make a GET request
    *
    * @param Array $options
    *
    * @return mixed|\Psr\Http\Message\ResponseInterface
    **/
    public function makeGetRequest($options = []){
        return $this->client->request('GET', $options['endpoint'], [
            'headers' => [
                'Authorization' => 'Basic ' . $options['token'],
                'Content-Type'  => 'application/json',
            ],
        ]);
    }
}
