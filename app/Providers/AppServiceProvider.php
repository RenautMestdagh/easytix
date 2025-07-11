<?php

namespace App\Providers;

use App\Livewire\Backend\ImprovedDropzone;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Livewire::component('improved-dropzone', ImprovedDropzone::class);
    }
}
