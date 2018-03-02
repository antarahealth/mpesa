<?php

namespace Kabangi\Mpesa\Auth;

use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Exceptions\ErrorException;
use Kabangi\Mpesa\Exceptions\ConfigurationException;

/**
 * Class Authenticator.
 *
 * @category PHP
 *
 * @author   David Mjomba <Kabangiprivate@gmail.com>
 */
class Authenticator
{
    /**
     * Cache key.
     */
    const AC_TOKEN = 'MP_AC_T';

    /**
     * @var string
     */
    protected $endpoint = 'oauth/v1/generate?grant_type=client_credentials';

    /**
     * @var Core
     */
    protected $engine;

    /**
     * @var Authenticator
     */
    protected static $instance;

    /**
     * Authenticator constructor.
     *
     * @param Core $core
     */
    public function __construct(){
        self::$instance = $this;
    }

    public function setEngine(Core $core){
        $this->engine   = $core;
    }

    /**
     * Get the access token required to transact.
     *
     * @return mixed
     *
     * @throws ConfigurationException
     */
    public function authenticate()
    {
        // if ($token = $this->engine->cache->get(self::AC_TOKEN)) {
        //     return $token;
        // }

        try {
            $response = $this->makeRequest();
            /// $this->saveCredentials($response);
            if(!empty($response->errorCode)){
                throw new \Exception(json_encode($response));
            }
            return $response->access_token;
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            
            throw $this->generateException($message);
        }
    }

    /**
     * Throw a contextual exception.
     *
     * @param $reason
     *
     * @return ErrorException|ConfigurationException
     */
    private function generateException($reason)
    {
        switch (\strtolower($reason)) {
            case 'bad request: invalid credentials':
                return new ConfigurationException('Invalid consumer key and secret combination');
            default:
                return new ErrorException($reason);
        }
    }

    /**
     * Generate the base64 encoded authorization key.
     *
     * @return string
     */
    private function generateCredentials()
    {
        $key    = $this->engine->config->get('mpesa.consumer_key');
        $secret = $this->engine->config->get('mpesa.consumer_secret');
        return \base64_encode($key . ':' . $secret);
    }

    /**
     * Initiate the authentication request.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function makeRequest()
    {
        $credentials = $this->generateCredentials();
        
        return $this->engine->makeGetRequest([
            'endpoint' => $this->endpoint,
            'token' => $credentials
        ]);
    }

    /**
     * Store the credentials in the cache.
     *
     * @param $credentials
     */
    private function saveCredentials($credentials)
    {
        $ttl = ($credentials->expires_in / 60) - 2;

        $this->engine->cache->put(self::AC_TOKEN, $credentials->access_token, $ttl);
    }
}
