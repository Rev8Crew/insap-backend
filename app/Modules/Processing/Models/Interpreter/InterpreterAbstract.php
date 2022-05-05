<?php


namespace App\Modules\Processing\Models\Interpreter;


use mikehaertl\shellcommand\Command;

abstract class InterpreterAbstract implements InterpreterInterface
{
    protected Command $command;

    protected string $mainExecFile = 'app';
    protected string $launcher = '';
    protected string $extension = '';

    public function __construct()
    {
        $this->command = new Command();
    }

    public function execute(string $app)
    {
        $this->command->setCommand($app);

        $success = $this->command->execute();
        $exitCode = $this->command->getExitCode();

        if (!$success) {
            $error = $this->command->getError();
            throw new \Exception("[Interpreter] Error while execute {$this->launcher} command '$app' with message [$exitCode] : '$error'");
        }

        return $exitCode;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addArg(string $key, $value)
    {
        $this->command->addArg($key, $value, true);
    }

    public function getAppCommand(): string
    {
        return $this->launcher . " " . $this->mainExecFile . "." . $this->extension;
    }
}
