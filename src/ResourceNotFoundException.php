<?php

namespace Citrium\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResourceNotFoundException extends Exception
{
    protected static string $errorCode = 'resource_not_found';

    public function __construct(ModelNotFoundException $original)
    {
        parent::__construct($original->getMessage());

        $this->status = Response::HTTP_NOT_FOUND;
        $this->title = 'Resource not found';
        $this->detail = 'The resource you have requested could not be found.';
    }
}
