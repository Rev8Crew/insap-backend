<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use Webmozart\Assert\Assert;

class ImporterEventEvent
{
    /** Different event types */

    public const EVENT_PRE_IMPORT = 0;
    public const EVENT_IMPORT = 5;
    public const EVENT_POST_IMPORT_BEFORE_DB = 10;
    public const EVENT_POST_IMPORT_AFTER_DB = 20;

    public const EVENT_PRE_EXPORT = 30;
    public const EVENT_EXPORT = 35;
    public const EVENT_POST_EXPORT = 40;

    /** Array for events */
    public const EVENT_ARRAY = [
        self::EVENT_PRE_IMPORT,
        self::EVENT_IMPORT,
        self::EVENT_POST_IMPORT_BEFORE_DB,
        self::EVENT_POST_IMPORT_AFTER_DB,

        self::EVENT_PRE_EXPORT,
        self::EVENT_EXPORT,
        self::EVENT_POST_EXPORT
    ];

    private int $event;

    /**
     * ImporterEventEvent constructor.
     * @param int $event
     */
    public function __construct(int $event)
    {
        Assert::true($this->validate($event));

        $this->event = $event;
    }

    /**
     * @param int $event
     * @return bool
     */
    protected function validate(int $event): bool
    {
        return in_array($event, self::EVENT_ARRAY);
    }

    /**
     * @return int
     */
    public function getEvent(): int
    {
        return $this->event;
    }
}
