<?php

namespace Citrium\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class NotAuthorizedException extends Exception
{
    protected static string $errorCode = 'not_authorized';

    protected int $status = Response::HTTP_UNAUTHORIZED;

    protected string $title = 'Unauthorized';

    protected string $detail = 'You are not authorized to access this resource.';
}
