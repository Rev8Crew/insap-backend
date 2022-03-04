<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use Webmozart\Assert\Assert;

class ImporterEventEvent
{
    /** Different event types */
    public const EVENT_IMPORT = 100;
    public const EVENT_EXPORT = 200;

    /** Array for events */
    public const EVENT_ARRAY = [
        self::EVENT_IMPORT,
        self::EVENT_EXPORT,
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
