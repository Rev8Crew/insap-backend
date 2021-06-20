<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreter;
use Exception;
use JsonMachine\JsonMachine;

/**
 *  Install importer script requirements.
 *  File location : storage/apps/import/{id}/requirements.json
 *
 *  Sections:
 *
 *  Commands to execute
 *   {
 *   "commands" : [
 *   "php composer.char install",
 *   "..."
 *   ]
 *   }
 */

/**
 * Class ImporterInstall
 * @package App\Modules\Importer\Models\Importer
 */
class ImporterEventInstall
{
    public const REQUIREMENTS_FILE_NAME = 'requirements.json';

    private ImporterEvent $importerEvent;
    private ImporterInterpreter $interpreter;

    public function __construct(ImporterEvent $importerEvent, ImporterInterpreter $interpreter)
    {
        $this->importerEvent = $importerEvent;
        $this->interpreter = $interpreter;
    }

    /**
     * @throws Exception
     */
    public function install() {
        $path = $this->importerEvent->getStoragePath();
        $requirementsJsonFile = $path . DIRECTORY_SEPARATOR . self::REQUIREMENTS_FILE_NAME;

        if (file_exists($requirementsJsonFile)) {
            /**
             *  Parse commands from json
             */
            $commands = JsonMachine::fromFile($requirementsJsonFile, '/commands');

            $cdCommand = 'cd ' . $path;
            foreach ($commands  as $command) {
                $exitCode = $this->interpreter->execute("$cdCommand;$command");

                if ($exitCode) {
                    throw new Exception("[Install] Failed exit code from $command [cd $cdCommand]");
                }

            }
        }

    }
}
