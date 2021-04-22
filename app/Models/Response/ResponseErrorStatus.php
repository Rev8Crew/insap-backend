<?php


namespace App\Models\Response;


class ResponseErrorStatus
{
    const ERROR_NOT_FOUND = 404;
    const ERROR_INTERNAL_SERVER = 500;
    const ERROR_BAD_REQUEST = 400;
    const ERROR_UNAUTHORIZED = 401;

    /**
     *  Default Status
     */
    const DEFAULT_STATUS = self::ERROR_BAD_REQUEST;

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
        return in_array($value, [
            self::ERROR_NOT_FOUND,
            self::ERROR_INTERNAL_SERVER,
            self::ERROR_BAD_REQUEST,
            self::ERROR_UNAUTHORIZED
        ]);
    }

    /**
     * ResponseErrorStatus constructor.
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
