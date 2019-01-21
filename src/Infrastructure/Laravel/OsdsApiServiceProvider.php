<?php

namespace Osds\Api\Framework\Laravel;

use Illuminate\Support\ServiceProvider;

class OsdsApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        include __DIR__.'/routes.php';
    }
}