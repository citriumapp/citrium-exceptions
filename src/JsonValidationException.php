<?php

namespace Citrium\Exceptions;

use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class JsonValidationException extends Exception
{
    public function __construct(ValidationException $exception)
    {
        parent::__construct($exception->getMessage());

        $this->status = Response::HTTP_UNPROCESSABLE_ENTITY;
        $this->detail = 'Please review the following errors.';
        $this->errors = $exception->errors();
    }
}
