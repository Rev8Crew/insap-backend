<?php


namespace App\Modules\Importer\Models\ImporterInterpreter;


class ImporterInterpreterPhp extends ImporterInterpreterAbstract implements ImporterInterpreter
{
    protected string $launcher = 'php';
    protected string $extension = 'php';
}
