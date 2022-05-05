<?php


namespace App\Modules\Processing\Models\Interpreter;


interface InterpreterInterface
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
