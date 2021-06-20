<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterGo;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterPhp;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreterPython;
use Webmozart\Assert\Assert;

class ImporterEventInterpreter
{
    private string $interpreter;

    /**
     * ImporterEventInterpreter constructor.
     * @param string $interpreter
     */
    public function __construct(string $interpreter)
    {
        Assert::true($this->validate($interpreter));

        $this->interpreter = $interpreter;
    }

    /**
     * @param string $interpreter
     * @return bool
     */
    public function validate(string $interpreter): bool
    {
        return in_array($interpreter, [
            ImporterInterpreterPhp::class,
            ImporterInterpreterPython::class,
            ImporterInterpreterGo::class
        ]);
    }

    /**
     * @return string
     */
    public function getInterpreter(): string
    {
        return $this->interpreter;
    }
}
