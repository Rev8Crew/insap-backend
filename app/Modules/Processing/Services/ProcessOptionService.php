<?php
declare(strict_types=1);

namespace App\Modules\Processing\Services;

use App\Enums\Process\ProcessOption;
use App\Modules\Processing\Models\Process;
use Illuminate\Support\Collection;

class ProcessOptionService
{
    public function getOptionFilePath(Process $process): string
    {
        return $process->getStoragePath() . DIRECTORY_SEPARATOR . Process::OPTIONS_FILE_NAME;
    }

    public function isOptionsFileExists(Process $process): bool
    {
        return \File::exists($this->getOptionFilePath($process));
    }

    public function getAllOptions(Process $process): Collection
    {
        return collect(json_decode(file_get_contents($this->getOptionFilePath($process)), true, 512, JSON_THROW_ON_ERROR));
    }

    public function getOptionsDiff(Process $process): Collection
    {
        $options = $this->getAllOptions($process);
        $requiredOptionsDiff = collect(ProcessOption::variants())->diff($options->keys());

        return $requiredOptionsDiff;
    }

    public function isAllRequiredOptionsExists(Process $process): bool
    {
        return (bool)$this->getOptionsDiff($process);
    }
}
