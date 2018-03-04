<?php

namespace Kabangi\Mpesa\Engine;

use ArrayAccess;
use Kabangi\Mpesa\Contracts\ConfigurationStore;

class Config implements ArrayAccess,ConfigurationStore
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array  $items
     * @return void
     */
    public function __construct($conf = []){
        // Config that comes with the package
        $configFile =  __DIR__ . '/../../config/mpesa.php';
        $defaultConfig = [];
        if(\is_file($configFile)){
            $defaultConfig = require $configFile;
        }
        $defaultConfig = array_merge($defaultConfig,$conf);

        // Config after user edits the config file copied by the system
        $userConfig    =  __DIR__ . '/../../../../../../config/mpesa.php';
        $custom        = [];
        if (\is_file($userConfig)) {
            $custom = require $userConfig;
        }
        
        $this->items = array_merge($defaultConfig, $custom);
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
         return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
     public static function exists($array, $key)
     {
         if ($array instanceof ArrayAccess) {
             return $array->offsetExists($key);
         }
 
         return array_key_exists($key, $array);
     }

     /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null){
        $key = str_replace("mpesa.","",$key);
        $array = $this->items;
        if (! static::accessible($array)) {
            return $this->value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?: $this->value($default);
        }
 
        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $this->value($default);
            }
        }
 
        return $array;
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    
    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

     /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function value($value){
        return $value instanceof Closure ? $value() : $value;
    }
}
