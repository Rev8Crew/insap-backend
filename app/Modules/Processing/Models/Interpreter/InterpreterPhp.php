<?php


namespace App\Modules\Processing\Models\Interpreter;


class InterpreterPhp extends InterpreterAbstract
{
    protected string $launcher = 'php';
    protected string $extension = 'php';
}
