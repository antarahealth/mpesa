<?php

namespace Kabangi\Mpesa\Engine;

use Kabangi\Mpesa\Contracts\CacheStore;

/**
 * Class Cache
 *
 * @category PHP
 *
 * @author   David Mjomba <private@gmail.com>
 */
class Cache implements CacheStore
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Cache constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get the cache value.
     *
     * @param      $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        $location = \trim($this->config->get('mpesa.cache_location')) . '/.mpc';

        if (! \is_file($location)) {
            return $default;
        }

        $cache = \unserialize(\file_get_contents($location));
        $cache = $this->cleanCache($cache, $location);

        if (! isset($cache[$key])) {
            return $default;
        }

        return $cache[$key]['v'];
    }

    /**
     * Store an item in the cache.
     *
     * @param string                                     $key
     * @param mixed                                      $value
     * @param \DateTimeInterface|\DateInterval|float|int $minutes
     */
    public function put($key, $value, $minutes = null)
    {
        $directory = \trim($this->config->get('mpesa.cache_location'));
        $location  = $directory . '/.mpc';

        if (! \is_dir($directory)) {
            \mkdir($directory, 0755, true);
        }
        $initial = [];
        if (\is_file($location)) {
            $initial = \unserialize(\file_get_contents($location));
            $initial = $this->cleanCache($initial, $location);
        }

        $minutes = $this->computeExpiryTime($minutes);
        $payload = [$key => ['v' => $value, 't' => $minutes]];
        $payload = \serialize(\array_merge($payload, $initial));

        \file_put_contents($location, $payload);
    }

    public function computeExpiryTime($minutes){
        if(empty($minutes)){
            return null;
        }
        $date = new \DateTime();
        $expiry = $date->modify((int) $minutes.' minutes')->format('Y-m-d H:i:s');
        return $expiry;
    }

    private function cleanCache($initial, $location)
    {
        

        $initial = \array_filter($initial, function ($value) {
            if (! $value['t']) {
                return true;
            }
            $expiry = new \DateTime($value['t']);
            $currentDt = new \DateTime();
            if ($currentDt > $expiry) {
                return false;
            }

            return true;
        });

        \file_put_contents($location, \serialize($initial));

        return $initial;
    }
}
