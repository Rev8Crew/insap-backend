<?php

namespace App\Enums\Process;

use App\Enums\EnumTrait;
use App\Modules\Processing\Models\Interpreter\InterpreterGo;
use App\Modules\Processing\Models\Interpreter\InterpreterPhp;
use App\Modules\Processing\Models\Interpreter\InterpreterPython;

class ProcessInterpreter
{
    use EnumTrait;

    public const PHP = InterpreterPhp::class;
    public const PYTHON = InterpreterPython::class;
    public const GO = InterpreterGo::class;

    public static function variants(): array
    {
        return [
            self::PHP,
            self::PYTHON,
            self::GO
        ];
    }
}
