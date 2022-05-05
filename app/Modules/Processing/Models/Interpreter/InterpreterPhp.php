<?php


namespace App\Modules\Processing\Models\Interpreter;


class InterpreterPhp extends InterpreterAbstract implements InterpreterInterface
{
    protected string $launcher = 'php';
    protected string $extension = 'php';
}
