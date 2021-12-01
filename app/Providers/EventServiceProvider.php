<?php

namespace App\Providers;

use App\Events\MailSendEvent;
use App\Listeners\MailSendListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ],
        MailSendEvent::class=>[
            MailSendListener::class
        ]
    ];
}
