<?php


namespace Dissonance\Session;

use Dissonance\Core\ServiceProvider;

class NativeProvider extends ServiceProvider
{
    public function register():void
    {
        $this->app->singleton(SessionStorageInterface::class,  function($app) {
            return new SessionStorageNative();
        },'session');
    }
}