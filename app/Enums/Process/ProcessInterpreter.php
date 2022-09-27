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

    public static function labels(): array
    {
        return [
            self::PHP => 'PHP',
            self::PYTHON => 'Python',
            self::GO => 'Golang',
        ];
    }

    public static function labelsArray(): array
    {
        return [
            [
                'text' => 'PHP',
                'value' => self::PHP
            ],
            [
                'text' => 'Python',
                'value' => self::PYTHON
            ],
            [
                'text' => 'Golang',
                'value' => self::GO
            ],
        ];
    }
}
