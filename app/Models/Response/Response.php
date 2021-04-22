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
    protected ?ResponseStatus $status;

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
        $this->status = new ResponseStatus(ResponseStatus::DEFAULT_STATUS);
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
        if (!$this->hasErrors) {
            $this->hasErrors = true;
            $this->status = new ResponseStatus( ResponseStatus::STATUS_ERROR);
        }

        $this->errors[] = [
            'code'    => (new ResponseErrorStatus($errorCode))->getStatus(),
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
        $this->status = new ResponseStatus( ResponseStatus::STATUS_OK );

        return $this;
    }

    /**
     * @param int $status
     *
     * @return Response
     */
    public function withStatus(int $status): Response
    {
        $this->status = new ResponseStatus($status);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data'      => $this->data,
            'status'    => $this->status->getStatus(),
            'hasErrors' => $this->hasErrors,
            'errors'    => $this->errors,
            'execution_time' => time() - $this->executionTime
        ];
    }

}
