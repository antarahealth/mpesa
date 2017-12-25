<?php

namespace Kabangi\Mpesa\Laravel;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider as RootProvider;
use Kabangi\Mpesa\C2B\Register;
use Kabangi\Mpesa\C2B\Simulate;
use Kabangi\Mpesa\B2C\Pay;
use Kabangi\Mpesa\B2B\Pay as B2BPay;
use Kabangi\Mpesa\AccountBalance\Balance;
use Kabangi\Mpesa\TransactionStatus\TransactionStatus;
use Kabangi\Mpesa\LipaNaMpesaOnline\STKPush;
use Kabangi\Mpesa\LipaNaMpesaOnline\STKStatusQuery;
use Kabangi\Mpesa\Reversal\Reversal;
use Kabangi\Mpesa\Contracts\CacheStore;
use Kabangi\Mpesa\Contracts\ConfigurationStore;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Laravel\Stores\LaravelCache;
use Kabangi\Mpesa\Laravel\Stores\LaravelConfig;

class ServiceProvider extends RootProvider{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../../config/mpesa.php' => config_path('mpesa.php')
        ]);
    }

    /**
     * Registrar the application services.
     */
    public function register(){
        $this->bindInstances();

        $this->registerFacades();
    }

    private function bindInstances(){
        $this->app->bind(ConfigurationStore::class, LaravelConfig::class);
        $this->app->bind(CacheStore::class, LaravelCache::class);
        $this->app->bind(Core::class, function ($app) {
            $config = $app->make(ConfigurationStore::class);
            $cache = $app->make(CacheStore::class);

            return new Core(new Client, $config, $cache);
        });
    }

    private function registerFacades(){
        $this->app->bind('mp_stk_push', function () {
            return $this->app->make(STKPush::class);
        });

        $this->app->bind('mp_stk_status_query', function () {
            return $this->app->make(STKStatusQuery::class);
        });

        $this->app->bind('mp_C2B_register', function () {
            return $this->app->make(Register::class);
        });

        $this->app->bind('mp_C2B_simulate', function () {
            return $this->app->make(Simulate::class);
        });

        $this->app->bind('mp_B2C_pay', function () {
            return $this->app->make(Pay::class);
        });

        $this->app->bind('mp_B2B_pay', function () {
            return $this->app->make(B2BPay::class);
        });

        $this->app->bind('mp_AB_balance', function () {
            return $this->app->make(Balance::class);
        });

        $this->app->bind('mp_reversal', function () {
            return $this->app->make(Reversal::class);
        });

        $this->app->bind('mp_transaction_status', function () {
            return $this->app->make(TransactionStatus::class);
        });
    }
}
