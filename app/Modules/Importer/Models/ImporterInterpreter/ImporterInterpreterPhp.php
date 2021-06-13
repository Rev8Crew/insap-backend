<?php


namespace App\Modules\Importer\Models\ImporterInterpreter;


use mikehaertl\shellcommand\Command;

class ImporterInterpreterPhp implements ImporterInterpreter
{
    private Command $command;
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
            throw new \Exception("Error while execute PHP command '$app' with message [$exitCode] : '$error'");
        }

        return $exitCode;
    }

    /**
     * @param string $key
     * @param $value
     */
    public function addArg(string $key, $value) {
        $this->command->addArg($key, $value, true);
    }

    public function getAppCommand(): string
    {
        return 'php app.php';
    }
}
