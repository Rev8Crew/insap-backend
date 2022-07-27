<?php

namespace App\Enums;

class Permission
{
    use EnumTrait;

    /** Appliance */
    public const APPLIANCE_CREATE = 'create appliance';
    public const APPLIANCE_EDIT = 'edit appliance';
    public const APPLIANCE_DELETE = 'delete appliance';
    public const APPLIANCE_VIEW = 'view appliance';
    public const APPLIANCE_VIEW_ALL = 'view all appliances';

    /** Process (Importer|exporter) */
    public const PROCESS_CREATE = 'create process';
    public const PROCESS_EDIT = 'edit process';
    public const PROCESS_DELETE = 'delete process';
    public const PROCESS_VIEW = 'view process';
    public const PROCESS_VIEW_ALL = 'view all processes';

    public static function variants(): array
    {
        return [
            self::APPLIANCE_CREATE,
            self::APPLIANCE_EDIT,
            self::APPLIANCE_DELETE,
            self::APPLIANCE_VIEW,
            self::APPLIANCE_VIEW_ALL,

            self::PROCESS_CREATE,
            self::PROCESS_EDIT,
            self::PROCESS_DELETE,
            self::PROCESS_VIEW,
            self::PROCESS_VIEW_ALL
        ];
    }
}
