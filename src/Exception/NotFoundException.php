<?php

namespace App\CustomerPortal\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Not Found')
    {
        parent::__construct($message);
    }
}
