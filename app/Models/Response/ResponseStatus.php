<?php


namespace App\Models\Response;

/**
 * Class ResponseStatuses
 * @package App\Models\Response
 */
class ResponseStatus
{
    /**
     *  Statuses
     */
    const STATUS_OK    = 0;
    const STATUS_ERROR = 1;
    const STATUS_UNKNOWN = 2;

    /**
     *  Default Status
     */
    const DEFAULT_STATUS = self::STATUS_UNKNOWN;

    /**
     * @var int
     */
    private int $status;

    /**
     * @param int $value
     * @return bool
     */
    public function validate(int $value): bool
    {
        return in_array($value, [ self::STATUS_OK, self::STATUS_ERROR, self::STATUS_UNKNOWN ]);
    }

    /**
     * ResponseStatus constructor.
     * @param int $status
     */
    public function __construct(int $status)
    {
        $this->status = $this->validate($status) ? $status : self::DEFAULT_STATUS;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}
