<?php


namespace App\Modules\Importer\Models\ImporterInterpreter;


interface ImporterInterpreter
{
    /**
     * @param string $app
     * @return mixed
     */
    public function execute(string $app);

    /**
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function addArg(string $key, $value);

    public function getAppCommand() : string;
}
