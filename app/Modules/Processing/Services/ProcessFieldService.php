<?php
declare(strict_types=1);

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessOption;
use App\Modules\Processing\Models\Dto\ProcessFieldDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Processing\Models\ProcessFieldType;

class ProcessFieldService
{
    public function installFieldsToProcess(Process $process): void
    {
        $fields = $process->getOptionsByKey(ProcessOption::FIELDS);

        $processFields = collect();
        foreach ($fields as $field) {
            $diff = collect(ProcessFieldType::REQUIRED_FIELDS)->diff(collect($field)->keys());

            throw_if(
                $diff->count(),
                new \RuntimeException("[Options][Fields] Require properties missing (". $diff->implode(',') ." ! Field: " . collect($field)->keys()->implode(','))
            );

            $dto = ProcessFieldDto::createFromArray($field);
            $processFields->push(ProcessFieldType::create($dto->toArray()));
        }

        $process->fields()->saveMany($processFields);
    }
}
