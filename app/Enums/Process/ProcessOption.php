<?php
declare(strict_types=1);

namespace App\Enums\Process;

use App\Enums\EnumTrait;

class ProcessOption
{
    use EnumTrait;

    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const VERSION = 'version';
    public const DATE = 'date';

    public const TRANSFER_DATA_ON_MULTIPLE_IMPORT = 'transfer_data_on_multiply_import';
    public const OVERWRITE_EXISTS_DATA_ON_MULTIPLY_IMPORT = 'overwrite_exists_data_on_multiply_import';

    public const FIELDS = 'fields';

    public static function variants(): array
    {
        return [
            self::NAME,
            self::DESCRIPTION,
            self::VERSION,
            self::DATE,
            self::TRANSFER_DATA_ON_MULTIPLE_IMPORT,
            self::OVERWRITE_EXISTS_DATA_ON_MULTIPLY_IMPORT,
            self::FIELDS
        ];
    }
}
