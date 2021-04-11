<?php

namespace App\Models\Response;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Response
 * Unified Response Interface for any response
 *
 * @package App\Models\Response
 */
class Response implements Arrayable
{
    const STATUS_OK    = 0;
    const STATUS_ERROR = 1;

    const ERROR_NOT_FOUND       = 404;
    const ERROR_INTERNAL_SERVER = 500;
    const ERROR_BAD_REQUEST     = 400;
    const ERROR_UNAUTHORIZED    = 401;

    protected int   $status = self::STATUS_OK;

    /**
     *  Response data
     * @var array
     */
    protected array $data = [];

    /**
     * @var bool
     */
    protected bool  $hasErrors = false;

    /**
     *  Include code and error message
     * @var array
     */
    protected array $errors = [];

    /**
     *  request execution time
     * @var int
     */
    protected int $executionTime = 0;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        $this->executionTime = time();
    }

    /**
     *  Return response with given error
     * @param int    $errorCode
     * @param string $message
     *
     * @return $this
     */
    public function withError(int $errorCode, string $message): self
    {
        $this->hasErrors = true;

        $this->errors[] = [
            'code'    => $errorCode,
            'message' => $message,
        ];

        return $this;
    }

    /**
     * @param array $data
     *
     * @return Response
     */
    public function withData(array $data): Response
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return Response
     */
    public function withStatus(int $status): Response
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data'      => $this->data,
            'status'    => $this->status,
            'hasErrors' => $this->hasErrors,
            'errors'    => $this->errors,
            'execution_time' => time() - $this->executionTime
        ];
    }

}