<?php

namespace App\Modules\Processing\Factories;

use App\Enums\Process\ProcessType;
use App\Modules\Processing\Services\ImporterService;
use App\Modules\Processing\Services\ProcessServiceInterface;

class ProcessTypeFactory
{
    public function create(ProcessType $processType): ProcessServiceInterface
    {
        if ($processType->is(ProcessType::EXPORTER)) {
            return app(ImporterService::class);
        }

        return app(ImporterService::class);
    }
}
