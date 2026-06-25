<?php

namespace Huement\StatComm;

use Huement\StatComm\Http\Controllers\CpController;
use Huement\StatComm\Livewire\StatComm;
use Huement\StatComm\Livewire\StatCommWidget;
use Livewire\Livewire;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'statcomm';

    public function bootAddon()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'statcomm');

        // ⚡ ALLOW USERS TO PUBLISH THE VIEWS
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/statcomm'),
        ], 'statcomm-views');

        // ⚡ ASSET PUBLISHING TAG: Copies your CSS file straight into the public web root
        $this->publishes([
            __DIR__ . '/../resources/css' => public_path('vendor/statcomm/css'),
        ], 'statcomm-assets');

        $this->publishes([
            __DIR__ . '/../config/statcomm.php' => config_path('statcomm.php'),
        ], 'statcomm-config');

        $this->publishes([
            __DIR__ . '/../resources/forms' => resource_path('forms'),
        ], 'statcomm-config');

        if (class_exists(Livewire::class)) {
            // ⚡ REGISTER THE FORM + LIST
            Livewire::component('statcomm', StatComm::class);
            // ⚡ REGISTER THE WIDGET
            Livewire::component('statcomm-widget', StatCommWidget::class);
        }

        // 1. REGISTER THE SECURE CP DASHBOARD ROUTE
        $this->registerCpRoutes(function ($router) {
            $router->get('statcomm', [CpController::class, 'index'])->name('statcomm.index');
            $router->post('statcomm/approve/{id}', [CpController::class, 'approve'])->name('statcomm.approve');
            // ⚡ Moderation Protocols
            $router->get('statcomm/edit/{id}', [CpController::class, 'edit'])->name('statcomm.edit');
            $router->post('statcomm/update/{id}', [CpController::class, 'update'])->name('statcomm.update');
            $router->delete('statcomm/delete/{id}', [CpController::class, 'destroy'])->name('statcomm.destroy');
        });

        // 2. EXTEND NAVIGATION USING LOWERCASE HANDLE FOR PERFECT FORMATTING
        Nav::extend(function ($nav) {
            $nav->tools('StatComm')
                ->icon('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-1.074-.765 6 6 0 0 1 1.4-3.578C4.315 15.13 3 13.687 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" /></svg>')
                ->route('statcomm.index');
        });

    }
}
