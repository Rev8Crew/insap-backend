<?php

namespace App\Models\Common;

use App\Enums\ResponseStatus;
use App\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Carbon;
use Laravel\Telescope\Telescope;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

/**
 * Class Response
 * Unified Response Interface for any response
 *
 * @package App\Models\Response
 */
class Response implements Arrayable
{
    use Makeable;

    protected ResponseStatus $status;

    /**
     *  Response data
     * @var mixed
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected bool $hasErrors = false;

    /**
     *  Include code and error message
     * @var array
     */
    protected array $errors = [];

    /**
     * @var array
     */
    protected array $validationErrors = [];

    /**
     *  request execution time
     * @var int
     */
    protected int $executionTime = 0;

    /**
     * Memory usage.
     *
     * @var float|int
     */
    protected $memoryUsageStart;

    /** @var float|int */
    protected $memoryUsageEnd;

    /** @var float|int */
    protected $memoryPeakUsage;

    /** @var Carbon */
    protected $date;

    /**
     * Response constructor.
     */
    public function __construct()
    {
        // Sets memory usage
        $this->memoryUsageStart = memory_get_usage();
        $this->executionTime = microtime(true);

        $this->status = ResponseStatus::create(ResponseStatus::UNKNOWN);
        $this->date = Carbon::now();
    }

    /**
     *  Return response with given error
     * @param int $errorCode
     * @param string $message
     *
     * @return $this
     */
    public function withError(int $errorCode, string $message): self
    {

        $this->hasErrors = true;
        $this->status = ResponseStatus::create(ResponseStatus::ERROR);

        $this->errors[] = [
            'code' => $errorCode,
            'message' => $message,
        ];

        return $this;
    }

    /**
     * @param Throwable $throwable
     * @return $this
     */
    public function catch(Throwable $throwable): Response
    {
        Telescope::catch($throwable);

        $code = (int)$throwable->getCode() ?: SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR;
        return $this->withError($code, $throwable->getMessage());
    }

    /**
     * @param mixed $data
     *
     * @return Response
     */
    public function withData($data): Response
    {
        $this->data = $data;
        $this->status = ResponseStatus::create(ResponseStatus::SUCCESS);

        return $this;
    }

    /**
     * @param int $status
     *
     * @return Response
     */
    public function withStatus(int $status): Response
    {
        $this->status = ResponseStatus::create($status);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    private function withMessage(string $message = ''): Response
    {
        if (!$message) {
            return $this;
        }
        $this->data['message'] = $message;

        return $this;
    }

    /**
     * @param string $message
     * @return Response
     */
    public function success(string $message = ''): Response
    {
        return $this->withStatus(ResponseStatus::SUCCESS)->withMessage($message);
    }

    public function validation(Validator $validator): Response
    {
        if ($validator->fails()) {
            $this->withError(SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY, "Validation Errors");
            $this->withStatus(ResponseStatus::ERROR);

            foreach ($validator->errors()->getMessages() as $field => $error) {
                $this->validationErrors[$field] = $error;
            }
        }
        return $this;
    }

    public function file(string $fileName): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return response()->download($fileName);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'status' => $this->status->getValue(),
            'hasErrors' => $this->hasErrors,
            'errors' => $this->errors,
            'validationErrors' => $this->validationErrors,
            'execution_time' => microtime(true) - $this->executionTime,
            'memory_start_usage' => $this->memoryUsageStart,
            'memory_peak_usage' => $this->memoryPeakUsage,
            'memory_end_usage' => $this->memoryUsageEnd,
            'date' => $this->date->format('Y-m-d H:i:s'),
        ];
    }

}
