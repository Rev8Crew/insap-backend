<?php


namespace App\Modules\Processing\Models\Interpreter;


class InterpreterPython extends InterpreterAbstract implements InterpreterInterface
{
    protected string $launcher = 'python3';
    protected string $extension = 'py';
}
