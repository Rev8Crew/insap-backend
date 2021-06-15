<?php


namespace App\Modules\Importer\Models\ImporterInterpreter;


class ImporterInterpreterGo extends ImporterInterpreterAbstract implements ImporterInterpreter
{
    protected string $launcher = 'go';
    protected string $extension = 'go';

    protected string $action = 'run';

    public function getAppCommand(): string
    {
        return $this->launcher . " " . $this->action . " " . $this->mainExecFile . "." . $this->extension;
    }
}
