<?php

namespace Fahimhossainmunna\QuickSetup;

use Illuminate\Support\ServiceProvider;
use Fahimhossainmunna\QuickSetup\Console\QuickSetupCommand;

class QuickSetupServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            QuickSetupCommand::class,
        ]);
    }

    public function boot()
    {
        // Optional: if you want to publish files later
    }
}
