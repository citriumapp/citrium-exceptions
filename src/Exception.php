<?php

namespace Citrium\Exceptions;

use Throwable;
use Ramsey\Uuid\Uuid;
use Exception as BaseException;
use Illuminate\Contracts\Support\Arrayable;

class Exception extends BaseException implements Arrayable
{
    const GENERIC_ERROR = 'An error has occurred. Please contact support if this continues to happen.';

    /**
     * A unique identifier for this particular occurrence of the problem
     */
    private string $id;

    /**
     * The HTTP status code applicable to this problem
     */
    protected int $status = 400;

    /**
     * An application-specific error code
     */
    protected static string $errorCode = 'unknown_error';

    /**
     * A short, human-readable summary of the problem that SHOULD NOT change from occurrence to occurrence
     * of the problem
     */
    protected string $title = 'An error has occurred';

    /**
     * A human-readable explanation specific to this occurrence of the problem
     */
    protected string $detail = self::GENERIC_ERROR;

    /**
     * A developer specific error message to help debug the issue
     */
    protected string $technicalError = '';

    /**
     * An array of validation errors. Should be keyed by the field name, with the value being an
     * array of messages
     */
    protected array $errors = [];

    /**
     * A key-value based collection of additional meta data
     */
    protected array $meta = [];

    /**
     * The service which originally threw this exception
     */
    protected static string $service = 'not-defined';

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->detail, $code, $previous);

        $this->technicalError = $message;
        $this->id             = Uuid::uuid4()->toString();
    }

    public static function setService(string $name): void
    {
        self::$service = $name;
    }

    public static function make(...$arguments): Exception
    {
        return new static(...$arguments);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function errorCode(): string
    {
        return static::$errorCode;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function details(): string
    {
        return $this->getMessage();
    }

    public function technicalDetails(): string
    {
        return $this->technicalError;
    }

    public function service(): string
    {
        return static::$service;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function meta()
    {
        return $this->meta;
    }

    public function reportable(): bool
    {
        return true;
    }

    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'status'  => $this->status(),
            'code'    => static::$errorCode,
            'title'   => $this->title,
            'details' => $this->getMessage(),
            'errors'  => $this->errors(),
            'meta'    => $this->formatMeta(),
        ];
    }

    protected function formatMeta(): array
    {
        if (env('APP_ENV', 'production') === 'production') {
            return $this->meta();
        }

        return array_merge($this->meta(), [
            'service'           => $this->service(),
            'technical_details' => $this->technicalDetails(),
        ]);
    }
}
