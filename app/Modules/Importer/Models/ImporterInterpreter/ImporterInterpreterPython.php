<?php


namespace App\Modules\Importer\Models\ImporterInterpreter;


class ImporterInterpreterPython extends ImporterInterpreterAbstract implements ImporterInterpreter
{
    protected string $launcher = 'python3';
    protected string $extension = 'py';
}
