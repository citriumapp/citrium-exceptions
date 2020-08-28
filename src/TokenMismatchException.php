<?php

namespace Citrium\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class TokenMismatchException extends Exception
{
    protected static string $errorCode = 'token_mismatch';
}
