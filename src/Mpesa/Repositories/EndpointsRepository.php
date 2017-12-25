<?php

namespace Kabangi\Mpesa\Repositories;

use Kabangi\Mpesa\Contracts\ConfigurationStore;
use Kabangi\Mpesa\Exceptions\ConfigurationException;

/**
 * Class EndpointsRepository.
 *
 * @category PHP
 *
 * @author   David Mjomba <Kabangiprivate@gmail.com>
 */
class EndpointsRepository
{

    /**
     * @var string
     */
    protected $baseEndpoint;

    /**
     * @var ConfigurationStore
     */
    private $store;

    /**
     * @var EndpointsRepository
     */
    public static $instance;

    /**
     * EndpointsRepository constructor.
     *
     * @param ConfigurationStore $store
     */
    public function __construct(ConfigurationStore $store)
    {
        $this->store = $store;

        $this->boot();
    }

    /**
     * Initialize the repository.
     */
    private function boot()
    {
        $this->initializeState();
        $this->setInstance();
    }

    /**
     * Validate the current package state.
     *
     * @throws ConfigurationException
     */
    private function initializeState(){
        $apiRoot = $this->store->get('mpesa.apiUrl', '');
        if (substr($apiRoot, strlen($apiRoot) - 1) !== '/') {
            $apiRoot = $apiRoot . '/';
        }
        $this->baseEndpoint  = $apiRoot;
    }

    /**
     * Set the singleton instance.
     */
    private function setInstance()
    {
        self::$instance = $this;
    }

    /**
     * Get the EndpointsRepository instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * Generate the complete endpoint.
     *
     * @param $endpoint
     *
     * @return string
     */
    public static function build($endpoint)
    {
        $instance = self::$instance;

        return $instance->baseEndpoint . $endpoint;
    }
}
