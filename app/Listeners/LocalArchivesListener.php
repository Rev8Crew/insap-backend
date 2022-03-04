<?php

namespace App\Listeners;

use App\Events\PreImportEvent;

class LocalArchivesListener
{
    public function __construct()
    {
        //
    }

    public function handle(PreImportEvent $event): void
    {

    }

    public function failed(PreImportEvent $event, $exception): void
    {

    }
}
