<?php


namespace App\Modules\Processing\Models\Interpreter;


class InterpreterGo extends InterpreterAbstract implements InterpreterInterface
{
    protected string $launcher = 'go';
    protected string $extension = 'go';

    protected string $action = 'run';

    public function getAppCommand(): string
    {
        return $this->launcher . " " . $this->action . " " . $this->mainExecFile . "." . $this->extension;
    }
}
