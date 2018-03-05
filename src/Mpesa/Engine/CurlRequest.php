<?php
namespace Kabangi\Mpesa\Engine;

use Kabangi\Mpesa\Contracts\HttpRequest;

class CurlRequest implements HttpRequest
{
    private $handle = null;

    public function __construct() {
        $this->handle = curl_init();
    }

    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute() {
        return curl_exec($this->handle);
    }

    public function getInfo($name) {
        return curl_getinfo($this->handle, $name);
    }

    public function error(){
        return curl_error($this->handle);
    }

    public function reset(){
        curl_reset($this->handle);
    }

    public function close() {
        curl_close($this->handle);
    }
}